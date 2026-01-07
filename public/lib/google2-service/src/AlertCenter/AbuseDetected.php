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

namespace Google\Service\AlertCenter;

class AbuseDetected extends \Google\Model
{
  /**
   * AbuseDetected alert variation type unspecified. No alert should be
   * unspecified.
   */
  public const VARIATION_TYPE_ABUSE_DETECTED_VARIATION_TYPE_UNSPECIFIED = 'ABUSE_DETECTED_VARIATION_TYPE_UNSPECIFIED';
  /**
   * Variation displayed for Drive abusive content alerts.
   */
  public const VARIATION_TYPE_DRIVE_ABUSIVE_CONTENT = 'DRIVE_ABUSIVE_CONTENT';
  /**
   * Variation displayed for Limited Disable alerts, when a Google service is
   * disabled for a user, totally or partially, due to the user's abusive
   * behavior.
   */
  public const VARIATION_TYPE_LIMITED_DISABLE = 'LIMITED_DISABLE';
  protected $additionalDetailsType = EntityList::class;
  protected $additionalDetailsDataType = '';
  /**
   * Product that the abuse is originating from.
   *
   * @var string
   */
  public $product;
  /**
   * Unique identifier of each sub alert that is onboarded.
   *
   * @var string
   */
  public $subAlertId;
  /**
   * Variation of AbuseDetected alerts. The variation_type determines the texts
   * displayed the alert details. This differs from sub_alert_id because each
   * sub alert can have multiple variation_types, representing different stages
   * of the alert.
   *
   * @var string
   */
  public $variationType;

  /**
   * List of abusive users/entities to be displayed in a table in the alert.
   *
   * @param EntityList $additionalDetails
   */
  public function setAdditionalDetails(EntityList $additionalDetails)
  {
    $this->additionalDetails = $additionalDetails;
  }
  /**
   * @return EntityList
   */
  public function getAdditionalDetails()
  {
    return $this->additionalDetails;
  }
  /**
   * Product that the abuse is originating from.
   *
   * @param string $product
   */
  public function setProduct($product)
  {
    $this->product = $product;
  }
  /**
   * @return string
   */
  public function getProduct()
  {
    return $this->product;
  }
  /**
   * Unique identifier of each sub alert that is onboarded.
   *
   * @param string $subAlertId
   */
  public function setSubAlertId($subAlertId)
  {
    $this->subAlertId = $subAlertId;
  }
  /**
   * @return string
   */
  public function getSubAlertId()
  {
    return $this->subAlertId;
  }
  /**
   * Variation of AbuseDetected alerts. The variation_type determines the texts
   * displayed the alert details. This differs from sub_alert_id because each
   * sub alert can have multiple variation_types, representing different stages
   * of the alert.
   *
   * Accepted values: ABUSE_DETECTED_VARIATION_TYPE_UNSPECIFIED,
   * DRIVE_ABUSIVE_CONTENT, LIMITED_DISABLE
   *
   * @param self::VARIATION_TYPE_* $variationType
   */
  public function setVariationType($variationType)
  {
    $this->variationType = $variationType;
  }
  /**
   * @return self::VARIATION_TYPE_*
   */
  public function getVariationType()
  {
    return $this->variationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AbuseDetected::class, 'Google_Service_AlertCenter_AbuseDetected');
