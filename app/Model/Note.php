<?php

namespace App\Model;

use App\Crypto\Keys;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $guarded = [];
    protected $appends = ['decrypted_content'];
    protected $hidden = ['content'];

    /**
     * The users that belong to the note.
     */
    public function users()
    {
        return $this
            ->belongsToMany('App\User', 'user_note')
            ->withPivot();
    }

    /**
     * Get the decrypted content using the user's private key.
     *
     * @return $string
     */
    public function getDecryptedContentAttribute()
    {
    	$private = session('private_key');
    	$document_key = Keys::rsaDecrypt($this->pivot->document_key, $private);
    	$content = Keys::decrypt($this->content, $document_key);
    	return $content;
    }
}
