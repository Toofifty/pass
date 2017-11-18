<?php

namespace App;

use App\Crypto\Keys;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $guarded = [];

    protected $appends = ['decrypted_content'];

    /**
     * The users that belong to the note.
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'user_note')->using('App\UserNote');
    }

    public function getDecryptedContentAttribute()
    {
    	$private = session('private_key');
    	// \Log::info($private);
    	\Log::info($this->pivot->document_key);
    	$document_key = Keys::rsaDecrypt($this->pivot->document_key, $private);
    	\Log::info($document_key);
    	$content = Keys::decrypt($this->content, $document_key);
    	\Log::info($content);
    	return $content;
    }
}
