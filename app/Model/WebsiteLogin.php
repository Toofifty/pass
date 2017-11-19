<?php

namespace App\Model;

use App\Crypto\Keys;
use Illuminate\Database\Eloquent\Model;

class WebsiteLogin extends Model
{
    protected $guarded = [];
    protected $appends = ['decrypted_password'];
    protected $hidden = ['password'];

    /**
     * The users that belong to the note.
     */
    public function users()
    {
        return $this
            ->belongsToMany('App\Model\User', 'user_website_login')
            ->using('App\Relation\UserWebsiteLogin');
    }

    /**
     * Get the decrypted password using the user's private key.
     *
     * @return $string
     */
    public function getDecryptedPasswordAttribute()
    {
    	$private = session('private_key');
    	$document_key = Keys::rsaDecrypt($this->pivot->document_key, $private);
    	$content = Keys::decrypt($this->password, $document_key);
    	return $content;
    }
}