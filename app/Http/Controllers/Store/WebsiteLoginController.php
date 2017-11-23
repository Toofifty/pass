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

    public function show($id)
    {
        return \Auth::user()->websiteLogins()->with('vaults')->find($id);
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
        \DB::transaction(function () use ($request, $encryptedPassword, $encryptedNotes, $encryptedDocKey) {
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

            foreach (request('vaults') as $vault) {
                if ($vault['id'] === -1) {
                    $this->validate($request, [
                        'vaults.*.title' => 'required|max:500'
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

    public function update(Request $request, $id)
    {
    	$this->validate($request, [
    		'title' => 'required|max:500'
        ]);

        // get document key
        $documentKey = \Auth::user()->websiteLogins()->find($id)->pivot->document_key;

        $result = Keys::encryptFields([request('password'), request('notes')], null, $documentKey);
        list($encryptedPassword, $encryptedNotes) = $result;

        $websiteLogin = null;
        \DB::transaction(function () use ($id, $websiteLogin, $encryptedPassword, $encryptedNotes) {
            $websiteLogin = WebsiteLogin::with('vaults')->findOrFail($id);

            $vaultIds = function ($vault) {
                return $vault['id'];
            };

            $vaults = request('vaults');
            if (!$vaults) {
                $vaults = [];
            }

            $oldVaults = array_map($vaultIds, $websiteLogin->vaults->toArray());
            $newVaults = array_map($vaultIds, $vaults);

            $createdVaults = array_diff($newVaults, $oldVaults);
            $deletedVaults = array_diff($oldVaults, $newVaults);

            $websiteLogin->update([
                'title' => request('title'),
                'url' => request('url'),
                'domain' => request('domain'),
                'username' => request('username'),
                'password' => $encryptedPassword,
                'notes' => $encryptedNotes,
                'icon' => request('icon')
            ]);
            
            foreach ($deletedVaults as $deletedId) {
                VaultWebsiteLogin::where('website_login_id', $id)->where('vault_id', $deletedId)->delete();
            }

            foreach ($createdVaults as $createdId) {
                VaultWebsiteLogin::create([
                    'vault_id' => $createdId,
                    'website_login_id' => $id,
                    'permission' => '???'
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
