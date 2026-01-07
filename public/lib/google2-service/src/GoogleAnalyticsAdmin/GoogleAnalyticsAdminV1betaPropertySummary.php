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

class GoogleAnalyticsAdminV1betaPropertySummary extends \Google\Model
{
  /**
   * Unknown or unspecified property type
   */
  public const PROPERTY_TYPE_PROPERTY_TYPE_UNSPECIFIED = 'PROPERTY_TYPE_UNSPECIFIED';
  /**
   * Ordinary Google Analytics property
   */
  public const PROPERTY_TYPE_PROPERTY_TYPE_ORDINARY = 'PROPERTY_TYPE_ORDINARY';
  /**
   * Google Analytics subproperty
   */
  public const PROPERTY_TYPE_PROPERTY_TYPE_SUBPROPERTY = 'PROPERTY_TYPE_SUBPROPERTY';
  /**
   * Google Analytics rollup property
   */
  public const PROPERTY_TYPE_PROPERTY_TYPE_ROLLUP = 'PROPERTY_TYPE_ROLLUP';
  /**
   * Display name for the property referred to in this property summary.
   *
   * @var string
   */
  public $displayName;
  /**
   * Resource name of this property's logical parent. Note: The Property-Moving
   * UI can be used to change the parent. Format: accounts/{account},
   * properties/{property} Example: "accounts/100", "properties/200"
   *
   * @var string
   */
  public $parent;
  /**
   * Resource name of property referred to by this property summary Format:
   * properties/{property_id} Example: "properties/1000"
   *
   * @var string
   */
  public $property;
  /**
   * The property's property type.
   *
   * @var string
   */
  public $propertyType;

  /**
   * Display name for the property referred to in this property summary.
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
   * Resource name of this property's logical parent. Note: The Property-Moving
   * UI can be used to change the parent. Format: accounts/{account},
   * properties/{property} Example: "accounts/100", "properties/200"
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Resource name of property referred to by this property summary Format:
   * properties/{property_id} Example: "properties/1000"
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
   * The property's property type.
   *
   * Accepted values: PROPERTY_TYPE_UNSPECIFIED, PROPERTY_TYPE_ORDINARY,
   * PROPERTY_TYPE_SUBPROPERTY, PROPERTY_TYPE_ROLLUP
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaPropertySummary::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaPropertySummary');
