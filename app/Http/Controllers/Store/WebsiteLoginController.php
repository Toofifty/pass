<?php

namespace App\Http\Controllers\Store;

use App\WebsiteLogin;
use App\UserWebsiteLogin;
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

        foreach (request('vault') as $vault) {
            if ($vault['title'] === 'Create new') {
                $this->validate($request, [
                    'vault-title' => 'required|max:500'
                ]);
                $vault = VaultController::storeFromLogin($request);
            }

            // TODO: add to existing vaults
            // Make sure to check if the user has access
        }

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
