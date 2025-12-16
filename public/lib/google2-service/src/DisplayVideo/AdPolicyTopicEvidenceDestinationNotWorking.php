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

class AdPolicyTopicEvidenceDestinationNotWorking extends \Google\Model
{
  /**
   * Not specified or unknown.
   */
  public const DEVICE_AD_POLICY_TOPIC_EVIDENCE_DESTINATION_NOT_WORKING_DEVICE_TYPE_UNKNOWN = 'AD_POLICY_TOPIC_EVIDENCE_DESTINATION_NOT_WORKING_DEVICE_TYPE_UNKNOWN';
  /**
   * Desktop device.
   */
  public const DEVICE_DESKTOP = 'DESKTOP';
  /**
   * Android device.
   */
  public const DEVICE_ANDROID = 'ANDROID';
  /**
   * iOS device.
   */
  public const DEVICE_IOS = 'IOS';
  /**
   * Not specified or unknown.
   */
  public const DNS_ERROR_TYPE_AD_POLICY_TOPIC_EVIDENCE_DESTINATION_NOT_WORKING_DNS_ERROR_TYPE_UNKNOWN = 'AD_POLICY_TOPIC_EVIDENCE_DESTINATION_NOT_WORKING_DNS_ERROR_TYPE_UNKNOWN';
  /**
   * Host name not found in DNS when fetching landing page.
   */
  public const DNS_ERROR_TYPE_HOSTNAME_NOT_FOUND = 'HOSTNAME_NOT_FOUND';
  /**
   * Google could not crawl the landing page when communicating with DNS.
   */
  public const DNS_ERROR_TYPE_GOOGLE_CRAWLER_DNS_ISSUE = 'GOOGLE_CRAWLER_DNS_ISSUE';
  /**
   * The device where visiting the URL resulted in the error.
   *
   * @var string
   */
  public $device;
  /**
   * The type of DNS error.
   *
   * @var string
   */
  public $dnsErrorType;
  /**
   * The full URL that didn't work.
   *
   * @var string
   */
  public $expandedUri;
  /**
   * The HTTP error code.
   *
   * @var string
   */
  public $httpErrorCode;
  /**
   * The last time the error was seen when navigating to URL.
   *
   * @var string
   */
  public $lastCheckedTime;

  /**
   * The device where visiting the URL resulted in the error.
   *
   * Accepted values:
   * AD_POLICY_TOPIC_EVIDENCE_DESTINATION_NOT_WORKING_DEVICE_TYPE_UNKNOWN,
   * DESKTOP, ANDROID, IOS
   *
   * @param self::DEVICE_* $device
   */
  public function setDevice($device)
  {
    $this->device = $device;
  }
  /**
   * @return self::DEVICE_*
   */
  public function getDevice()
  {
    return $this->device;
  }
  /**
   * The type of DNS error.
   *
   * Accepted values:
   * AD_POLICY_TOPIC_EVIDENCE_DESTINATION_NOT_WORKING_DNS_ERROR_TYPE_UNKNOWN,
   * HOSTNAME_NOT_FOUND, GOOGLE_CRAWLER_DNS_ISSUE
   *
   * @param self::DNS_ERROR_TYPE_* $dnsErrorType
   */
  public function setDnsErrorType($dnsErrorType)
  {
    $this->dnsErrorType = $dnsErrorType;
  }
  /**
   * @return self::DNS_ERROR_TYPE_*
   */
  public function getDnsErrorType()
  {
    return $this->dnsErrorType;
  }
  /**
   * The full URL that didn't work.
   *
   * @param string $expandedUri
   */
  public function setExpandedUri($expandedUri)
  {
    $this->expandedUri = $expandedUri;
  }
  /**
   * @return string
   */
  public function getExpandedUri()
  {
    return $this->expandedUri;
  }
  /**
   * The HTTP error code.
   *
   * @param string $httpErrorCode
   */
  public function setHttpErrorCode($httpErrorCode)
  {
    $this->httpErrorCode = $httpErrorCode;
  }
  /**
   * @return string
   */
  public function getHttpErrorCode()
  {
    return $this->httpErrorCode;
  }
  /**
   * The last time the error was seen when navigating to URL.
   *
   * @param string $lastCheckedTime
   */
  public function setLastCheckedTime($lastCheckedTime)
  {
    $this->lastCheckedTime = $lastCheckedTime;
  }
  /**
   * @return string
   */
  public function getLastCheckedTime()
  {
    return $this->lastCheckedTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdPolicyTopicEvidenceDestinationNotWorking::class, 'Google_Service_DisplayVideo_AdPolicyTopicEvidenceDestinationNotWorking');
