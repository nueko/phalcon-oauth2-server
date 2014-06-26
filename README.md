Phalcon-Oauth2
==============

Phalcon wrapper for Oauth2 https://github.com/php-loep/oauth2-server

please see:
> https://github.com/php-loep/oauth2-server/wiki/Developing-an-OAuth-2.0-authorization-server

> https://github.com/php-loep/oauth2-server/wiki/Securing-your-API-with-OAuth-2.0


Install
-------
```bash
curl -sS getcomposer.org/installer | php
php composer.phar require "league/oauth2-server":"3.*" -vvv
```
Server Example
--------------
```php
# add composer autoload on public/index.php, loader.php or wherever you want
require __DIR__ "/../vendor/autoload.php"

# Config DB

return new \Phalcon\Config([
    'database'    => [
        'oauth' => [
            'adapter'  => 'Mysql',
            'host'     => 'localhost',
            'port'     => 3306,
            'username' => 'root',
            'password' => 'pwd',
            'dbname'   => 'oauth2',
        ],
        'app'   => [
            'adapter'  => 'Mysql',
            'host'     => 'localhost',
            'port'     => 3306,
            'username' => 'root',
            'password' => 'pwd',
            'dbname'   => 'project',
        ],
    ],
    # ...
]);

# Register The Lib to the loader
$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
    'Sum' => '/Path/To/Lib/Dir/',
    # ...
])->register();

# set as service
$app->setService('oauth', function() use ($config) {
   $oauthdb = new Phalcon\Db\Adapter\Pdo\Mysql($config->database->oauth->toArray());

    $server = new \League\OAuth2\Server\Authorization(
        new \Sum\Oauth2\Server\Storage\Pdo\Mysql\Client($oauthdb),
        new \Sum\Oauth2\Server\Storage\Pdo\Mysql\Session($oauthdb),
        new \Sum\Oauth2\Server\Storage\Pdo\Mysql\Scope($oauthdb)
    );

    # Not required as it called directly from original code
    # $request = new \League\OAuth2\Server\Util\Request();
    
    # add these 2 lines code if you want to use my own Request otherwise comment it
    $request = new \Sum\Oauth2\Server\Storage\Pdo\Mysql\Request(); 
    $server->setRequest($request);

    $server->setAccessTokenTTL(86400);
    $server->addGrantType(new League\OAuth2\Server\Grant\ClientCredentials());
    return $server;
});

# should be post, but it is only test 
$app->get('/access', function () use ($app) {
	try {
	    $params = $app->oauth->getParam(array('client_id', 'client_secret'));
	    echo json_encode(
	    	$app->oauth
	    		->getGrantType('client_credentials')
	    		->completeFlow($params)
	    );
	} catch (\League\OAuth2\Server\Exception\ClientException $e) {
	    echo $e->getTraceAsString();
	} catch (\Exception $e) {
	    echo $e->getTraceAsString();
	}
});
```
Test
----
```bash
curl "localhost/phalcon/public/access?client_id=what&client_secret=ever"
```
Response
--------
```js
{
	access_token: "KKiGP5YURoR41k2iYy82Dp4rFyOxrhJUp9KcdjuK",
	token_type: "Bearer",
	expires: "1397626655",
	expires_in: 86400
}
```

Resource Example
----------------
```php
$di['resource'] = function () use ($config) {
    $oauthdb = new DbAdapter(
        $config->database->oauth->toArray()
    );
    $resource = new League\OAuth2\Server\Resource(
        new \Sum\Oauth2\Server\Storage\Pdo\Mysql\Session($oauthdb)
    );
    ##only exist on my develop fork
    #$resource->setMsg([
    #    'invalidToken' => 'Token tidak benar',
    #    'missingToken' => 'Token tidak ditemukan'
    #]);
    $resource->setRequest(new \Sum\Oauth2\Server\Storage\Pdo\Mysql\Request());

    return $resource;
};

$app->get('/bill', function () use ($app) {
    try {
        $app->resource->setTokenKey('token');
        $app->resource->isValid();
        return $app->response
            ->setContentType('application/json')
            ->setJsonContent([
	            'error'   => False,
	            'status'  => "OK",
	            'message' => "Welcome"
        ]);
    } catch (League\OAuth2\Server\Exception\InvalidAccessTokenException $e) {
        $body['meta'] = [
            'error'   => TRUE,
            'status'  => 403,
            'message' => $e->getMessage()
        ];
        return $app->response
            ->setStatusCode(403, 'Forbidden')
            ->setContentType('application/json')
            ->setJsonContent([
	            'error'   => TRUE,
	            'status'  => 403,
	            'message' => $e->getMessage()
		]);
    }
});
```
Resource Test
-------------
```bash
curl "localhost/phalcon/public/bill?token=KKiGP5YURoR41k2iYy82Dp4rFyOxrhJUp9KcdjuK"
```

