<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicationDocumentController extends Controller
{
    public function __invoke(Publication $publication): StreamedResponse
    {
        $this->authorize('viewDocument', $publication);

        abort_unless(
            $publication->document && Storage::disk('local')->exists($publication->document),
            404
        );

        return Storage::disk('local')->download(
            $publication->document,
            basename($publication->document)
        );
    }
}
