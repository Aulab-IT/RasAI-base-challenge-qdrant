<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use App\Services\DocumentSplitterService;
use App\Services\EmbeddingService;
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
        
        $file = $request->file('file');
        $path = $file->store('documents');

        $document = Document::create([
            'name' => $request->name,
            'path' => $path,
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize()
        ]);

        $content = $document->getContentFromFile();
        $documentParts = DocumentSplitterService::splitDocument($content, 500, ' ', 30);
        // TODO : Cambiare nome modello KnowledgeBase in Embedding
        foreach ($documentParts as $part) {
            
            $embedding = EmbeddingService::createEmbedding($part);
            
            $kb = $document->knowledgeBases()->create([
                'content' => $part,
                'embedding' => json_encode($embedding)
            ]);

        }

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
        $splittedDocuments = DocumentSplitterService::splitDocument($content, 500, ' ', 30);
        
        foreach ($splittedDocuments as $splittedDocument) {
            $embedding = EmbeddingService::createEmbedding($splittedDocument);
            
            $kb = $document->knowledgeBases()->create([
                'content' => $splittedDocument,
                'embedding' => json_encode($embedding)
            ]);

        }
    
        return redirect()->route('documents.index');

        // return view('documents.test', compact('splittedDocuments'));
    }
}
