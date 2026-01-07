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

class PropertyUsage extends \Google\Model
{
  /**
   * Unknown or unspecified property type
   */
  public const PROPERTY_TYPE_ANALYTICS_PROPERTY_TYPE_UNSPECIFIED = 'ANALYTICS_PROPERTY_TYPE_UNSPECIFIED';
  /**
   * Ordinary Google Analytics property
   */
  public const PROPERTY_TYPE_ANALYTICS_PROPERTY_TYPE_ORDINARY = 'ANALYTICS_PROPERTY_TYPE_ORDINARY';
  /**
   * Google Analytics subproperty
   */
  public const PROPERTY_TYPE_ANALYTICS_PROPERTY_TYPE_SUBPROPERTY = 'ANALYTICS_PROPERTY_TYPE_SUBPROPERTY';
  /**
   * Google Analytics rollup property
   */
  public const PROPERTY_TYPE_ANALYTICS_PROPERTY_TYPE_ROLLUP = 'ANALYTICS_PROPERTY_TYPE_ROLLUP';
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
   * The ID of the property's parent account.
   *
   * @var string
   */
  public $accountId;
  /**
   * The number of events for which the property is billed in the requested
   * month.
   *
   * @var string
   */
  public $billableEventCount;
  /**
   * The display name of the property.
   *
   * @var string
   */
  public $displayName;
  /**
   * The name of the Google Analytics Admin API property resource. Format:
   * analyticsadmin.googleapis.com/properties/{property_id}
   *
   * @var string
   */
  public $property;
  /**
   * The subtype of the analytics property. This affects the billable event
   * count.
   *
   * @var string
   */
  public $propertyType;
  /**
   * The service level of the property.
   *
   * @var string
   */
  public $serviceLevel;
  /**
   * Total event count that the property received during the requested month.
   *
   * @var string
   */
  public $totalEventCount;

  /**
   * The ID of the property's parent account.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * The number of events for which the property is billed in the requested
   * month.
   *
   * @param string $billableEventCount
   */
  public function setBillableEventCount($billableEventCount)
  {
    $this->billableEventCount = $billableEventCount;
  }
  /**
   * @return string
   */
  public function getBillableEventCount()
  {
    return $this->billableEventCount;
  }
  /**
   * The display name of the property.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The name of the Google Analytics Admin API property resource. Format:
   * analyticsadmin.googleapis.com/properties/{property_id}
   *
   * @param string $property
   */
  public function setProperty($property)
  {
    $this->property = $property;
  }
  /**
   * @return string
   */
  public function getProperty()
  {
    return $this->property;
  }
  /**
   * The subtype of the analytics property. This affects the billable event
   * count.
   *
   * Accepted values: ANALYTICS_PROPERTY_TYPE_UNSPECIFIED,
   * ANALYTICS_PROPERTY_TYPE_ORDINARY, ANALYTICS_PROPERTY_TYPE_SUBPROPERTY,
   * ANALYTICS_PROPERTY_TYPE_ROLLUP
   *
   * @param self::PROPERTY_TYPE_* $propertyType
   */
  public function setPropertyType($propertyType)
  {
    $this->propertyType = $propertyType;
  }
  /**
   * @return self::PROPERTY_TYPE_*
   */
  public function getPropertyType()
  {
    return $this->propertyType;
  }
  /**
   * The service level of the property.
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
  /**
   * Total event count that the property received during the requested month.
   *
   * @param string $totalEventCount
   */
  public function setTotalEventCount($totalEventCount)
  {
    $this->totalEventCount = $totalEventCount;
  }
  /**
   * @return string
   */
  public function getTotalEventCount()
  {
    return $this->totalEventCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PropertyUsage::class, 'Google_Service_GoogleMarketingPlatformAdminAPI_PropertyUsage');
