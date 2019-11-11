<?php
/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2019 Setasign - Jan Slabon (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi\PdfParser\Type;

use setasign\Fpdi\PdfParser\StreamReader;

/**
 * Class representing a PDF string object
 *
 * @package setasign\Fpdi\PdfParser\Type
 */
class PdfString extends PdfType
{
    /**
     * Parses a string object from the stream reader.
     *
     * @param StreamReader $streamReader
     * @return self
     */
    public static function parse(StreamReader $streamReader)
    {
        $pos = $startPos = $streamReader->getOffset();
        $openBrackets = 1;
        do {
            $buffer = $streamReader->getBuffer(false);
            for ($length = \strlen($buffer); $openBrackets !== 0 && $pos < $length; $pos++) {
                switch ($buffer[$pos]) {
                    case '(':
                        $openBrackets++;
                        break;
                    case ')':
                        $openBrackets--;
                        break;
                    case '\\':
                        $pos++;
                }
            }
        } while ($openBrackets !== 0 && $streamReader->increaseLength());

        $result = \substr($buffer, $startPos, $openBrackets + $pos - $startPos - 1);
        $streamReader->setOffset($pos);

        $v = new self;
        $v->value = $result;

        return $v;
    }

    /**
     * Helper method to create an instance.
     *
     * @param string $value The string needs to be escaped accordingly.
     * @return self
     */
    public static function create($value)
    {
        $v = new self;
        $v->value = $value;

        return $v;
    }

    /**
     * Ensures that the passed value is a PdfString instance.
     *
     * @param mixed $string
     * @return self
     * @throws PdfTypeException
     */
    public static function ensure($string)
    {
        return PdfType::ensureType(self::class, $string, 'String value expected.');
    }

    /**
     * Unescapes escaped sequences in a PDF string according to the PDF specification.
     *
     * @param string $s
     * @return string
     */
    public static function unescape($s)
    {
        $out = '';
        /** @noinspection ForeachInvariantsInspection */
        for ($count = 0, $n = \strlen($s); $count < $n; $count++) {
            if ($s[$count] !== '\\') {
                $out .= $s[$count];
            } else {
                // A backslash at the end of the string - ignore it
                if ($count === ($n - 1)) {
                    break;
                }

                switch ($s[++$count]) {
                    case ')':
                    case '(':
                    case '\\':
                        $out .= $s[$count];
                        break;

                    case 'f':
                        $out .= "\x0C";
                        break;

                    case 'b':
                        $out .= "\x08";
                        break;

                    case 't':
                        $out .= "\x09";
                        break;

                    case 'r':
                        $out .= "\x0D";
                        break;

                    case 'n':
                        $out .= "\x0A";
                        break;

                    case "\r":
                        if ($count !== $n - 1 && $s[$count + 1] === "\n") {
                            $count++;
                        }
                        break;

                    case "\n":
                        break;

                    default:
                        $actualChar = \ord($s[$count]);
                        // ascii 48 = number 0
                        // ascii 57 = number 9
                        if ($actualChar >= 48 &&
                            $actualChar <= 57) {
                            $oct = '' . $s[$count];

                            /** @noinspection NotOptimalIfConditionsInspection */
                            if ($count + 1 < $n &&
                                \ord($s[$count + 1]) >= 48 &&
                                \ord($s[$count + 1]) <= 57
                            ) {
                                $count++;
                                $oct .= $s[$count];

                                /** @noinspection NotOptimalIfConditionsInspection */
                                if ($count + 1 < $n &&
                                    \ord($s[$count + 1]) >= 48 &&
                                    \ord($s[$count + 1]) <= 57
                                ) {
                                    $oct .= $s[++$count];
                                }
                            }

                            $out .= \chr(\octdec($oct));
                        } else {
                            // If the character is not one of those defined, the backslash is ignored
                            $out .= $s[$count];
                        }
                }
            }
        }
        return $out;
    }
}
