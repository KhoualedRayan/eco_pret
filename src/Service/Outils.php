<?php

namespace App\Service;

class Outils
{
    function crypterMessage($message)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $messageCrypte = openssl_encrypt($message, 'aes-256-cbc', $_ENV['APP_CLE_CRYPTAGE'], 0, $iv);
        return base64_encode($iv . $messageCrypte);
    }

    function decrypterMessage($messageCrypte)
    {
        $messageCrypte = base64_decode($messageCrypte);
        $iv = substr($messageCrypte, 0, openssl_cipher_iv_length('aes-256-cbc'));
        $messageCrypte = substr($messageCrypte, openssl_cipher_iv_length('aes-256-cbc'));
        return openssl_decrypt($messageCrypte, 'aes-256-cbc', $_ENV['APP_CLE_CRYPTAGE'], 0, $iv);
    }
}

?>
