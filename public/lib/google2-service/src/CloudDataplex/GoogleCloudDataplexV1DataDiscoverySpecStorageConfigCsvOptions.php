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

class GoogleCloudDataplexV1DataDiscoverySpecStorageConfigCsvOptions extends \Google\Model
{
  /**
   * Optional. The delimiter that is used to separate values. The default is ,
   * (comma).
   *
   * @var string
   */
  public $delimiter;
  /**
   * Optional. The character encoding of the data. The default is UTF-8.
   *
   * @var string
   */
  public $encoding;
  /**
   * Optional. The number of rows to interpret as header rows that should be
   * skipped when reading data rows.
   *
   * @var int
   */
  public $headerRows;
  /**
   * Optional. The character used to quote column values. Accepts " (double
   * quotation mark) or ' (single quotation mark). If unspecified, defaults to "
   * (double quotation mark).
   *
   * @var string
   */
  public $quote;
  /**
   * Optional. Whether to disable the inference of data types for CSV data. If
   * true, all columns are registered as strings.
   *
   * @var bool
   */
  public $typeInferenceDisabled;

  /**
   * Optional. The delimiter that is used to separate values. The default is ,
   * (comma).
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
   * Optional. The character encoding of the data. The default is UTF-8.
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
   * skipped when reading data rows.
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
   * Optional. The character used to quote column values. Accepts " (double
   * quotation mark) or ' (single quotation mark). If unspecified, defaults to "
   * (double quotation mark).
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
   * Optional. Whether to disable the inference of data types for CSV data. If
   * true, all columns are registered as strings.
   *
   * @param bool $typeInferenceDisabled
   */
  public function setTypeInferenceDisabled($typeInferenceDisabled)
  {
    $this->typeInferenceDisabled = $typeInferenceDisabled;
  }
  /**
   * @return bool
   */
  public function getTypeInferenceDisabled()
  {
    return $this->typeInferenceDisabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataDiscoverySpecStorageConfigCsvOptions::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataDiscoverySpecStorageConfigCsvOptions');
