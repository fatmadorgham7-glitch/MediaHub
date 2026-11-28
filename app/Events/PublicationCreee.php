<?php

namespace App\Events;

use App\Models\Publication;

class PublicationCreee
{
    public function __construct(public readonly Publication $publication) {}
}
