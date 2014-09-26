<?php
/**
 * Created by PhpStorm.
 * User: elf
 * Date: 9/26/14
 * Time: 9:11 PM
 */

namespace Sumeko\Phalcon\Oauth2\Server;


use League\OAuth2\Server\Storage\SessionInterface;

class Resource extends \League\OAuth2\Server\Resource
{

    public function __construct(SessionInterface $session)
    {
        parent::$exceptionMessages = [
            'invalid_request'    => "Permintaan tidak menyertakan parameter yang diperlukan, menyertakan isi parameter tidak benar, menyertakan parameter lebih dari satu kali, atau berformat salah. periksa parameter '%s'.",
            'invalid_token'      => 'The access token provided is expired, revoked, malformed, or invalid for other reasons.',
            'insufficient_scope' => 'Permintaan membutuhkan hak yang lebih tinggi daripada yang diberikan oleh token akses. Lingkup yang dibutuhkan adalah: %s',
            'missing_token'      => "Permintaan tidak menemukan token akses baik di 'header Authorization' ataupun parameter permintaan '%s'.",
        ];
        parent::__construct($session);
    }
} 