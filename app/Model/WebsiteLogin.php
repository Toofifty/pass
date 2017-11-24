<?php

namespace App\Model;

use App\Crypto\Keys;
use Illuminate\Database\Eloquent\Model;

class WebsiteLogin extends Encryptable
{
    protected $guarded = [];

    /**
     * The users that belong to the note.
     */
    public function users()
    {
        return $this
            ->belongsToMany('App\User', 'user_website_login')
            ->using('App\Relation\UserWebsiteLogin');
    }

    public function vaults()
    {
        return $this
            ->belongsToMany('App\Model\Vault', 'vault_website_login')
            ->using('App\Relation\VaultWebsiteLogin');
    }

    /**
     * Get the decrypted password using the user's private key.
     *
     * @return $string
     */
    public function getPasswordAttribute($encrypted)
    {
        return $this->getDecryptedContent($encrypted);
    }

    /**
     * Get the decrypted notes using the user's private key.
     *
     * @return $string
     */
    public function getNotesAttribute($encrypted)
    {
        return $this->getDecryptedContent($encrypted);
    }
}
