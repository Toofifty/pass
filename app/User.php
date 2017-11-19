<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'public_key', 'private_key'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The notes that belong to the user.
     */
    public function notes()
    {
        return $this
            ->belongsToMany('App\Model\Note', 'user_note')
            ->withPivot('document_key');
    }

    /**
     * The vaults that belong to the user.
     */
    public function vaults()
    {
        return $this
            ->belongsToMany('App\Model\Vault', 'user_vault')
            ->withPivot('document_key');
    }

    /**
     * The website logins that belong to the user.
     */
    public function websiteLogins()
    {
        return $this
            ->belongsToMany('App\Model\WebsiteLogin', 'user_website_login')
            ->withPivot('document_key');
    }
}
