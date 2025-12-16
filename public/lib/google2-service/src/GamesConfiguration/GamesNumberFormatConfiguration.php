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

namespace Google\Service\GamesConfiguration;

class GamesNumberFormatConfiguration extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const NUMBER_FORMAT_TYPE_NUMBER_FORMAT_TYPE_UNSPECIFIED = 'NUMBER_FORMAT_TYPE_UNSPECIFIED';
  /**
   * Numbers are formatted to have no digits or fixed number of digits after the
   * decimal point according to locale. An optional custom unit can be added.
   */
  public const NUMBER_FORMAT_TYPE_NUMERIC = 'NUMERIC';
  /**
   * Numbers are formatted to hours, minutes and seconds.
   */
  public const NUMBER_FORMAT_TYPE_TIME_DURATION = 'TIME_DURATION';
  /**
   * Numbers are formatted to currency according to locale.
   */
  public const NUMBER_FORMAT_TYPE_CURRENCY = 'CURRENCY';
  /**
   * The curreny code string. Only used for CURRENCY format type.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * The number of decimal places for number. Only used for NUMERIC format type.
   *
   * @var int
   */
  public $numDecimalPlaces;
  /**
   * The formatting for the number.
   *
   * @var string
   */
  public $numberFormatType;
  protected $suffixType = GamesNumberAffixConfiguration::class;
  protected $suffixDataType = '';

  /**
   * The curreny code string. Only used for CURRENCY format type.
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * The number of decimal places for number. Only used for NUMERIC format type.
   *
   * @param int $numDecimalPlaces
   */
  public function setNumDecimalPlaces($numDecimalPlaces)
  {
    $this->numDecimalPlaces = $numDecimalPlaces;
  }
  /**
   * @return int
   */
  public function getNumDecimalPlaces()
  {
    return $this->numDecimalPlaces;
  }
  /**
   * The formatting for the number.
   *
   * Accepted values: NUMBER_FORMAT_TYPE_UNSPECIFIED, NUMERIC, TIME_DURATION,
   * CURRENCY
   *
   * @param self::NUMBER_FORMAT_TYPE_* $numberFormatType
   */
  public function setNumberFormatType($numberFormatType)
  {
    $this->numberFormatType = $numberFormatType;
  }
  /**
   * @return self::NUMBER_FORMAT_TYPE_*
   */
  public function getNumberFormatType()
  {
    return $this->numberFormatType;
  }
  /**
   * An optional suffix for the NUMERIC format type. These strings follow the
   * same plural rules as all Android string resources.
   *
   * @param GamesNumberAffixConfiguration $suffix
   */
  public function setSuffix(GamesNumberAffixConfiguration $suffix)
  {
    $this->suffix = $suffix;
  }
  /**
   * @return GamesNumberAffixConfiguration
   */
  public function getSuffix()
  {
    return $this->suffix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GamesNumberFormatConfiguration::class, 'Google_Service_GamesConfiguration_GamesNumberFormatConfiguration');
