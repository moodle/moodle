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

namespace Google\Service\Css;

class Price extends \Google\Model
{
  /**
   * The price represented as a number in micros (1 million micros is an
   * equivalent to one's currency standard unit, for example, 1 USD = 1000000
   * micros).
   *
   * @var string
   */
  public $amountMicros;
  /**
   * The currency of the price using three-letter acronyms according to [ISO
   * 4217](http://en.wikipedia.org/wiki/ISO_4217).
   *
   * @var string
   */
  public $currencyCode;

  /**
   * The price represented as a number in micros (1 million micros is an
   * equivalent to one's currency standard unit, for example, 1 USD = 1000000
   * micros).
   *
   * @param string $amountMicros
   */
  public function setAmountMicros($amountMicros)
  {
    $this->amountMicros = $amountMicros;
  }
  /**
   * @return string
   */
  public function getAmountMicros()
  {
    return $this->amountMicros;
  }
  /**
   * The currency of the price using three-letter acronyms according to [ISO
   * 4217](http://en.wikipedia.org/wiki/ISO_4217).
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Price::class, 'Google_Service_Css_Price');
