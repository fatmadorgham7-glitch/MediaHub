<?php

namespace App\Listeners;

use App\Events\PublicationCreee;
use Illuminate\Support\Facades\Log;

class JournaliserPublicationCreee
{
    public function handle(PublicationCreee $event): void
    {
        Log::info('Publication créée', ['id' => $event->publication->id]);
    }
}
