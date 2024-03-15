<x-layout>
    
    <div class="container">
        <div class="row gap-4">
            <div class="col-12">
                <h1>Documents</h1>
            </div>
            <div class="col-12">
                <div class="card ragsy-card bg-body-tertiary d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between">
                        <h2 class="fs-4">Files</h2>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="bi bi-upload"></i>
                            Upload a file
                        </button>
                        {{-- <label class="btn btn-outline-primary" for="file">
                            <input wire:change="save" wire:model="file" accept="application/pdf" class="d-none" type="file" name="file" id="file">
                            <i class="bi bi-upload"></i>
                            Upload a file
                        </label> --}}
                    </div>
                    <table class="table table-striped">
                        <thead>
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Type</th>
                            <th scope="col">Size</th>
                            <th scope="col">Uploaded at</th>
                            <th scope="col">
                                <span class="d-none">Actions</span>     
                            </th>
                          </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            @foreach ($documents as $document)

                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $document->name }}</td>
                                <td>{{ $document->mime }}</td>
                                <td>{{ $document->getHumanReadableSizeAttribute() }}</td>
                                <td>{{ $document->created_at->diffForHumans() }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('documents.test', $document) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('documents.download', $document) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <form action="{{ route('documents.destroy', $document) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="uploadModalLabel">Upload a new document</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row gap-3">
                        <div class="col-12">
                            <div class="mb-3 has-validation">
                                <label for="name">Name of file</label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="name@example.com">
                                @error('name')     
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div> 
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="dropzone-area" for="file">
                                <input accept="application/pdf" class="d-none" type="file" name="file" id="file">
                                <span class="file-name">
                                    <i class="bi bi-upload"></i>
                                    Upload a file
                                </span>
                            </label>
                            @error('file')     
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div> 
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>

</x-layout>