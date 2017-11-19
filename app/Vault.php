<?php

namespace App;

use App\Crypto\Keys;
use Illuminate\Database\Eloquent\Model;

class Vault extends Model
{
    protected $guarded = [];
    protected $appends = [];
    protected $hidden = [];

    /**
     * The users that belong to the note.
     */
    public function users()
    {
        return $this
            ->belongsToMany('App\User', 'user_vault')
            ->withPivot('permission');
    }

    public function websiteLogins()
    {
        return $this
            ->belongsToMany('App\WebsiteLogin', 'vault_website_login')
            ->withPivot('permission');
    }

    public function parents()
    {
        return $this
            ->belongsToMany('App\Vault', 'vault_vault', 'child_id', 'parent_id')
            ->withPivot('permission');
    }

    public function children()
    {
        return $this
            ->belongsToMany('App\Vault', 'vault_vault', 'parent_id', 'child_id')
            ->withPivot('permission');
    }
}
