<?php

namespace MongoDB\GridFS\Exception;

use MongoDB\Exception\RuntimeException;
use function MongoDB\BSON\fromPHP;
use function MongoDB\BSON\toJSON;
use function sprintf;
use function stream_get_meta_data;

class StreamException extends RuntimeException
{
    /**
     * @param resource $source
     * @param resource $destination
     */
    public static function downloadFromFilenameFailed(string $filename, $source, $destination) : self
    {
        $sourceMetadata = stream_get_meta_data($source);
        $destinationMetadata = stream_get_meta_data($destination);

        return new static(sprintf('Downloading file from "%s" to "%s" failed. GridFS filename: "%s"', $sourceMetadata['uri'], $destinationMetadata['uri'], $filename));
    }

    /**
     * @param mixed    $id
     * @param resource $source
     * @param resource $destination
     */
    public static function downloadFromIdFailed($id, $source, $destination) : self
    {
        $idString = toJSON(fromPHP(['_id' => $id]));
        $sourceMetadata = stream_get_meta_data($source);
        $destinationMetadata = stream_get_meta_data($destination);

        return new static(sprintf('Downloading file from "%s" to "%s" failed. GridFS identifier: "%s"', $sourceMetadata['uri'], $destinationMetadata['uri'], $idString));
    }

    /** @param resource $source */
    public static function uploadFailed(string $filename, $source, string $destinationUri) : self
    {
        $sourceMetadata = stream_get_meta_data($source);

        return new static(sprintf('Uploading file from "%s" to "%s" failed. GridFS filename: "%s"', $sourceMetadata['uri'], $destinationUri, $filename));
    }
}
