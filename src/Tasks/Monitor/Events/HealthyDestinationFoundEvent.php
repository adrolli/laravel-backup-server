<?php

namespace Spatie\BackupServer\Tasks\Monitor\Events;

use Spatie\BackupServer\Models\Destination;

class HealthyDestinationFoundEvent
{
    public function __construct(
        public Destination $destination
    ) {}
}
