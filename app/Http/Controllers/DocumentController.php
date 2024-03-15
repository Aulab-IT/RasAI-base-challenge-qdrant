<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use App\Services\DocumentSplitter;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::all();

        return view('documents.index' , compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'file' => 'required|file'
        ]);
        
        $document = $request->file('file');
        $path = $document->store('documents');

        $document = new Document([
            'name' => $request->name,
            'path' => $path,
            'mime' => $document->getClientMimeType(),
            'size' => $document->getSize()
        ]);

        $document->save();

        return redirect()->route('documents.index');
    }

    public function download(Document $document)
    {   
        $ext = pathinfo($document->path, PATHINFO_EXTENSION);

        return response()->download(storage_path('app/' . $document->path) , $document->name . '.' . $ext);
    }

    public function destroy(Document $document)
    {
        Storage::delete($document->path);
        $document->delete();

        return redirect()->route('documents.index');
    }

    public function test(Document $document)
    {
        $content = $document->getContentFromFile();
        $splittedDocuments = DocumentSplitter::splitDocument($content, 500, ' ', 30);

        return view('documents.test', compact('splittedDocuments'));
    }
}
