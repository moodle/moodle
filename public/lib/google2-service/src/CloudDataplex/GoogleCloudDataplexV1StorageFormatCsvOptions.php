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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1StorageFormatCsvOptions extends \Google\Model
{
  /**
   * Optional. The delimiter used to separate values. Defaults to ','.
   *
   * @var string
   */
  public $delimiter;
  /**
   * Optional. The character encoding of the data. Accepts "US-ASCII", "UTF-8",
   * and "ISO-8859-1". Defaults to UTF-8 if unspecified.
   *
   * @var string
   */
  public $encoding;
  /**
   * Optional. The number of rows to interpret as header rows that should be
   * skipped when reading data rows. Defaults to 0.
   *
   * @var int
   */
  public $headerRows;
  /**
   * Optional. The character used to quote column values. Accepts '"' (double
   * quotation mark) or ''' (single quotation mark). Defaults to '"' (double
   * quotation mark) if unspecified.
   *
   * @var string
   */
  public $quote;

  /**
   * Optional. The delimiter used to separate values. Defaults to ','.
   *
   * @param string $delimiter
   */
  public function setDelimiter($delimiter)
  {
    $this->delimiter = $delimiter;
  }
  /**
   * @return string
   */
  public function getDelimiter()
  {
    return $this->delimiter;
  }
  /**
   * Optional. The character encoding of the data. Accepts "US-ASCII", "UTF-8",
   * and "ISO-8859-1". Defaults to UTF-8 if unspecified.
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
   * Optional. The number of rows to interpret as header rows that should be
   * skipped when reading data rows. Defaults to 0.
   *
   * @param int $headerRows
   */
  public function setHeaderRows($headerRows)
  {
    $this->headerRows = $headerRows;
  }
  /**
   * @return int
   */
  public function getHeaderRows()
  {
    return $this->headerRows;
  }
  /**
   * Optional. The character used to quote column values. Accepts '"' (double
   * quotation mark) or ''' (single quotation mark). Defaults to '"' (double
   * quotation mark) if unspecified.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1StorageFormatCsvOptions::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1StorageFormatCsvOptions');
