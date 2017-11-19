<?php

namespace App\Crypto;

use Illuminate\Encryption\Encrypter;
use phpseclib\Crypt\RSA;
use phpseclib\Crypt\AES;
use phpseclib\Crypt\Random;

class Keys
{
    /**
     * Encrypt an array of fields using the public key and document key
     * provided. Document key is generated if not available.
     *
     * Extract encrypted content like so:
     * list($encryptedDocKey, $field1, $field2) = Keys::encryptFields($fields, $publicKey)
     *
     * Or if a document key is provided:
     * list($field1, $field2) = Keys::encryptFields($fields, $publicKey, $documentKey)
     *
     * @param array $fields content to encrypt
     * @param string|null $publicKey user's public key
     * @param string|null $documentKey
     * @return array
     */
    public static function encryptFields($fields, $publicKey = null, $documentKey = null)
    {
        if (!$publicKey) {
            $publicKey = \Auth::user()->public_key;
        }

        $result = [];
        if (!$documentKey) {
            $documentKey = self::generateKey();
            $encryptedDocKey = self::rsaEncrypt($documentKey, $publicKey);
            $result[] = $encryptedDocKey;
        }

        foreach ($fields as $value) {
            if (!$value || $value === '') {
                $result[] = '';
            } else {
                $result[] = self::encrypt($value, $documentKey);
            }
        }

        return $result;
    }

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
        if (!$data || $data === '') return null;
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
        if (!$data || $data === '') return null;
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
        if (!$data || $data === '') return null;
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
        if (!$data || $data === '') return null;
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