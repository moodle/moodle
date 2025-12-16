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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaConversionEventDefaultConversionValue extends \Google\Model
{
  /**
   * When a conversion event for this event_name has no set currency, this
   * currency will be applied as the default. Must be in ISO 4217 currency code
   * format. See https://en.wikipedia.org/wiki/ISO_4217 for more information.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * This value will be used to populate the value for all conversions of the
   * specified event_name where the event "value" parameter is unset.
   *
   * @var 
   */
  public $value;

  /**
   * When a conversion event for this event_name has no set currency, this
   * currency will be applied as the default. Must be in ISO 4217 currency code
   * format. See https://en.wikipedia.org/wiki/ISO_4217 for more information.
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
  public function setValue($value)
  {
    $this->value = $value;
  }
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaConversionEventDefaultConversionValue::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaConversionEventDefaultConversionValue');
