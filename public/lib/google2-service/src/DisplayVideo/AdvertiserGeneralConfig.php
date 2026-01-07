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

class AdvertiserGeneralConfig extends \Google\Model
{
  /**
   * Required. Immutable. Advertiser's currency in ISO 4217 format. Accepted
   * codes and the currencies they represent are: Currency Code : Currency Name
   * * `ARS` : Argentine Peso * `AUD` : Australian Dollar * `BRL` : Brazilian
   * Real * `CAD` : Canadian Dollar * `CHF` : Swiss Franc * `CLP` : Chilean Peso
   * * `CNY` : Chinese Yuan * `COP` : Colombian Peso * `CZK` : Czech Koruna *
   * `DKK` : Danish Krone * `EGP` : Egyption Pound * `EUR` : Euro * `GBP` :
   * British Pound * `HKD` : Hong Kong Dollar * `HUF` : Hungarian Forint * `IDR`
   * : Indonesian Rupiah * `ILS` : Israeli Shekel * `INR` : Indian Rupee * `JPY`
   * : Japanese Yen * `KRW` : South Korean Won * `MXN` : Mexican Pesos * `MYR` :
   * Malaysian Ringgit * `NGN` : Nigerian Naira * `NOK` : Norwegian Krone *
   * `NZD` : New Zealand Dollar * `PEN` : Peruvian Nuevo Sol * `PLN` : Polish
   * Zloty * `RON` : New Romanian Leu * `RUB` : Russian Ruble * `SEK` : Swedish
   * Krona * `TRY` : Turkish Lira * `TWD` : New Taiwan Dollar * `USD` : US
   * Dollar * `ZAR` : South African Rand
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Required. The domain URL of the advertiser's primary website. The system
   * will send this information to publishers that require website URL to
   * associate a campaign with an advertiser. Provide a URL with no path or
   * query string, beginning with `http:` or `https:`. For example,
   * http://www.example.com
   *
   * @var string
   */
  public $domainUrl;
  /**
   * Output only. The standard TZ database name of the advertiser's time zone.
   * For example, `America/New_York`. See more at:
   * https://en.wikipedia.org/wiki/List_of_tz_database_time_zones For CM360
   * hybrid advertisers, the time zone is the same as that of the associated
   * CM360 account; for third-party only advertisers, the time zone is the same
   * as that of the parent partner.
   *
   * @var string
   */
  public $timeZone;

  /**
   * Required. Immutable. Advertiser's currency in ISO 4217 format. Accepted
   * codes and the currencies they represent are: Currency Code : Currency Name
   * * `ARS` : Argentine Peso * `AUD` : Australian Dollar * `BRL` : Brazilian
   * Real * `CAD` : Canadian Dollar * `CHF` : Swiss Franc * `CLP` : Chilean Peso
   * * `CNY` : Chinese Yuan * `COP` : Colombian Peso * `CZK` : Czech Koruna *
   * `DKK` : Danish Krone * `EGP` : Egyption Pound * `EUR` : Euro * `GBP` :
   * British Pound * `HKD` : Hong Kong Dollar * `HUF` : Hungarian Forint * `IDR`
   * : Indonesian Rupiah * `ILS` : Israeli Shekel * `INR` : Indian Rupee * `JPY`
   * : Japanese Yen * `KRW` : South Korean Won * `MXN` : Mexican Pesos * `MYR` :
   * Malaysian Ringgit * `NGN` : Nigerian Naira * `NOK` : Norwegian Krone *
   * `NZD` : New Zealand Dollar * `PEN` : Peruvian Nuevo Sol * `PLN` : Polish
   * Zloty * `RON` : New Romanian Leu * `RUB` : Russian Ruble * `SEK` : Swedish
   * Krona * `TRY` : Turkish Lira * `TWD` : New Taiwan Dollar * `USD` : US
   * Dollar * `ZAR` : South African Rand
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
   * Required. The domain URL of the advertiser's primary website. The system
   * will send this information to publishers that require website URL to
   * associate a campaign with an advertiser. Provide a URL with no path or
   * query string, beginning with `http:` or `https:`. For example,
   * http://www.example.com
   *
   * @param string $domainUrl
   */
  public function setDomainUrl($domainUrl)
  {
    $this->domainUrl = $domainUrl;
  }
  /**
   * @return string
   */
  public function getDomainUrl()
  {
    return $this->domainUrl;
  }
  /**
   * Output only. The standard TZ database name of the advertiser's time zone.
   * For example, `America/New_York`. See more at:
   * https://en.wikipedia.org/wiki/List_of_tz_database_time_zones For CM360
   * hybrid advertisers, the time zone is the same as that of the associated
   * CM360 account; for third-party only advertisers, the time zone is the same
   * as that of the parent partner.
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
class_alias(AdvertiserGeneralConfig::class, 'Google_Service_DisplayVideo_AdvertiserGeneralConfig');
