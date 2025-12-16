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

namespace Google\Service\FirebaseDynamicLinks;

class GetIosPostInstallAttributionRequest extends \Google\Model
{
  /**
   * Unknown method.
   */
  public const RETRIEVAL_METHOD_UNKNOWN_PAYLOAD_RETRIEVAL_METHOD = 'UNKNOWN_PAYLOAD_RETRIEVAL_METHOD';
  /**
   * iSDK performs a server lookup by device heuristics in the background when
   * app is first-opened; no API called by developer.
   */
  public const RETRIEVAL_METHOD_IMPLICIT_WEAK_MATCH = 'IMPLICIT_WEAK_MATCH';
  /**
   * iSDK performs a server lookup by device heuristics upon a dev API call.
   */
  public const RETRIEVAL_METHOD_EXPLICIT_WEAK_MATCH = 'EXPLICIT_WEAK_MATCH';
  /**
   * iSDK performs a strong match only if weak match is found upon a dev API
   * call.
   */
  public const RETRIEVAL_METHOD_EXPLICIT_STRONG_AFTER_WEAK_MATCH = 'EXPLICIT_STRONG_AFTER_WEAK_MATCH';
  /**
   * Unknown style.
   */
  public const VISUAL_STYLE_UNKNOWN_VISUAL_STYLE = 'UNKNOWN_VISUAL_STYLE';
  /**
   * Default style.
   */
  public const VISUAL_STYLE_DEFAULT_STYLE = 'DEFAULT_STYLE';
  /**
   * Custom style.
   */
  public const VISUAL_STYLE_CUSTOM_STYLE = 'CUSTOM_STYLE';
  /**
   * App installation epoch time (https://en.wikipedia.org/wiki/Unix_time). This
   * is a client signal for a more accurate weak match.
   *
   * @var string
   */
  public $appInstallationTime;
  /**
   * APP bundle ID.
   *
   * @var string
   */
  public $bundleId;
  protected $deviceType = DeviceInfo::class;
  protected $deviceDataType = '';
  /**
   * iOS version, ie: 9.3.5. Consider adding "build".
   *
   * @var string
   */
  public $iosVersion;
  /**
   * App post install attribution retrieval information. Disambiguates mechanism
   * (iSDK or developer invoked) to retrieve payload from clicked link.
   *
   * @var string
   */
  public $retrievalMethod;
  /**
   * Google SDK version. Version takes the form "$major.$minor.$patch"
   *
   * @var string
   */
  public $sdkVersion;
  /**
   * Possible unique matched link that server need to check before performing
   * device heuristics match. If passed link is short server need to expand the
   * link. If link is long server need to vslidate the link.
   *
   * @var string
   */
  public $uniqueMatchLinkToCheck;
  /**
   * Strong match page information. Disambiguates between default UI and custom
   * page to present when strong match succeeds/fails to find cookie.
   *
   * @var string
   */
  public $visualStyle;

  /**
   * App installation epoch time (https://en.wikipedia.org/wiki/Unix_time). This
   * is a client signal for a more accurate weak match.
   *
   * @param string $appInstallationTime
   */
  public function setAppInstallationTime($appInstallationTime)
  {
    $this->appInstallationTime = $appInstallationTime;
  }
  /**
   * @return string
   */
  public function getAppInstallationTime()
  {
    return $this->appInstallationTime;
  }
  /**
   * APP bundle ID.
   *
   * @param string $bundleId
   */
  public function setBundleId($bundleId)
  {
    $this->bundleId = $bundleId;
  }
  /**
   * @return string
   */
  public function getBundleId()
  {
    return $this->bundleId;
  }
  /**
   * Device information.
   *
   * @param DeviceInfo $device
   */
  public function setDevice(DeviceInfo $device)
  {
    $this->device = $device;
  }
  /**
   * @return DeviceInfo
   */
  public function getDevice()
  {
    return $this->device;
  }
  /**
   * iOS version, ie: 9.3.5. Consider adding "build".
   *
   * @param string $iosVersion
   */
  public function setIosVersion($iosVersion)
  {
    $this->iosVersion = $iosVersion;
  }
  /**
   * @return string
   */
  public function getIosVersion()
  {
    return $this->iosVersion;
  }
  /**
   * App post install attribution retrieval information. Disambiguates mechanism
   * (iSDK or developer invoked) to retrieve payload from clicked link.
   *
   * Accepted values: UNKNOWN_PAYLOAD_RETRIEVAL_METHOD, IMPLICIT_WEAK_MATCH,
   * EXPLICIT_WEAK_MATCH, EXPLICIT_STRONG_AFTER_WEAK_MATCH
   *
   * @param self::RETRIEVAL_METHOD_* $retrievalMethod
   */
  public function setRetrievalMethod($retrievalMethod)
  {
    $this->retrievalMethod = $retrievalMethod;
  }
  /**
   * @return self::RETRIEVAL_METHOD_*
   */
  public function getRetrievalMethod()
  {
    return $this->retrievalMethod;
  }
  /**
   * Google SDK version. Version takes the form "$major.$minor.$patch"
   *
   * @param string $sdkVersion
   */
  public function setSdkVersion($sdkVersion)
  {
    $this->sdkVersion = $sdkVersion;
  }
  /**
   * @return string
   */
  public function getSdkVersion()
  {
    return $this->sdkVersion;
  }
  /**
   * Possible unique matched link that server need to check before performing
   * device heuristics match. If passed link is short server need to expand the
   * link. If link is long server need to vslidate the link.
   *
   * @param string $uniqueMatchLinkToCheck
   */
  public function setUniqueMatchLinkToCheck($uniqueMatchLinkToCheck)
  {
    $this->uniqueMatchLinkToCheck = $uniqueMatchLinkToCheck;
  }
  /**
   * @return string
   */
  public function getUniqueMatchLinkToCheck()
  {
    return $this->uniqueMatchLinkToCheck;
  }
  /**
   * Strong match page information. Disambiguates between default UI and custom
   * page to present when strong match succeeds/fails to find cookie.
   *
   * Accepted values: UNKNOWN_VISUAL_STYLE, DEFAULT_STYLE, CUSTOM_STYLE
   *
   * @param self::VISUAL_STYLE_* $visualStyle
   */
  public function setVisualStyle($visualStyle)
  {
    $this->visualStyle = $visualStyle;
  }
  /**
   * @return self::VISUAL_STYLE_*
   */
  public function getVisualStyle()
  {
    return $this->visualStyle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetIosPostInstallAttributionRequest::class, 'Google_Service_FirebaseDynamicLinks_GetIosPostInstallAttributionRequest');
