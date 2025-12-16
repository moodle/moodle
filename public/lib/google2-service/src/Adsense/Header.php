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

namespace Google\Service\Adsense;

class Header extends \Google\Model
{
  /**
   * Unspecified header.
   */
  public const TYPE_HEADER_TYPE_UNSPECIFIED = 'HEADER_TYPE_UNSPECIFIED';
  /**
   * Dimension header type.
   */
  public const TYPE_DIMENSION = 'DIMENSION';
  /**
   * Tally header type.
   */
  public const TYPE_METRIC_TALLY = 'METRIC_TALLY';
  /**
   * Ratio header type.
   */
  public const TYPE_METRIC_RATIO = 'METRIC_RATIO';
  /**
   * Currency header type.
   */
  public const TYPE_METRIC_CURRENCY = 'METRIC_CURRENCY';
  /**
   * Milliseconds header type.
   */
  public const TYPE_METRIC_MILLISECONDS = 'METRIC_MILLISECONDS';
  /**
   * Decimal header type.
   */
  public const TYPE_METRIC_DECIMAL = 'METRIC_DECIMAL';
  /**
   * The [ISO-4217 currency code](https://en.wikipedia.org/wiki/ISO_4217) of
   * this column. Only present if the header type is METRIC_CURRENCY.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Required. Name of the header.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Type of the header.
   *
   * @var string
   */
  public $type;

  /**
   * The [ISO-4217 currency code](https://en.wikipedia.org/wiki/ISO_4217) of
   * this column. Only present if the header type is METRIC_CURRENCY.
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
   * Required. Name of the header.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. Type of the header.
   *
   * Accepted values: HEADER_TYPE_UNSPECIFIED, DIMENSION, METRIC_TALLY,
   * METRIC_RATIO, METRIC_CURRENCY, METRIC_MILLISECONDS, METRIC_DECIMAL
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Header::class, 'Google_Service_Adsense_Header');
