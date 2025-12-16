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

namespace Google\Service\Sheets;

class TextToColumnsRequest extends \Google\Model
{
  /**
   * Default value. This value must not be used.
   */
  public const DELIMITER_TYPE_DELIMITER_TYPE_UNSPECIFIED = 'DELIMITER_TYPE_UNSPECIFIED';
  /**
   * ","
   */
  public const DELIMITER_TYPE_COMMA = 'COMMA';
  /**
   * ";"
   */
  public const DELIMITER_TYPE_SEMICOLON = 'SEMICOLON';
  /**
   * "."
   */
  public const DELIMITER_TYPE_PERIOD = 'PERIOD';
  /**
   * " "
   */
  public const DELIMITER_TYPE_SPACE = 'SPACE';
  /**
   * A custom value as defined in delimiter.
   */
  public const DELIMITER_TYPE_CUSTOM = 'CUSTOM';
  /**
   * Automatically detect columns.
   */
  public const DELIMITER_TYPE_AUTODETECT = 'AUTODETECT';
  /**
   * The delimiter to use. Used only if delimiterType is CUSTOM.
   *
   * @var string
   */
  public $delimiter;
  /**
   * The delimiter type to use.
   *
   * @var string
   */
  public $delimiterType;
  protected $sourceType = GridRange::class;
  protected $sourceDataType = '';

  /**
   * The delimiter to use. Used only if delimiterType is CUSTOM.
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
   * The delimiter type to use.
   *
   * Accepted values: DELIMITER_TYPE_UNSPECIFIED, COMMA, SEMICOLON, PERIOD,
   * SPACE, CUSTOM, AUTODETECT
   *
   * @param self::DELIMITER_TYPE_* $delimiterType
   */
  public function setDelimiterType($delimiterType)
  {
    $this->delimiterType = $delimiterType;
  }
  /**
   * @return self::DELIMITER_TYPE_*
   */
  public function getDelimiterType()
  {
    return $this->delimiterType;
  }
  /**
   * The source data range. This must span exactly one column.
   *
   * @param GridRange $source
   */
  public function setSource(GridRange $source)
  {
    $this->source = $source;
  }
  /**
   * @return GridRange
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TextToColumnsRequest::class, 'Google_Service_Sheets_TextToColumnsRequest');
