<?php
/**
 * Created by PhpStorm.
 * User: elf
 * Date: 9/26/14
 * Time: 6:48 PM
 */

namespace Sumeko\Phalcon\Oauth2\Server;


use League\OAuth2\Server\Exception\ClientException;
use League\OAuth2\Server\Storage\ClientInterface;
use League\OAuth2\Server\Storage\ScopeInterface;
use League\OAuth2\Server\Storage\SessionInterface;

class Authorization extends \League\OAuth2\Server\Authorization
{
    public function __construct(ClientInterface $client, SessionInterface $session, ScopeInterface $scope)
    {
        parent::$exceptionMessages = [
            'invalid_request'           =>  "Permintaan tidak menyertakan parameter yang diperlukan, menyertakan isi parameter tidak benar, menyertakan parameter lebih dari satu kali, atau berformat salah. periksa parameter '%s'.",
            'unauthorized_client'       =>  "Klien tidak berwenang meminta token akses dengan metode ini.",
            'access_denied'             =>  'Pemilik sumber daya atau pelaksana otorisasi menolak permintaan tersebut.',
            'unsupported_response_type' =>  'Server otorisasi tidak mendukung metode ini untuk memperoleh token akses.',
            'invalid_scope'             =>  "Ruang lingkup yang diminta tidak valid, tidak diketahui, atau berformat salah. Periksa lingkup '%s'.",
            'server_error'              =>  "Server otorisasi mengalami kondisi tak terduga yang mencegah permintaan terpenuhi.",
            'temporarily_unavailable'   =>  "Server otorisasi sementara ini tidak dapat menangani permintaan dikarenakan kelebihan beban atau dalam proses pemeliharaan.",
            'unsupported_grant_type'    =>  "Jenis pemberian hak '%s' tidak didukung oleh server otorisasi",
            'invalid_client'            =>  'Otentikasi klien gagal',
            'invalid_grant'             =>  "Pemberian otorisasi yang disediakan tidak valid, kadaluarsa, dicabut, tidak cocok dengan URI pengalihan yg digunakan dalam permintaan otorisasi, atau diterbitkan untuk klien lain. Periksa parameter '%s'.",
            'invalid_credentials'       =>  'Kredensial pengguna yang tidak benar.',
            'invalid_refresh'           =>  'Refresh token tidak benar.',
        ];
        parent::__construct($client, $session, $scope);

    }

    public function getStatusCode($errorType)
    {
        return parent::$exceptionHttpStatusCodes[$errorType];
    }

    /**
     * Issue an access token
     *
     * @param  array $inputParams Optional array of parsed $_POST keys
     * @throws ClientException
     * @throws \League\OAuth2\Server\Exception\InvalidGrantTypeException
     * @return array             Authorise request parameters
     */
    public function issueAccessToken($inputParams = [])
    {
        if($this->getRequest()->server('REQUEST_METHOD') !== 'POST') {
            throw new ClientException(sprintf(self::$exceptionMessages['unauthorized_client'], 'grant_type'), 0);
        }
        $grantType = $this->getParam('grant_type', 'post', $inputParams);

        if (is_null($grantType)) {
            throw new ClientException(sprintf(self::$exceptionMessages['invalid_request'], 'grant_type'), 0);
        }

        // Ensure grant type is one that is recognised and is enabled
        if ( ! in_array($grantType, array_keys($this->grantTypes))) {
            throw new ClientException(sprintf(self::$exceptionMessages['unsupported_grant_type'], $grantType), 7);
        }

        // Complete the flow
        return $this->getGrantType($grantType)->completeFlow($inputParams);
    }


} 