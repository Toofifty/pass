<?php

namespace App\Http\Controllers;

use App\Note;
use App\UserNote;
use App\Crypto\Keys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    public function index()
    {
    	// return Note::find(3)->users()->get();
    	return Auth::user()->notes()->get();
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'title' => 'required|max:500',
    		'content' => 'required|max:500'
    	]);

    	// make new document key
    	$documentKey = Keys::generateKey();

    	Log::info("DOCUMENT KEY: $documentKey");

    	// encrypt content with key
    	$encryptedContent = Keys::encrypt(request('content'), $documentKey);

    	Log::info("CONTENT ".request('content'));
    	Log::info("ENC CONTENT: $encryptedContent");

    	// encrypt key with user public key
    	$userPub = Auth::user()->public_key;
    	$encryptedDocKey = Keys::rsaEncrypt($documentKey, $userPub);

    	Log::info("ENC DOCUMENT KEY: $encryptedDocKey");

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
