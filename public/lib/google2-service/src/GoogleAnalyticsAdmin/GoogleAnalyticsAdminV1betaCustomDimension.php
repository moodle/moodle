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

class GoogleAnalyticsAdminV1betaCustomDimension extends \Google\Model
{
  /**
   * Scope unknown or not specified.
   */
  public const SCOPE_DIMENSION_SCOPE_UNSPECIFIED = 'DIMENSION_SCOPE_UNSPECIFIED';
  /**
   * Dimension scoped to an event.
   */
  public const SCOPE_EVENT = 'EVENT';
  /**
   * Dimension scoped to a user.
   */
  public const SCOPE_USER = 'USER';
  /**
   * Dimension scoped to eCommerce items
   */
  public const SCOPE_ITEM = 'ITEM';
  /**
   * Optional. Description for this custom dimension. Max length of 150
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. If set to true, sets this dimension as NPA and excludes it from
   * ads personalization. This is currently only supported by user-scoped custom
   * dimensions.
   *
   * @var bool
   */
  public $disallowAdsPersonalization;
  /**
   * Required. Display name for this custom dimension as shown in the Analytics
   * UI. Max length of 82 characters, alphanumeric plus space and underscore
   * starting with a letter. Legacy system-generated display names may contain
   * square brackets, but updates to this field will never permit square
   * brackets.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Resource name for this CustomDimension resource. Format:
   * properties/{property}/customDimensions/{customDimension}
   *
   * @var string
   */
  public $name;
  /**
   * Required. Immutable. Tagging parameter name for this custom dimension. If
   * this is a user-scoped dimension, then this is the user property name. If
   * this is an event-scoped dimension, then this is the event parameter name.
   * If this is an item-scoped dimension, then this is the parameter name found
   * in the eCommerce items array. May only contain alphanumeric and underscore
   * characters, starting with a letter. Max length of 24 characters for user-
   * scoped dimensions, 40 characters for event-scoped dimensions.
   *
   * @var string
   */
  public $parameterName;
  /**
   * Required. Immutable. The scope of this dimension.
   *
   * @var string
   */
  public $scope;

  /**
   * Optional. Description for this custom dimension. Max length of 150
   * characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. If set to true, sets this dimension as NPA and excludes it from
   * ads personalization. This is currently only supported by user-scoped custom
   * dimensions.
   *
   * @param bool $disallowAdsPersonalization
   */
  public function setDisallowAdsPersonalization($disallowAdsPersonalization)
  {
    $this->disallowAdsPersonalization = $disallowAdsPersonalization;
  }
  /**
   * @return bool
   */
  public function getDisallowAdsPersonalization()
  {
    return $this->disallowAdsPersonalization;
  }
  /**
   * Required. Display name for this custom dimension as shown in the Analytics
   * UI. Max length of 82 characters, alphanumeric plus space and underscore
   * starting with a letter. Legacy system-generated display names may contain
   * square brackets, but updates to this field will never permit square
   * brackets.
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
   * Output only. Resource name for this CustomDimension resource. Format:
   * properties/{property}/customDimensions/{customDimension}
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
   * Required. Immutable. Tagging parameter name for this custom dimension. If
   * this is a user-scoped dimension, then this is the user property name. If
   * this is an event-scoped dimension, then this is the event parameter name.
   * If this is an item-scoped dimension, then this is the parameter name found
   * in the eCommerce items array. May only contain alphanumeric and underscore
   * characters, starting with a letter. Max length of 24 characters for user-
   * scoped dimensions, 40 characters for event-scoped dimensions.
   *
   * @param string $parameterName
   */
  public function setParameterName($parameterName)
  {
    $this->parameterName = $parameterName;
  }
  /**
   * @return string
   */
  public function getParameterName()
  {
    return $this->parameterName;
  }
  /**
   * Required. Immutable. The scope of this dimension.
   *
   * Accepted values: DIMENSION_SCOPE_UNSPECIFIED, EVENT, USER, ITEM
   *
   * @param self::SCOPE_* $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return self::SCOPE_*
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaCustomDimension::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaCustomDimension');
