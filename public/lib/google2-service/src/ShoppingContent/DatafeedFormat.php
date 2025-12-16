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

namespace Google\Service\ShoppingContent;

class DatafeedFormat extends \Google\Model
{
  /**
   * Delimiter for the separation of values in a delimiter-separated values
   * feed. If not specified, the delimiter will be auto-detected. Ignored for
   * non-DSV data feeds. Acceptable values are: - "`pipe`" - "`tab`" - "`tilde`"
   *
   * @var string
   */
  public $columnDelimiter;
  /**
   * Character encoding scheme of the data feed. If not specified, the encoding
   * will be auto-detected. Acceptable values are: - "`latin-1`" - "`utf-16be`"
   * - "`utf-16le`" - "`utf-8`" - "`windows-1252`"
   *
   * @var string
   */
  public $fileEncoding;
  /**
   * Specifies how double quotes are interpreted. If not specified, the mode
   * will be auto-detected. Ignored for non-DSV data feeds. Acceptable values
   * are: - "`normal character`" - "`value quoting`"
   *
   * @var string
   */
  public $quotingMode;

  /**
   * Delimiter for the separation of values in a delimiter-separated values
   * feed. If not specified, the delimiter will be auto-detected. Ignored for
   * non-DSV data feeds. Acceptable values are: - "`pipe`" - "`tab`" - "`tilde`"
   *
   * @param string $columnDelimiter
   */
  public function setColumnDelimiter($columnDelimiter)
  {
    $this->columnDelimiter = $columnDelimiter;
  }
  /**
   * @return string
   */
  public function getColumnDelimiter()
  {
    return $this->columnDelimiter;
  }
  /**
   * Character encoding scheme of the data feed. If not specified, the encoding
   * will be auto-detected. Acceptable values are: - "`latin-1`" - "`utf-16be`"
   * - "`utf-16le`" - "`utf-8`" - "`windows-1252`"
   *
   * @param string $fileEncoding
   */
  public function setFileEncoding($fileEncoding)
  {
    $this->fileEncoding = $fileEncoding;
  }
  /**
   * @return string
   */
  public function getFileEncoding()
  {
    return $this->fileEncoding;
  }
  /**
   * Specifies how double quotes are interpreted. If not specified, the mode
   * will be auto-detected. Ignored for non-DSV data feeds. Acceptable values
   * are: - "`normal character`" - "`value quoting`"
   *
   * @param string $quotingMode
   */
  public function setQuotingMode($quotingMode)
  {
    $this->quotingMode = $quotingMode;
  }
  /**
   * @return string
   */
  public function getQuotingMode()
  {
    return $this->quotingMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatafeedFormat::class, 'Google_Service_ShoppingContent_DatafeedFormat');
