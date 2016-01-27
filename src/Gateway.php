<?php

namespace Phalcon\OAuth2\Server;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\AccessDeniedException;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\TokenType\TokenTypeInterface;
use League\OAuth2\Server\Util\RedirectUri;
use Phalcon\OAuth2\Server\Storage\Phql\AccessTokenStorage;
use Phalcon\OAuth2\Server\Storage\Phql\ClientStorage;
use Phalcon\OAuth2\Server\Storage\Phql\ScopeStorage;
use Phalcon\OAuth2\Server\Storage\Phql\SessionStorage;
use Phalcon\Text;

class Gateway extends Component
{

    /**
     * The authorization server
     *
     * @var AuthorizationServer
     */
    protected $authorization;

    /**
     * The resource server.
     *
     * @var ResourceServer
     */
    protected $resource;

    /**
     * The auth code request parameters.
     *
     * @var array
     */
    protected $authCodeRequestParams;

    /**
     * The redirect uri generator.
     *
     * @var bool|null
     */
    protected $redirectUriGenerator = null;

    /**
     * Create a new Authorize instance.
     *
     * @param AuthorizationServer $authorization
     * @param ResourceServer $resource
     */
    public function __construct(AuthorizationServer $authorization, ResourceServer $resource)
    {
        $this->authorization = $authorization;
        $this->resource = $resource;
        $this->authCodeRequestParams = [];
    }


    public static function phqlStorage(array $grants = ['client_credentials'], $refreshToken = false)
    {
        $session = new SessionStorage();
        $accessToken = new AccessTokenStorage();
        $client = new ClientStorage();
        $scope = new ScopeStorage();
        $authorization = new AuthorizationServer;

        $authorization->setSessionStorage($session);
        $authorization->setAccessTokenStorage($accessToken);
        $authorization->setClientStorage($client);
        $authorization->setScopeStorage($scope);

        foreach ($grants as $grant) {
            $class = 'League\OAuth2\Server\Grant\\' . Text::camelize($grant) . "Grant";
            $authorization->addGrantType(new $class);
        }

        if ($refreshToken) {
            $authorization->addGrantType(new RefreshTokenGrant());
        }

        $resource = new ResourceServer($session, $accessToken, $client, $scope);

        return new self($authorization, $resource);
    }

    /**
     * Get the issuer.
     *
     * @return AuthorizationServer
     */
    public function getAuthorization()
    {
        return $this->authorization;
    }

    /**
     * Issue an access token if the request parameters are valid.
     *
     * @return array a response object for the protocol in use
     */
    public function issueAccessToken()
    {
        return $this->authorization->issueAccessToken();
    }

    /**
     * Get the Auth Code request parameters.
     *
     * @return array
     */
    public function getAuthCodeRequestParams()
    {
        return $this->authCodeRequestParams;
    }

    /**
     * Check the validity of the auth code request.
     *
     * @return null a response appropriate for the protocol in use
     */
    public function checkAuthCodeRequest()
    {
        $this->authCodeRequestParams = $this->authorization->getGrantType('authorization_code')->checkAuthorizeParams();
    }

    /**
     * Issue an auth code.
     *
     * @param string $ownerType the auth code owner type
     * @param string $ownerId the auth code owner id
     * @param array $params additional parameters to merge
     *
     * @return string the auth code redirect url
     */
    public function issueAuthCode($ownerType, $ownerId, $params = [])
    {
        $params = array_merge($this->authCodeRequestParams, $params);

        return $this->authorization->getGrantType('authorization_code')->newAuthorizeRequest($ownerType, $ownerId,
            $params);
    }

    /**
     * Generate a redirect uri when the auth code request is denied by the user.
     *
     * @return string a correctly formed url to redirect back to
     */
    public function authCodeRequestDeniedRedirectUri()
    {
        $error = new AccessDeniedException();

        return $this->getRedirectUriGenerator()->make($this->getAuthCodeRequestParam('redirect_uri'), [
                'error'             => $error->errorType,
                'error_description' => $error->getMessage(),
            ]
        );
    }

    /**
     * Get the RedirectUri generator instance.
     *
     * @return RedirectUri
     */
    public function getRedirectUriGenerator()
    {
        if (is_null($this->redirectUriGenerator)) {
            $this->redirectUriGenerator = new RedirectUri();
        }

        return $this->redirectUriGenerator;
    }

    /**
     * Set the RedirectUri generator instance.
     *
     * @param $redirectUri
     */
    public function setRedirectUriGenerator($redirectUri)
    {
        $this->redirectUriGenerator = $redirectUri;
    }

    /**
     * Get a single parameter from the auth code request parameters.
     *
     * @param $key
     * @param null $default
     *
     * @return mixed
     */
    public function getAuthCodeRequestParam($key, $default = null)
    {
        if (array_key_exists($key, $this->authCodeRequestParams)) {
            return $this->authCodeRequestParams[$key];
        }

        return $default;
    }

    /**
     * Validate a request with an access token in it.
     *
     * @param bool $httpHeadersOnly whether or not to check only the http headers of the request
     * @param string|null $accessToken an access token to validate
     *
     * @return mixed
     */
    public function validateAccessToken($httpHeadersOnly = false, $accessToken = null)
    {
        return $this->resource->isValidRequest($httpHeadersOnly, $accessToken);
    }

    /**
     * get the scopes associated with the current request.
     *
     * @return array
     */
    public function getScopes()
    {
        return $this->getAccessToken()->getScopes();
    }

    /**
     * Get the current access token for the session.
     *
     * If the session does not have an active access token, an exception will be thrown.
     *
     * @throws AccessDeniedException
     *
     * @return \League\OAuth2\Server\Entity\AccessTokenEntity
     */
    public function getAccessToken()
    {
        $accessToken = $this->getResource()->getAccessToken();

        if (is_null($accessToken)) {
            throw new AccessDeniedException;
        }

        return $accessToken;
    }

    /**
     * Get the checker.
     *
     * @return ResourceServer
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Check if the current request has all the scopes passed.
     *
     * @param string|array $scope the scope(s) to check for existence
     *
     * @return bool
     */
    public function hasScope($scope)
    {
        if (is_array($scope)) {
            foreach ($scope as $s) {
                if ($this->hasScope($s) === false) {
                    return false;
                }
            }

            return true;
        }

        return $this->getAccessToken()->hasScope($scope);
    }

    /**
     * Get the resource owner ID of the current request.
     *
     * @return string
     */
    public function getResourceOwnerId()
    {
        return $this->getAccessToken()->getSession()->getOwnerId();
    }

    /**
     * Get the resource owner type of the current request (client or user).
     *
     * @return string
     */
    public function getResourceOwnerType()
    {
        return $this->getAccessToken()->getSession()->getOwnerType();
    }

    /**
     * Get the client id of the current request.
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->resource->getAccessToken()->getSession()->getClient()->getId();
    }

    /**
     * Set the request to use on the issuer and checker.
     *
     * @param $request
     */
    public function setRequest($request)
    {
        $this->authorization->setRequest($request);
        $this->resource->setRequest($request);
    }

    /**
     * Set the token type to use.
     *
     * @param \League\OAuth2\Server\TokenType\TokenTypeInterface $tokenType
     */
    public function setTokenType(TokenTypeInterface $tokenType)
    {
        $this->authorization->setTokenType($tokenType);
        $this->resource->setTokenType($tokenType);
    }

}