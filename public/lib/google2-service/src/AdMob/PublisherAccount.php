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

namespace Google\Service\AdMob;

class PublisherAccount extends \Google\Model
{
  /**
   * Currency code of the earning-related metrics, which is the 3-letter code
   * defined in ISO 4217. The daily average rate is used for the currency
   * conversion.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Resource name of this account. Format is accounts/{publisher_id}.
   *
   * @var string
   */
  public $name;
  /**
   * The unique ID by which this publisher account can be identified in the API
   * requests (for example, pub-1234567890).
   *
   * @var string
   */
  public $publisherId;
  /**
   * The time zone that is used in reports that are generated for this account.
   * The value is a time-zone ID as specified by the CLDR project, for example,
   * "America/Los_Angeles".
   *
   * @var string
   */
  public $reportingTimeZone;

  /**
   * Currency code of the earning-related metrics, which is the 3-letter code
   * defined in ISO 4217. The daily average rate is used for the currency
   * conversion.
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
   * Resource name of this account. Format is accounts/{publisher_id}.
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
   * The unique ID by which this publisher account can be identified in the API
   * requests (for example, pub-1234567890).
   *
   * @param string $publisherId
   */
  public function setPublisherId($publisherId)
  {
    $this->publisherId = $publisherId;
  }
  /**
   * @return string
   */
  public function getPublisherId()
  {
    return $this->publisherId;
  }
  /**
   * The time zone that is used in reports that are generated for this account.
   * The value is a time-zone ID as specified by the CLDR project, for example,
   * "America/Los_Angeles".
   *
   * @param string $reportingTimeZone
   */
  public function setReportingTimeZone($reportingTimeZone)
  {
    $this->reportingTimeZone = $reportingTimeZone;
  }
  /**
   * @return string
   */
  public function getReportingTimeZone()
  {
    return $this->reportingTimeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PublisherAccount::class, 'Google_Service_AdMob_PublisherAccount');
