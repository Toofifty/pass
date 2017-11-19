<?php

namespace App\Http\Controllers\Store;

use App\Model\WebsiteLogin;
use App\Relation\UserWebsiteLogin;
use App\Relation\VaultWebsiteLogin;
use App\Crypto\Keys;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebsiteLoginController extends Controller
{
    public function index()
    {
    	return \Auth::user()->websiteLogins()->get();
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'title' => 'required|max:500'
    	]);

        $result = Keys::encryptFields([request('password'), request('notes')]);
        list($encryptedDocKey, $encryptedPassword, $encryptedNotes) = $result;

        // push to database
        $websiteLogin = null;
        \DB::transaction(function () use ($encryptedPassword, $encryptedNotes, $encryptedDocKey) {
            $websiteLogin = WebsiteLogin::create([
                'title' => request('title'),
                'url' => request('url'),
                'domain' => request('domain'),
                'username' => request('username'),
                'password' => $encryptedPassword,
                'notes' => $encryptedNotes,
                'icon' => request('icon')
            ]);

            UserWebsiteLogin::create([
                'user_id' => \Auth::user()->id,
                'website_login_id' => $websiteLogin->id,
                'document_key' => $encryptedDocKey,
                'permission' => 'owner'
            ]);

            foreach (request('vault') as $vault) {
                if ($vault['id'] === -1) {
                    $this->validate($request, [
                        'vault-title' => 'required|max:500'
                    ]);
                    $vault = VaultController::storeFromLogin($request);
                } else if (\Auth::user()->vaults()->find($vault['id']) === null) {
                    continue;
                }

                VaultWebsiteLogin::create([
                    'vault_id' => $vault['id'],
                    'website_login_id' => $websiteLogin->id,
                    'permission' => 'owner'
                ]);
            }
        });

    	return $websiteLogin;
    }

    public function destroy($id)
    {
    	$note = WebsiteLogin::findOrFail($id);
    	$note->delete();
    	return 204;
    }
}
