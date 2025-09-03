<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2024 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi\PdfParser\Type;

use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\PdfParser;
use setasign\Fpdi\PdfParser\PdfParserException;

/**
 * A class defining a PDF data type
 */
class PdfType
{
    /**
     * Resolves a PdfType value to its value.
     *
     * This method is used to evaluate indirect and direct object references until a final value is reached.
     *
     * @param PdfType $value
     * @param PdfParser $parser
     * @param bool $stopAtIndirectObject
     * @param array $ensuredObjectsList A list of all ensured indirect objects to prevent recursion
     * @return PdfType
     * @throws CrossReferenceException
     * @throws PdfParserException
     */
    public static function resolve(
        PdfType $value,
        PdfParser $parser,
        $stopAtIndirectObject = false,
        array &$ensuredObjectsList = []
    ) {
        if ($value instanceof PdfIndirectObjectReference) {
            $value = $parser->getIndirectObject($value->value);
        }

        if ($value instanceof PdfIndirectObject) {
            if ($stopAtIndirectObject === true) {
                return $value;
            }

            if (\in_array($value->objectNumber, $ensuredObjectsList, true)) {
                throw new PdfParserException(
                    \sprintf('Indirect reference recursion detected (%s).', $value->objectNumber)
                );
            }
            $ensuredObjectsList[] = $value->objectNumber;
            return self::resolve($value->value, $parser, $stopAtIndirectObject, $ensuredObjectsList);
        }

        return $value;
    }

    /**
     * Ensure that a value is an instance of a specific PDF type.
     *
     * @param string $type
     * @param PdfType $value
     * @param string $errorMessage
     * @return mixed
     * @throws PdfTypeException
     */
    protected static function ensureType($type, $value, $errorMessage)
    {
        if (!($value instanceof $type)) {
            throw new PdfTypeException(
                $errorMessage,
                PdfTypeException::INVALID_DATA_TYPE
            );
        }

        return $value;
    }

    /**
     * Flatten indirect object references to direct objects.
     *
     * @param PdfType $value
     * @param PdfParser $parser
     * @return PdfType
     * @throws CrossReferenceException
     * @throws PdfParserException
     */
    public static function flatten(PdfType $value, PdfParser $parser)
    {
        if ($value instanceof PdfIndirectObjectReference) {
            return self::flatten(self::resolve($value, $parser), $parser);
        }

        if ($value instanceof PdfDictionary || $value instanceof PdfArray) {
            foreach ($value->value as $key => $_value) {
                $value->value[$key] = self::flatten($_value, $parser);
            }
        }

        if ($value instanceof PdfStream) {
            throw new PdfTypeException('There is a stream object found which cannot be flattened to a direct object.');
        }

        return $value;
    }

    /**
     * The value of the PDF type.
     *
     * @var mixed
     */
    public $value;
}
