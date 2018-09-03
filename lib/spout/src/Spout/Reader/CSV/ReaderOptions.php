<?php

namespace Box\Spout\Reader\CSV;

use Box\Spout\Common\Helper\EncodingHelper;

/**
 * Class ReaderOptions
 * This class is used to customize the reader's behavior
 *
 * @package Box\Spout\Reader\CSV
 */
class ReaderOptions extends \Box\Spout\Reader\Common\ReaderOptions
{
    /** @var string Defines the character used to delimit fields (one character only) */
    protected $fieldDelimiter = ',';

    /** @var string Defines the character used to enclose fields (one character only) */
    protected $fieldEnclosure = '"';

    /** @var string Encoding of the CSV file to be read */
    protected $encoding = EncodingHelper::ENCODING_UTF8;

    /** @var string Defines the End of line */
    protected $endOfLineCharacter = "\n";

    /**
     * @return string
     */
    public function getFieldDelimiter()
    {
        return $this->fieldDelimiter;
    }

    /**
     * Sets the field delimiter for the CSV.
     * Needs to be called before opening the reader.
     *
     * @param string $fieldDelimiter Character that delimits fields
     * @return ReaderOptions
     */
    public function setFieldDelimiter($fieldDelimiter)
    {
        $this->fieldDelimiter = $fieldDelimiter;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldEnclosure()
    {
        return $this->fieldEnclosure;
    }

    /**
     * Sets the field enclosure for the CSV.
     * Needs to be called before opening the reader.
     *
     * @param string $fieldEnclosure Character that enclose fields
     * @return ReaderOptions
     */
    public function setFieldEnclosure($fieldEnclosure)
    {
        $this->fieldEnclosure = $fieldEnclosure;
        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Sets the encoding of the CSV file to be read.
     * Needs to be called before opening the reader.
     *
     * @param string $encoding Encoding of the CSV file to be read
     * @return ReaderOptions
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * @return string EOL for the CSV
     */
    public function getEndOfLineCharacter()
    {
        return $this->endOfLineCharacter;
    }

    /**
     * Sets the EOL for the CSV.
     * Needs to be called before opening the reader.
     *
     * @param string $endOfLineCharacter used to properly get lines from the CSV file.
     * @return ReaderOptions
     */
    public function setEndOfLineCharacter($endOfLineCharacter)
    {
        $this->endOfLineCharacter = $endOfLineCharacter;
        return $this;
    }
}
