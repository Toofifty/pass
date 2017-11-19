<?php

namespace App;

use App\Crypto\Keys;
use Illuminate\Database\Eloquent\Model;

class Vault extends Model
{
    protected $guarded = [];
    protected $appends = ['decrypted_notes'];
    protected $hidden = ['notes'];

    /**
     * The users that belong to the note.
     */
    public function users()
    {
        return $this
            ->belongsToMany('App\User', 'user_vault')
            ->withPivot();
    }

    /**
     * Get the decrypted content using the user's private key.
     *
     * @return $string
     */
    public function getDecryptedNotesAttribute()
    {
        if (!$this->notes || $this->notes === '') return $this->notes;
        
    	$private = session('private_key');
    	$document_key = Keys::rsaDecrypt($this->pivot->document_key, $private);
    	$notes = Keys::decrypt($this->notes, $document_key);
    	return $notes;
    }
}
