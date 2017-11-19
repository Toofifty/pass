<?php

namespace App\Http\Controllers\Store;

use App\Vault;
use App\UserVault;
use App\Crypto\Keys;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VaultController extends Controller
{
    public function index()
    {
    	return \Auth::user()->vaults()->get();
    }

    public static function storeFromLogin(Request $request)
    {
        // no encrypted fields necessary, can't add notes here
        // still need to store a document key though
        list($encryptedDocKey) = Keys::encryptFields([]);

        // push to database
        $vault = null;
        \DB::transaction(function () use ($encryptedDocKey) {
            $vault = Vault::create([
                'title' => request('vault-title'),
                'notes' => null,
                'icon' => null
            ]);

            UserVault::create([
                'user_id' => \Auth::user()->id,
                'vault_id' => $vault->id,
                'document_key' => $encryptedDocKey,
                'permission' => 'owner'
            ]);
        });
        return $vault;
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'title' => 'required|max:500'
    	]);

        list($encryptedDocKey, $encryptedNotes) = Keys::encryptFields([request('notes')]);

    	// push to database
    	$vault = null;
    	\DB::transaction(function () use ($encryptedNotes, $encryptedDocKey) {
			$vault = Vault::create([
	    		'title' => request('title'),
	    		'notes' => $encryptedNotes,
                'icon' => request('icon')
	    	]);

	    	UserVault::create([
	    		'user_id' => \Auth::user()->id,
	    		'vault_id' => $vault->id,
	    		'document_key' => $encryptedDocKey,
	    		'permission' => 'owner'
	    	]);
    	});
    	return $vault;
    }

    public function destroy($id)
    {
    	$note = Vault::findOrFail($id);
    	$note->delete();
    	return 204;
    }
}
