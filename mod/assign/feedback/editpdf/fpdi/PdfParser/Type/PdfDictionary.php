<?php
/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2019 Setasign - Jan Slabon (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi\PdfParser\Type;

use setasign\Fpdi\PdfParser\PdfParser;
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\Fpdi\PdfParser\Tokenizer;

/**
 * Class representing a PDF dictionary object
 *
 * @package setasign\Fpdi\PdfParser\Type
 */
class PdfDictionary extends PdfType
{
    /**
     * Parses a dictionary of the passed tokenizer, stream-reader and parser.
     *
     * @param Tokenizer $tokenizer
     * @param StreamReader $streamReader
     * @param PdfParser $parser
     * @return bool|self
     * @throws PdfTypeException
     */
    public static function parse(Tokenizer $tokenizer, StreamReader $streamReader, PdfParser $parser)
    {
        $entries = [];

        while (true) {
            $token = $tokenizer->getNextToken();
            if ($token === '>' && $streamReader->getByte() === '>') {
                $streamReader->addOffset(1);
                break;
            }

            $key = $parser->readValue($token);
            if ($key === false) {
                return false;
            }

            // ensure the first value to be a Name object
            if (!($key instanceof PdfName)) {
                $lastToken = null;
                // ignore all other entries and search for the closing brackets
                while (($token = $tokenizer->getNextToken()) !== '>' && $token !== false && $lastToken !== '>') {
                    $lastToken = $token;
                }

                if ($token === false) {
                    return false;
                }

                break;
            }


            $value = $parser->readValue();
            if ($value === false) {
                return false;
            }

            if ($value instanceof PdfNull) {
                continue;
            }

            // catch missing value
            if ($value instanceof PdfToken && $value->value === '>' && $streamReader->getByte() === '>') {
                $streamReader->addOffset(1);
                break;
            }

            $entries[$key->value] = $value;
        }

        $v = new self;
        $v->value = $entries;

        return $v;
    }

    /**
     * Helper method to create an instance.
     *
     * @param PdfType[] $entries The keys are the name entries of the dictionary.
     * @return self
     */
    public static function create(array $entries = [])
    {
        $v = new self;
        $v->value = $entries;

        return $v;
    }

    /**
     * Get a value by its key from a dictionary or a default value.
     *
     * @param mixed $dictionary
     * @param string $key
     * @param PdfType|mixed|null $default
     * @return PdfNull|PdfType
     * @throws PdfTypeException
     */
    public static function get($dictionary, $key, PdfType $default = null)
    {
        $dictionary = self::ensure($dictionary);

        if (isset($dictionary->value[$key])) {
            return $dictionary->value[$key];
        }

        return $default === null
            ? new PdfNull()
            : $default;
    }

    /**
     * Ensures that the passed value is a PdfDictionary instance.
     *
     * @param mixed $dictionary
     * @return self
     * @throws PdfTypeException
     */
    public static function ensure($dictionary)
    {
        return PdfType::ensureType(self::class, $dictionary, 'Dictionary value expected.');
    }
}
