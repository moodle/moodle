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

class GoogleAdsSearchads360V0ResourcesAdGroupCriterionEffectiveLabel extends \Google\Model
{
  /**
   * Immutable. The ad group criterion to which the effective label is attached.
   *
   * @var string
   */
  public $adGroupCriterion;
  /**
   * Immutable. The effective label assigned to the ad group criterion.
   *
   * @var string
   */
  public $label;
  /**
   * Output only. The ID of the Customer which owns the effective label.
   *
   * @var string
   */
  public $ownerCustomerId;
  /**
   * Immutable. The resource name of the ad group criterion effective label. Ad
   * group criterion effective label resource names have the form: `customers/{o
   * wner_customer_id}/adGroupCriterionEffectiveLabels/{ad_group_id}~{criterion_
   * id}~{label_id}`
   *
   * @var string
   */
  public $resourceName;

  /**
   * Immutable. The ad group criterion to which the effective label is attached.
   *
   * @param string $adGroupCriterion
   */
  public function setAdGroupCriterion($adGroupCriterion)
  {
    $this->adGroupCriterion = $adGroupCriterion;
  }
  /**
   * @return string
   */
  public function getAdGroupCriterion()
  {
    return $this->adGroupCriterion;
  }
  /**
   * Immutable. The effective label assigned to the ad group criterion.
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
   * Output only. The ID of the Customer which owns the effective label.
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
   * Immutable. The resource name of the ad group criterion effective label. Ad
   * group criterion effective label resource names have the form: `customers/{o
   * wner_customer_id}/adGroupCriterionEffectiveLabels/{ad_group_id}~{criterion_
   * id}~{label_id}`
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
class_alias(GoogleAdsSearchads360V0ResourcesAdGroupCriterionEffectiveLabel::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAdGroupCriterionEffectiveLabel');
