<?php

namespace App\Crypto;

use Illuminate\Encryption\Encrypter;
use phpseclib\Crypt\RSA;
use phpseclib\Crypt\AES;
use phpseclib\Crypt\Random;

class Keys
{
    /**
     * Generate a new keypair.
     *
     * @return array
     */
    public static function generate()
    {
        $rsa = new RSA();
        return $rsa->createKey();
    }

    /**
     * Encrypt data using an RSA public key, so it can
     * only be decrypted with the corresponding
     * private key.
     *
     * @param string $data
     * @param string $key public key
     * @return string
     */
    public static function rsaEncrypt($data, $key)
    {
        $rsa = new RSA();
        $rsa->loadKey($key);
        return base64_encode($rsa->encrypt($data));
    }

    /**
     * Decrypt data using a matching RSA private key to
     * the public key that encrypted it.
     *
     * @param string $data
     * @param string $key private key
     * @return string
     */
    public static function rsaDecrypt($data, $key)
    {
        $rsa = new RSA();
        $rsa->loadKey($key);
        return $rsa->decrypt(base64_decode($data));
    }

    /**
     * Encrypt data using the AES 256 cipher.
     *
     * @param string $data
     * @param string $key (or password)
     * @return string
     */
    public static function encrypt($data, $key)
    {
        $aes = new AES();
        $aes->setPassword($key);
        // TODO: implement IV
        // $aes->setIV(Random::string($aes->getBlockLength() >> 3));
        return base64_encode($aes->encrypt($data));
    }

    /**
     * Decrypt data using the AES 256 cipher.
     *
     * @param string $data
     * @param string $key (or password)
     * @return string
     */
    public static function decrypt($data, $key)
    {
        $aes = new AES();
        $aes->setPassword($key);
        // $aes->setIV(Random::string($aes->getBlockLength() >> 3));
        return $aes->decrypt(base64_decode($data));
    }

    /**
     * Generate key to use for AES ciphers, and document keys
     * down the line.
     *
     * @param int $length
     * @return string
     */
    public static function generateKey($length = 128)
    {
        return Random::string($length);
    }
}