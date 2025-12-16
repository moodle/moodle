<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Bigquery;

class CsvOptions extends \Google\Collection
{
  protected $collection_key = 'nullMarkers';
  /**
   * Optional. Indicates if BigQuery should accept rows that are missing
   * trailing optional columns. If true, BigQuery treats missing trailing
   * columns as null values. If false, records with missing trailing columns are
   * treated as bad records, and if there are too many bad records, an invalid
   * error is returned in the job result. The default value is false.
   *
   * @var bool
   */
  public $allowJaggedRows;
  /**
   * Optional. Indicates if BigQuery should allow quoted data sections that
   * contain newline characters in a CSV file. The default value is false.
   *
   * @var bool
   */
  public $allowQuotedNewlines;
  /**
   * Optional. The character encoding of the data. The supported values are
   * UTF-8, ISO-8859-1, UTF-16BE, UTF-16LE, UTF-32BE, and UTF-32LE. The default
   * value is UTF-8. BigQuery decodes the data after the raw, binary data has
   * been split using the values of the quote and fieldDelimiter properties.
   *
   * @var string
   */
  public $encoding;
  /**
   * Optional. The separator character for fields in a CSV file. The separator
   * is interpreted as a single byte. For files encoded in ISO-8859-1, any
   * single character can be used as a separator. For files encoded in UTF-8,
   * characters represented in decimal range 1-127 (U+0001-U+007F) can be used
   * without any modification. UTF-8 characters encoded with multiple bytes
   * (i.e. U+0080 and above) will have only the first byte used for separating
   * fields. The remaining bytes will be treated as a part of the field.
   * BigQuery also supports the escape sequence "\t" (U+0009) to specify a tab
   * separator. The default value is comma (",", U+002C).
   *
   * @var string
   */
  public $fieldDelimiter;
  /**
   * Optional. Specifies a string that represents a null value in a CSV file.
   * For example, if you specify "\N", BigQuery interprets "\N" as a null value
   * when querying a CSV file. The default value is the empty string. If you set
   * this property to a custom value, BigQuery throws an error if an empty
   * string is present for all data types except for STRING and BYTE. For STRING
   * and BYTE columns, BigQuery interprets the empty string as an empty value.
   *
   * @var string
   */
  public $nullMarker;
  /**
   * Optional. A list of strings represented as SQL NULL value in a CSV file.
   * null_marker and null_markers can't be set at the same time. If null_marker
   * is set, null_markers has to be not set. If null_markers is set, null_marker
   * has to be not set. If both null_marker and null_markers are set at the same
   * time, a user error would be thrown. Any strings listed in null_markers,
   * including empty string would be interpreted as SQL NULL. This applies to
   * all column types.
   *
   * @var string[]
   */
  public $nullMarkers;
  /**
   * Optional. Indicates if the embedded ASCII control characters (the first 32
   * characters in the ASCII-table, from '\x00' to '\x1F') are preserved.
   *
   * @var bool
   */
  public $preserveAsciiControlCharacters;
  /**
   * Optional. The value that is used to quote data sections in a CSV file.
   * BigQuery converts the string to ISO-8859-1 encoding, and then uses the
   * first byte of the encoded string to split the data in its raw, binary
   * state. The default value is a double-quote ("). If your data does not
   * contain quoted sections, set the property value to an empty string. If your
   * data contains quoted newline characters, you must also set the
   * allowQuotedNewlines property to true. To include the specific quote
   * character within a quoted value, precede it with an additional matching
   * quote character. For example, if you want to escape the default character '
   * " ', use ' "" '.
   *
   * @var string
   */
  public $quote;
  /**
   * Optional. The number of rows at the top of a CSV file that BigQuery will
   * skip when reading the data. The default value is 0. This property is useful
   * if you have header rows in the file that should be skipped. When autodetect
   * is on, the behavior is the following: * skipLeadingRows unspecified -
   * Autodetect tries to detect headers in the first row. If they are not
   * detected, the row is read as data. Otherwise data is read starting from the
   * second row. * skipLeadingRows is 0 - Instructs autodetect that there are no
   * headers and data should be read starting from the first row. *
   * skipLeadingRows = N > 0 - Autodetect skips N-1 rows and tries to detect
   * headers in row N. If headers are not detected, row N is just skipped.
   * Otherwise row N is used to extract column names for the detected schema.
   *
   * @var string
   */
  public $skipLeadingRows;
  /**
   * Optional. Controls the strategy used to match loaded columns to the schema.
   * If not set, a sensible default is chosen based on how the schema is
   * provided. If autodetect is used, then columns are matched by name.
   * Otherwise, columns are matched by position. This is done to keep the
   * behavior backward-compatible. Acceptable values are: POSITION - matches by
   * position. This assumes that the columns are ordered the same way as the
   * schema. NAME - matches by name. This reads the header row as column names
   * and reorders columns to match the field names in the schema.
   *
   * @var string
   */
  public $sourceColumnMatch;

  /**
   * Optional. Indicates if BigQuery should accept rows that are missing
   * trailing optional columns. If true, BigQuery treats missing trailing
   * columns as null values. If false, records with missing trailing columns are
   * treated as bad records, and if there are too many bad records, an invalid
   * error is returned in the job result. The default value is false.
   *
   * @param bool $allowJaggedRows
   */
  public function setAllowJaggedRows($allowJaggedRows)
  {
    $this->allowJaggedRows = $allowJaggedRows;
  }
  /**
   * @return bool
   */
  public function getAllowJaggedRows()
  {
    return $this->allowJaggedRows;
  }
  /**
   * Optional. Indicates if BigQuery should allow quoted data sections that
   * contain newline characters in a CSV file. The default value is false.
   *
   * @param bool $allowQuotedNewlines
   */
  public function setAllowQuotedNewlines($allowQuotedNewlines)
  {
    $this->allowQuotedNewlines = $allowQuotedNewlines;
  }
  /**
   * @return bool
   */
  public function getAllowQuotedNewlines()
  {
    return $this->allowQuotedNewlines;
  }
  /**
   * Optional. The character encoding of the data. The supported values are
   * UTF-8, ISO-8859-1, UTF-16BE, UTF-16LE, UTF-32BE, and UTF-32LE. The default
   * value is UTF-8. BigQuery decodes the data after the raw, binary data has
   * been split using the values of the quote and fieldDelimiter properties.
   *
   * @param string $encoding
   */
  public function setEncoding($encoding)
  {
    $this->encoding = $encoding;
  }
  /**
   * @return string
   */
  public function getEncoding()
  {
    return $this->encoding;
  }
  /**
   * Optional. The separator character for fields in a CSV file. The separator
   * is interpreted as a single byte. For files encoded in ISO-8859-1, any
   * single character can be used as a separator. For files encoded in UTF-8,
   * characters represented in decimal range 1-127 (U+0001-U+007F) can be used
   * without any modification. UTF-8 characters encoded with multiple bytes
   * (i.e. U+0080 and above) will have only the first byte used for separating
   * fields. The remaining bytes will be treated as a part of the field.
   * BigQuery also supports the escape sequence "\t" (U+0009) to specify a tab
   * separator. The default value is comma (",", U+002C).
   *
   * @param string $fieldDelimiter
   */
  public function setFieldDelimiter($fieldDelimiter)
  {
    $this->fieldDelimiter = $fieldDelimiter;
  }
  /**
   * @return string
   */
  public function getFieldDelimiter()
  {
    return $this->fieldDelimiter;
  }
  /**
   * Optional. Specifies a string that represents a null value in a CSV file.
   * For example, if you specify "\N", BigQuery interprets "\N" as a null value
   * when querying a CSV file. The default value is the empty string. If you set
   * this property to a custom value, BigQuery throws an error if an empty
   * string is present for all data types except for STRING and BYTE. For STRING
   * and BYTE columns, BigQuery interprets the empty string as an empty value.
   *
   * @param string $nullMarker
   */
  public function setNullMarker($nullMarker)
  {
    $this->nullMarker = $nullMarker;
  }
  /**
   * @return string
   */
  public function getNullMarker()
  {
    return $this->nullMarker;
  }
  /**
   * Optional. A list of strings represented as SQL NULL value in a CSV file.
   * null_marker and null_markers can't be set at the same time. If null_marker
   * is set, null_markers has to be not set. If null_markers is set, null_marker
   * has to be not set. If both null_marker and null_markers are set at the same
   * time, a user error would be thrown. Any strings listed in null_markers,
   * including empty string would be interpreted as SQL NULL. This applies to
   * all column types.
   *
   * @param string[] $nullMarkers
   */
  public function setNullMarkers($nullMarkers)
  {
    $this->nullMarkers = $nullMarkers;
  }
  /**
   * @return string[]
   */
  public function getNullMarkers()
  {
    return $this->nullMarkers;
  }
  /**
   * Optional. Indicates if the embedded ASCII control characters (the first 32
   * characters in the ASCII-table, from '\x00' to '\x1F') are preserved.
   *
   * @param bool $preserveAsciiControlCharacters
   */
  public function setPreserveAsciiControlCharacters($preserveAsciiControlCharacters)
  {
    $this->preserveAsciiControlCharacters = $preserveAsciiControlCharacters;
  }
  /**
   * @return bool
   */
  public function getPreserveAsciiControlCharacters()
  {
    return $this->preserveAsciiControlCharacters;
  }
  /**
   * Optional. The value that is used to quote data sections in a CSV file.
   * BigQuery converts the string to ISO-8859-1 encoding, and then uses the
   * first byte of the encoded string to split the data in its raw, binary
   * state. The default value is a double-quote ("). If your data does not
   * contain quoted sections, set the property value to an empty string. If your
   * data contains quoted newline characters, you must also set the
   * allowQuotedNewlines property to true. To include the specific quote
   * character within a quoted value, precede it with an additional matching
   * quote character. For example, if you want to escape the default character '
   * " ', use ' "" '.
   *
   * @param string $quote
   */
  public function setQuote($quote)
  {
    $this->quote = $quote;
  }
  /**
   * @return string
   */
  public function getQuote()
  {
    return $this->quote;
  }
  /**
   * Optional. The number of rows at the top of a CSV file that BigQuery will
   * skip when reading the data. The default value is 0. This property is useful
   * if you have header rows in the file that should be skipped. When autodetect
   * is on, the behavior is the following: * skipLeadingRows unspecified -
   * Autodetect tries to detect headers in the first row. If they are not
   * detected, the row is read as data. Otherwise data is read starting from the
   * second row. * skipLeadingRows is 0 - Instructs autodetect that there are no
   * headers and data should be read starting from the first row. *
   * skipLeadingRows = N > 0 - Autodetect skips N-1 rows and tries to detect
   * headers in row N. If headers are not detected, row N is just skipped.
   * Otherwise row N is used to extract column names for the detected schema.
   *
   * @param string $skipLeadingRows
   */
  public function setSkipLeadingRows($skipLeadingRows)
  {
    $this->skipLeadingRows = $skipLeadingRows;
  }
  /**
   * @return string
   */
  public function getSkipLeadingRows()
  {
    return $this->skipLeadingRows;
  }
  /**
   * Optional. Controls the strategy used to match loaded columns to the schema.
   * If not set, a sensible default is chosen based on how the schema is
   * provided. If autodetect is used, then columns are matched by name.
   * Otherwise, columns are matched by position. This is done to keep the
   * behavior backward-compatible. Acceptable values are: POSITION - matches by
   * position. This assumes that the columns are ordered the same way as the
   * schema. NAME - matches by name. This reads the header row as column names
   * and reorders columns to match the field names in the schema.
   *
   * @param string $sourceColumnMatch
   */
  public function setSourceColumnMatch($sourceColumnMatch)
  {
    $this->sourceColumnMatch = $sourceColumnMatch;
  }
  /**
   * @return string
   */
  public function getSourceColumnMatch()
  {
    return $this->sourceColumnMatch;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CsvOptions::class, 'Google_Service_Bigquery_CsvOptions');
