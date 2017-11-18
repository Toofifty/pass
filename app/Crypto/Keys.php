<?php

namespace App\Crypto;

use Illuminate\Encryption\Encrypter;
use phpseclib\Crypt\RSA;

class Keys
{
    protected static function config()
    {
    	return [
	        'digest_alg' => 'sha512',
	        'private_key_bits' => 4096,
	        'private_key_type' => OPENSSL_KEYTYPE_RSA
	    ];
	}

    public static function generate()
    {
        // return openssl_pkey_new(self::config());
        $rsa = new RSA();
        return $rsa->createKey();
    }

    public static function private($keys)
    {
    	openssl_pkey_export($keys, $out);
    	return $out;
    }

    public static function privateEncrypt($data, $key)
    {
        openssl_private_encrypt($data, $out, $key);
        return $out;
    }

    public static function privateDecrypt($data, $key)
    {
        if (!openssl_private_decrypt($data, $out, $key)) {
            return openssl_error_string();
        }
        return $out;
    }

    public static function public($keys)
    {
    	return openssl_pkey_get_details($keys)['key'];
    }

    public static function rsaEncrypt($data, $key)
    {
        $rsa = new RSA();
        $rsa->loadKey($key);
        return base64_encode($rsa->encrypt($data));
    }

    public static function rsaDecrypt($data, $key)
    {
        $rsa = new RSA();
        $rsa->loadKey($key);
        return $rsa->decrypt(base64_decode($data));
    }

    public static function publicDecrypt($data, $key)
    {
        openssl_public_decrypt(base64_decode($data), $out, $key);
        return $out;
    }

    public static function encrypt($data, $key)
    {
        $method = 'AES-256-CBC';
        $key = hash('sha256', $key, true);
        $iv = openssl_random_pseudo_bytes(16);

        $cipher = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
        $hash = hash_hmac('sha256', $cipher, $key, true);

        return base64_encode($iv . $hash . $cipher);
    }

    public static function decrypt($data, $key)
    {
        $data = base64_decode($data);

        $method = 'AES-256-CBC';
        $iv = substr($data, 0, 16);
        $hash = substr($data, 16, 32);
        $cipher = substr($data, 48);
        $key = hash('sha256', $key, true);

        if (hash_hmac('sha256', $cipher, $key, true) !== $hash) {
            return null;
        }

        return openssl_decrypt($cipher, $method, $key, OPENSSL_RAW_DATA, $iv);
    }

    public static function generateKey($length = 128)
    {
        return base64_encode(openssl_random_pseudo_bytes($length));
    }

    public static function encryptedPrivate($keys, $password)
    {
    	return self::encrypt(self::private($keys), $password);
    }
}