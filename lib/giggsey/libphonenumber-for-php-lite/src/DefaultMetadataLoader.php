<?php

declare(strict_types=1);

namespace libphonenumber;

class DefaultMetadataLoader implements MetadataLoaderInterface
{
    public function loadMetadata(string $metadataFileName): array
    {
        return include $metadataFileName;
    }
}
