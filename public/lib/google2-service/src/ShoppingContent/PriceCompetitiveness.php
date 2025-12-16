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

class PriceCompetitiveness extends \Google\Model
{
  /**
   * The price benchmark currency (ISO 4217 code).
   *
   * @var string
   */
  public $benchmarkPriceCurrencyCode;
  /**
   * The latest available price benchmark in micros (1 millionth of a standard
   * unit, 1 USD = 1000000 micros) for the product's catalog in the benchmark
   * country.
   *
   * @var string
   */
  public $benchmarkPriceMicros;
  /**
   * The country of the price benchmark (ISO 3166 code).
   *
   * @var string
   */
  public $countryCode;

  /**
   * The price benchmark currency (ISO 4217 code).
   *
   * @param string $benchmarkPriceCurrencyCode
   */
  public function setBenchmarkPriceCurrencyCode($benchmarkPriceCurrencyCode)
  {
    $this->benchmarkPriceCurrencyCode = $benchmarkPriceCurrencyCode;
  }
  /**
   * @return string
   */
  public function getBenchmarkPriceCurrencyCode()
  {
    return $this->benchmarkPriceCurrencyCode;
  }
  /**
   * The latest available price benchmark in micros (1 millionth of a standard
   * unit, 1 USD = 1000000 micros) for the product's catalog in the benchmark
   * country.
   *
   * @param string $benchmarkPriceMicros
   */
  public function setBenchmarkPriceMicros($benchmarkPriceMicros)
  {
    $this->benchmarkPriceMicros = $benchmarkPriceMicros;
  }
  /**
   * @return string
   */
  public function getBenchmarkPriceMicros()
  {
    return $this->benchmarkPriceMicros;
  }
  /**
   * The country of the price benchmark (ISO 3166 code).
   *
   * @param string $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PriceCompetitiveness::class, 'Google_Service_ShoppingContent_PriceCompetitiveness');
