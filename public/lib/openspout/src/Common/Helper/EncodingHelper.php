<?php

declare(strict_types=1);

namespace OpenSpout\Common\Helper;

use Error;
use OpenSpout\Common\Exception\EncodingConversionException;

/**
 * @internal
 */
final readonly class EncodingHelper
{
    /**
     * Definition of the encodings that can have a BOM.
     */
    public const ENCODING_UTF8 = 'UTF-8';
    public const ENCODING_UTF16_LE = 'UTF-16LE';
    public const ENCODING_UTF16_BE = 'UTF-16BE';
    public const ENCODING_UTF32_LE = 'UTF-32LE';
    public const ENCODING_UTF32_BE = 'UTF-32BE';

    /**
     * Definition of the BOMs for the different encodings.
     */
    public const BOM_UTF8 = "\xEF\xBB\xBF";
    public const BOM_UTF16_LE = "\xFF\xFE";
    public const BOM_UTF16_BE = "\xFE\xFF";
    public const BOM_UTF32_LE = "\xFF\xFE\x00\x00";
    public const BOM_UTF32_BE = "\x00\x00\xFE\xFF";

    /** @var array<string, string> Map representing the encodings supporting BOMs (key) and their associated BOM (value) */
    private array $supportedEncodingsWithBom;

    private bool $canUseIconv;

    private bool $canUseMbString;

    public function __construct(bool $canUseIconv, bool $canUseMbString)
    {
        $this->canUseIconv = $canUseIconv;
        $this->canUseMbString = $canUseMbString;

        $this->supportedEncodingsWithBom = [
            self::ENCODING_UTF8 => self::BOM_UTF8,
            self::ENCODING_UTF16_LE => self::BOM_UTF16_LE,
            self::ENCODING_UTF16_BE => self::BOM_UTF16_BE,
            self::ENCODING_UTF32_LE => self::BOM_UTF32_LE,
            self::ENCODING_UTF32_BE => self::BOM_UTF32_BE,
        ];
    }

    public static function factory(): self
    {
        return new self(
            \function_exists('iconv'),
            \function_exists('mb_convert_encoding'),
        );
    }

    /**
     * Returns the number of bytes to use as offset in order to skip the BOM.
     *
     * @param resource $filePointer Pointer to the file to check
     * @param string   $encoding    Encoding of the file to check
     *
     * @return int Bytes offset to apply to skip the BOM (0 means no BOM)
     */
    public function getBytesOffsetToSkipBOM($filePointer, string $encoding): int
    {
        $byteOffsetToSkipBom = 0;

        if ($this->hasBOM($filePointer, $encoding)) {
            $bomUsed = $this->supportedEncodingsWithBom[$encoding];

            // we skip the N first bytes
            $byteOffsetToSkipBom = \strlen($bomUsed);
        }

        return $byteOffsetToSkipBom;
    }

    /**
     * Attempts to convert a non UTF-8 string into UTF-8.
     *
     * @param string $string         Non UTF-8 string to be converted
     * @param string $sourceEncoding The encoding used to encode the source string
     *
     * @return string The converted, UTF-8 string
     *
     * @throws EncodingConversionException If conversion is not supported or if the conversion failed
     */
    public function attemptConversionToUTF8(?string $string, string $sourceEncoding): ?string
    {
        return $this->attemptConversion($string, $sourceEncoding, self::ENCODING_UTF8);
    }

    /**
     * Attempts to convert a UTF-8 string into the given encoding.
     *
     * @param string $string         UTF-8 string to be converted
     * @param string $targetEncoding The encoding the string should be re-encoded into
     *
     * @return string The converted string, encoded with the given encoding
     *
     * @throws EncodingConversionException If conversion is not supported or if the conversion failed
     */
    public function attemptConversionFromUTF8(?string $string, string $targetEncoding): ?string
    {
        return $this->attemptConversion($string, self::ENCODING_UTF8, $targetEncoding);
    }

    /**
     * Returns whether the file identified by the given pointer has a BOM.
     *
     * @param resource $filePointer Pointer to the file to check
     * @param string   $encoding    Encoding of the file to check
     *
     * @return bool TRUE if the file has a BOM, FALSE otherwise
     */
    private function hasBOM($filePointer, string $encoding): bool
    {
        $hasBOM = false;

        rewind($filePointer);

        if (\array_key_exists($encoding, $this->supportedEncodingsWithBom)) {
            $potentialBom = $this->supportedEncodingsWithBom[$encoding];
            $numBytesInBom = \strlen($potentialBom);

            $hasBOM = (fgets($filePointer, $numBytesInBom + 1) === $potentialBom);
        }

        return $hasBOM;
    }

    /**
     * Attempts to convert the given string to the given encoding.
     * Depending on what is installed on the server, we will try to iconv or mbstring.
     *
     * @param string $string         string to be converted
     * @param string $sourceEncoding The encoding used to encode the source string
     * @param string $targetEncoding The encoding the string should be re-encoded into
     *
     * @return string The converted string, encoded with the given encoding
     *
     * @throws EncodingConversionException If conversion is not supported or if the conversion failed
     */
    private function attemptConversion(?string $string, string $sourceEncoding, string $targetEncoding): ?string
    {
        // if source and target encodings are the same, it's a no-op
        if (null === $string || $sourceEncoding === $targetEncoding) {
            return $string;
        }

        $convertedString = null;

        if ($this->canUseIconv) {
            set_error_handler(static function (): bool {
                return true;
            });

            $convertedString = iconv($sourceEncoding, $targetEncoding, $string);

            restore_error_handler();
        } elseif ($this->canUseMbString) {
            $errorMessage = null;
            set_error_handler(static function ($nr, $message) use (&$errorMessage): bool {
                $errorMessage = $message; // @codeCoverageIgnore

                return true; // @codeCoverageIgnore
            });

            try {
                $convertedString = mb_convert_encoding($string, $targetEncoding, $sourceEncoding);
            } catch (Error $error) {
                $errorMessage = $error->getMessage();
            }

            restore_error_handler();
            if (null !== $errorMessage) {
                $convertedString = false;
            }
        } else {
            throw new EncodingConversionException("The conversion from {$sourceEncoding} to {$targetEncoding} is not supported. Please install \"iconv\" or \"mbstring\".");
        }

        if (false === $convertedString) {
            throw new EncodingConversionException("The conversion from {$sourceEncoding} to {$targetEncoding} failed.");
        }

        return $convertedString;
    }
}
