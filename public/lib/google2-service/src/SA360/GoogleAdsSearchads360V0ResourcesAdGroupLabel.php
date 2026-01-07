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

class GoogleAdsSearchads360V0ResourcesAdGroupLabel extends \Google\Model
{
  /**
   * Immutable. The ad group to which the label is attached.
   *
   * @var string
   */
  public $adGroup;
  /**
   * Immutable. The label assigned to the ad group.
   *
   * @var string
   */
  public $label;
  /**
   * Output only. The ID of the Customer which owns the label.
   *
   * @var string
   */
  public $ownerCustomerId;
  /**
   * Immutable. The resource name of the ad group label. Ad group label resource
   * names have the form:
   * `customers/{owner_customer_id}/adGroupLabels/{ad_group_id}~{label_id}`
   *
   * @var string
   */
  public $resourceName;

  /**
   * Immutable. The ad group to which the label is attached.
   *
   * @param string $adGroup
   */
  public function setAdGroup($adGroup)
  {
    $this->adGroup = $adGroup;
  }
  /**
   * @return string
   */
  public function getAdGroup()
  {
    return $this->adGroup;
  }
  /**
   * Immutable. The label assigned to the ad group.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * Output only. The ID of the Customer which owns the label.
   *
   * @param string $ownerCustomerId
   */
  public function setOwnerCustomerId($ownerCustomerId)
  {
    $this->ownerCustomerId = $ownerCustomerId;
  }
  /**
   * @return string
   */
  public function getOwnerCustomerId()
  {
    return $this->ownerCustomerId;
  }
  /**
   * Immutable. The resource name of the ad group label. Ad group label resource
   * names have the form:
   * `customers/{owner_customer_id}/adGroupLabels/{ad_group_id}~{label_id}`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesAdGroupLabel::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAdGroupLabel');
