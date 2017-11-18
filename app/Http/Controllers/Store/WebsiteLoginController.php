<?php

namespace App\Http\Controllers\Store;

use App\Note;
use App\UserNote;
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
    		'title' => 'required|max:500',
    		'content' => 'required|max:500'
    	]);

    	// make new document key
    	$documentKey = Keys::generateKey();

    	// encrypt content with key
    	$encryptedContent = Keys::encrypt(request('content'), $documentKey);

    	// encrypt key with user public key
    	$public = Auth::user()->public_key;
    	$encryptedDocKey = Keys::rsaEncrypt($documentKey, $public);

    	// push to database
    	$note = null;
    	DB::transaction(function () use ($encryptedContent, $encryptedDocKey) {
			$note = Note::create([
	    		'title' => request('title'),
	    		'content' => $encryptedContent
	    	]);

	    	UserNote::create([
	    		'user_id' => Auth::user()->id,
	    		'note_id' => $note->id,
	    		'document_key' => $encryptedDocKey,
	    		'permission' => 'owner'
	    	]);
    	});
    	return $note;
    }

    public function destroy($id)
    {
    	$note = Note::findOrFail($id);
    	$note->delete();
    	return 204;
    }
}
