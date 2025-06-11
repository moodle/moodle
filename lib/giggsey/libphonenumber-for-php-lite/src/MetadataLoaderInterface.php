<?php

declare(strict_types=1);

namespace libphonenumber;

/**
 * @interal
 * @phpstan-import-type PhoneMetadataArray from PhoneMetadata
 */
interface MetadataLoaderInterface
{
    /**
     * @param string $metadataFileName File name (including path) of metadata to load.
     * @return PhoneMetadataArray
     */
    public function loadMetadata(string $metadataFileName): array;
}
