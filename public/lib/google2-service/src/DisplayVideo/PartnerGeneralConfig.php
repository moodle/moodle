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

namespace Google\Service\DisplayVideo;

class PartnerGeneralConfig extends \Google\Model
{
  /**
   * Immutable. Partner's currency in ISO 4217 format.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Immutable. The standard TZ database name of the partner's time zone. For
   * example, `America/New_York`. See more at:
   * https://en.wikipedia.org/wiki/List_of_tz_database_time_zones
   *
   * @var string
   */
  public $timeZone;

  /**
   * Immutable. Partner's currency in ISO 4217 format.
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
   * Immutable. The standard TZ database name of the partner's time zone. For
   * example, `America/New_York`. See more at:
   * https://en.wikipedia.org/wiki/List_of_tz_database_time_zones
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartnerGeneralConfig::class, 'Google_Service_DisplayVideo_PartnerGeneralConfig');
