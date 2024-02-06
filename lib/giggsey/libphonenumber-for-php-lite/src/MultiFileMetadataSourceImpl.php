<?php

declare(strict_types=1);

namespace libphonenumber;

/**
 * @internal
 */
class MultiFileMetadataSourceImpl implements MetadataSourceInterface
{
    protected static string $metaDataFilePrefix = PhoneNumberUtil::META_DATA_FILE_PREFIX;

    /**
     * A mapping from a region code to the PhoneMetadata for that region.
     * @var PhoneMetadata[]
     */
    protected array $regionToMetadataMap = [];

    /**
     * A mapping from a country calling code for a non-geographical entity to the PhoneMetadata for
     * that country calling code. Examples of the country calling codes include 800 (International
     * Toll Free Service) and 808 (International Shared Cost Service).
     * @var PhoneMetadata[]
     */
    protected array $countryCodeToNonGeographicalMetadataMap = [];

    /**
     * The prefix of the metadata files from which region data is loaded.
     */
    protected ?string $currentFilePrefix;

    public function __construct(protected MetadataLoaderInterface $metadataLoader, ?string $currentFilePrefix = null)
    {
        if ($currentFilePrefix === null) {
            $currentFilePrefix = static::$metaDataFilePrefix;
        }

        $this->currentFilePrefix = $currentFilePrefix;
    }

    /**
     * @inheritdoc
     */
    public function getMetadataForRegion(string $regionCode): PhoneMetadata
    {
        $regionCode = strtoupper($regionCode);

        if (!array_key_exists($regionCode, $this->regionToMetadataMap)) {
            // The regionCode here will be valid and won't be '001', so we don't need to worry about
            // what to pass in for the country calling code.
            $this->loadMetadataFromFile($this->currentFilePrefix, $regionCode, 0, $this->metadataLoader);
        }

        return $this->regionToMetadataMap[$regionCode];
    }

    /**
     * @inheritdoc
     */
    public function getMetadataForNonGeographicalRegion(int $countryCallingCode): PhoneMetadata
    {
        if (!array_key_exists($countryCallingCode, $this->countryCodeToNonGeographicalMetadataMap)) {
            $this->loadMetadataFromFile($this->currentFilePrefix, PhoneNumberUtil::REGION_CODE_FOR_NON_GEO_ENTITY, $countryCallingCode, $this->metadataLoader);
        }

        return $this->countryCodeToNonGeographicalMetadataMap[$countryCallingCode];
    }

    /**
     * @param string $filePrefix
     * @param string $regionCode
     * @param int $countryCallingCode
     * @param MetadataLoaderInterface $metadataLoader
     */
    public function loadMetadataFromFile(string $filePrefix, string $regionCode, int $countryCallingCode, MetadataLoaderInterface $metadataLoader): void
    {
        $regionCode = strtoupper($regionCode);

        $isNonGeoRegion = PhoneNumberUtil::REGION_CODE_FOR_NON_GEO_ENTITY === $regionCode;
        $fileName = $filePrefix . '_' . ($isNonGeoRegion ? $countryCallingCode : $regionCode) . '.php';
        if (!is_readable($fileName)) {
            throw new \RuntimeException('missing metadata: ' . $fileName);
        }

        $data = $metadataLoader->loadMetadata($fileName);
        $metadata = new PhoneMetadata();
        $metadata->fromArray($data);
        if ($isNonGeoRegion) {
            $this->countryCodeToNonGeographicalMetadataMap[$countryCallingCode] = $metadata;
        } else {
            $this->regionToMetadataMap[$regionCode] = $metadata;
        }
    }
}
