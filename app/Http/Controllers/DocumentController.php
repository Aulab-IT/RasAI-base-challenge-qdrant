<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use App\Services\OpenAiService;
use App\Services\QdrantService;
use Illuminate\Support\Facades\Storage;
use App\Services\DocumentSplitterService;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::all();

        return view('documents.index' , compact('documents'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required',
            'file' => 'required|file'
        ]);
        
        // Store the file
        $file = $request->file('file');
        $path = $file->store('documents');

        // Create the document
        $document = Document::create([
            'name' => $request->name,
            'path' => $path,
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize()
        ]);

        // TODO>> 
        // Get the content of the document 
        $content = $document->getContentFromFile();
        // Split the document into parts and create embeddings
        $documentParts = DocumentSplitterService::splitDocument($content, 500, ' ', 20);

        // TODO>>
        // Create embeddings for each part of the document
        $qdrant = new QdrantService();

        foreach ($documentParts as $documentPart) {
            $embedding = OpenAiService::createEmbedding($documentPart);
            
            // Save the embeddings to the vector database
            $qdrant->insert($embedding, $documentPart);
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
            $embedding = OpenAiService::createEmbedding($splittedDocument);
            
            $kb = $document->embeddings()->create([
                'content' => $splittedDocument,
                'embedding' => json_encode($embedding)
            ]);

        }
    
        return redirect()->route('documents.index');

        // return view('documents.test', compact('splittedDocuments'));
    }
}
