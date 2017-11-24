<?php

namespace App\Model;

use App\Crypto\Keys;
use Illuminate\Database\Eloquent\Model;

class Encryptable extends Model
{
    protected function getDecryptedContent($encrypted)
    {
        $private = session('private_key');
        $documentKey = Keys::rsaDecrypt($this->pivot->document_key, $private);
        $content = Keys::decrypt($encrypted, $documentKey);
        return $content ?: '';
    }
}