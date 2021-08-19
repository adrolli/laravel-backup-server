<?php

namespace Spatie\BackupServer\Support\Helpers;

use Illuminate\Contracts\Filesystem\Filesystem;

class DestinationLocation
{
    public function __construct(
        private Filesystem $disk,
        private string $path
    ) {
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getDirectory(): string
    {
        return pathinfo($this->getPath(), PATHINFO_BASENAME);
    }

    public function getFullPath(): string
    {
        $pathPrefix = $this->disk->getDriver()->getAdapter()->getPathPrefix();

        return $pathPrefix  . $this->path;
    }

    public function __toString()
    {
        return $this->getFullPath();
    }
}
