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

namespace Google\Service\GoogleMarketingPlatformAdminAPI;

class SetPropertyServiceLevelRequest extends \Google\Model
{
  /**
   * Service level unspecified.
   */
  public const SERVICE_LEVEL_ANALYTICS_SERVICE_LEVEL_UNSPECIFIED = 'ANALYTICS_SERVICE_LEVEL_UNSPECIFIED';
  /**
   * The standard version of Google Analytics.
   */
  public const SERVICE_LEVEL_ANALYTICS_SERVICE_LEVEL_STANDARD = 'ANALYTICS_SERVICE_LEVEL_STANDARD';
  /**
   * The premium version of Google Analytics.
   */
  public const SERVICE_LEVEL_ANALYTICS_SERVICE_LEVEL_360 = 'ANALYTICS_SERVICE_LEVEL_360';
  /**
   * Required. The Analytics property to change the ServiceLevel setting. This
   * field is the name of the Google Analytics Admin API property resource.
   * Format: analyticsadmin.googleapis.com/properties/{property_id}
   *
   * @var string
   */
  public $analyticsProperty;
  /**
   * Required. The service level to set for this property.
   *
   * @var string
   */
  public $serviceLevel;

  /**
   * Required. The Analytics property to change the ServiceLevel setting. This
   * field is the name of the Google Analytics Admin API property resource.
   * Format: analyticsadmin.googleapis.com/properties/{property_id}
   *
   * @param string $analyticsProperty
   */
  public function setAnalyticsProperty($analyticsProperty)
  {
    $this->analyticsProperty = $analyticsProperty;
  }
  /**
   * @return string
   */
  public function getAnalyticsProperty()
  {
    return $this->analyticsProperty;
  }
  /**
   * Required. The service level to set for this property.
   *
   * Accepted values: ANALYTICS_SERVICE_LEVEL_UNSPECIFIED,
   * ANALYTICS_SERVICE_LEVEL_STANDARD, ANALYTICS_SERVICE_LEVEL_360
   *
   * @param self::SERVICE_LEVEL_* $serviceLevel
   */
  public function setServiceLevel($serviceLevel)
  {
    $this->serviceLevel = $serviceLevel;
  }
  /**
   * @return self::SERVICE_LEVEL_*
   */
  public function getServiceLevel()
  {
    return $this->serviceLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SetPropertyServiceLevelRequest::class, 'Google_Service_GoogleMarketingPlatformAdminAPI_SetPropertyServiceLevelRequest');
