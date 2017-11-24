<?php

namespace App\Model;

use App\Crypto\Keys;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $guarded = [];

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
    public function getContentAttribute($encrypted)
    {
    	return $this->getDecryptedContent($encrypted);
    }
}
