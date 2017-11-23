<?php

namespace App\Http\Controllers\Store;

use App\Model\Vault;
use App\Relation\UserVault;
use App\Relation\VaultVault;
use App\Crypto\Keys;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VaultController extends Controller
{
    public function index()
    {
    	return \Auth::user()->vaults()->where('top_level', true)->get();
    }

    public function all($id) 
    {
        return \Auth::user()->vaults()->with('children', 'websiteLogins')->find($id);
    }

    public static function storeFromLogin(Request $request)
    {
        // no encrypted fields necessary, can't add notes here
        // still need to store a document key though
        list($encryptedDocKey) = Keys::encryptFields([]);

        // push to database
        $vault = null;
        \DB::transaction(function () use (&$vault, $encryptedDocKey) {
            $title = '';
            foreach (request('vaults') as $reqVault) {
                if ($reqVault['id'] === -1) {
                    $title = $reqVault['title'];
                    break;
                }
            }

            $vault = Vault::create([
                'title' => $title,
                'notes' => null,
                'top_level' => true,
                'icon' => null
            ]);

            UserVault::create([
                'user_id' => \Auth::user()->id,
                'vault_id' => $vault->id,
                'document_key' => $encryptedDocKey,
                'permission' => 'owner'
            ]);
        });
        \Log::info(json_encode($vault));
        return $vault;
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'title' => 'required|max:500'
    	]);

        list($encryptedDocKey) = Keys::encryptFields([]);

    	// push to database
    	$vault = null;
    	\DB::transaction(function () use ($vault, $encryptedDocKey) {
			$vault = Vault::create([
	    		'title' => request('title'),
	    		'notes' => request('notes'),
                'icon' => request('icon'),
                'top_level' => !request('parents') || request('parents') === null
	    	]);

            if (request('parents') !== null) {
                foreach (request('parents') as $parent) {
                    VaultVault::create([
                        'parent_id' => $parent['id'],
                        'child_id' => $vault->id,
                        'permission' => '???'
                    ]);
                }
            }

	    	UserVault::create([
	    		'user_id' => \Auth::user()->id,
	    		'vault_id' => $vault->id,
	    		'document_key' => $encryptedDocKey,
	    		'permission' => 'owner'
	    	]);
    	});
    	return $vault;
    }

    public function update(Request $request, $id)
    {
        $vault = null;
        \DB::transaction(function () use ($id, $vault) {
            $vault = Vault::with('parents')->findOrFail($id);

            $parentIds = function ($parent) {
                return $parent['id'];
            };

            $parents = request('parents');
            if (!$parents) {
                $parents = [];
            }

            $oldParents = array_map($parentIds, $vault->parents->toArray());
            $newParents = array_map($parentIds, $parents);

            $createdParents = array_diff($newParents, $oldParents);
            $deletedParents = array_diff($oldParents, $newParents);

            $vault->update([
                'title' => request('title'),
                'notes' => request('notes'),
                'icon' => request('icon'),
                'top_level' => !request('parents') || request('parents') === null
            ]);
            
            foreach ($deletedParents as $deletedId) {
                VaultVault::where('child_id', $id)->where('parent_id', $deletedId)->delete();
            }

            foreach ($createdParents as $createdId) {
                VaultVault::create([
                    'parent_id' => $createdId,
                    'child_id' => $id,
                    'permission' => '???'
                ]);
            }
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
