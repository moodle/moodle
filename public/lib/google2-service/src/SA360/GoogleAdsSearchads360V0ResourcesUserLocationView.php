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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesUserLocationView extends \Google\Model
{
  /**
   * Output only. Criterion Id for the country.
   *
   * @var string
   */
  public $countryCriterionId;
  /**
   * Output only. The resource name of the user location view. UserLocation view
   * resource names have the form: `customers/{customer_id}/userLocationViews/{c
   * ountry_criterion_id}~{targeting_location}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. Indicates whether location was targeted or not.
   *
   * @var bool
   */
  public $targetingLocation;

  /**
   * Output only. Criterion Id for the country.
   *
   * @param string $countryCriterionId
   */
  public function setCountryCriterionId($countryCriterionId)
  {
    $this->countryCriterionId = $countryCriterionId;
  }
  /**
   * @return string
   */
  public function getCountryCriterionId()
  {
    return $this->countryCriterionId;
  }
  /**
   * Output only. The resource name of the user location view. UserLocation view
   * resource names have the form: `customers/{customer_id}/userLocationViews/{c
   * ountry_criterion_id}~{targeting_location}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Output only. Indicates whether location was targeted or not.
   *
   * @param bool $targetingLocation
   */
  public function setTargetingLocation($targetingLocation)
  {
    $this->targetingLocation = $targetingLocation;
  }
  /**
   * @return bool
   */
  public function getTargetingLocation()
  {
    return $this->targetingLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesUserLocationView::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesUserLocationView');
