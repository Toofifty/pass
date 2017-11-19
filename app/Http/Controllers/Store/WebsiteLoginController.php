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

    	// make new document key
    	$documentKey = Keys::generateKey();

    	// encrypt password and notes with key
        $encryptedPassword = Keys::encrypt(request('password'), $documentKey);
        $encryptedNotes = Keys::encrypt(request('notes'), $documentKey);

    	// encrypt key with user public key
    	$public = \Auth::user()->public_key;
    	$encryptedDocKey = Keys::rsaEncrypt($documentKey, $public);

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
    	$note = Note::findOrFail($id);
    	$note->delete();
    	return 204;
    }
}
