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

namespace Google\Service\Adsense;

class AdClient extends \Google\Model
{
  /**
   * State unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The ad client is ready to show ads.
   */
  public const STATE_READY = 'READY';
  /**
   * Running some checks on the ad client before it is ready to serve ads.
   */
  public const STATE_GETTING_READY = 'GETTING_READY';
  /**
   * The ad client hasn't been checked yet. There are tasks pending before
   * AdSense will start the review.
   */
  public const STATE_REQUIRES_REVIEW = 'REQUIRES_REVIEW';
  /**
   * Output only. Resource name of the ad client. Format:
   * accounts/{account}/adclients/{adclient}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Reporting product code of the ad client. For example, "AFC"
   * for AdSense for Content. Corresponds to the `PRODUCT_CODE` dimension, and
   * present only if the ad client supports reporting.
   *
   * @var string
   */
  public $productCode;
  /**
   * Output only. Unique ID of the ad client as used in the `AD_CLIENT_ID`
   * reporting dimension. Present only if the ad client supports reporting.
   *
   * @var string
   */
  public $reportingDimensionId;
  /**
   * Output only. State of the ad client.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Resource name of the ad client. Format:
   * accounts/{account}/adclients/{adclient}
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
   * Output only. Reporting product code of the ad client. For example, "AFC"
   * for AdSense for Content. Corresponds to the `PRODUCT_CODE` dimension, and
   * present only if the ad client supports reporting.
   *
   * @param string $productCode
   */
  public function setProductCode($productCode)
  {
    $this->productCode = $productCode;
  }
  /**
   * @return string
   */
  public function getProductCode()
  {
    return $this->productCode;
  }
  /**
   * Output only. Unique ID of the ad client as used in the `AD_CLIENT_ID`
   * reporting dimension. Present only if the ad client supports reporting.
   *
   * @param string $reportingDimensionId
   */
  public function setReportingDimensionId($reportingDimensionId)
  {
    $this->reportingDimensionId = $reportingDimensionId;
  }
  /**
   * @return string
   */
  public function getReportingDimensionId()
  {
    return $this->reportingDimensionId;
  }
  /**
   * Output only. State of the ad client.
   *
   * Accepted values: STATE_UNSPECIFIED, READY, GETTING_READY, REQUIRES_REVIEW
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdClient::class, 'Google_Service_Adsense_AdClient');
