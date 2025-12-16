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

namespace Google\Service\AndroidPublisher;

class UpdateSubscriptionRequest extends \Google\Model
{
  /**
   * Defaults to PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_SENSITIVE.
   */
  public const LATENCY_TOLERANCE_PRODUCT_UPDATE_LATENCY_TOLERANCE_UNSPECIFIED = 'PRODUCT_UPDATE_LATENCY_TOLERANCE_UNSPECIFIED';
  /**
   * The update will propagate to clients within several minutes on average and
   * up to a few hours in rare cases. Throughput is limited to 7,200 updates per
   * app per hour.
   */
  public const LATENCY_TOLERANCE_PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_SENSITIVE = 'PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_SENSITIVE';
  /**
   * The update will propagate to clients within 24 hours. Supports high
   * throughput of up to 720,000 updates per app per hour using batch
   * modification methods.
   */
  public const LATENCY_TOLERANCE_PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_TOLERANT = 'PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_TOLERANT';
  /**
   * Optional. If set to true, and the subscription with the given package_name
   * and product_id doesn't exist, the subscription will be created. If a new
   * subscription is created, update_mask is ignored.
   *
   * @var bool
   */
  public $allowMissing;
  /**
   * Optional. The latency tolerance for the propagation of this product update.
   * Defaults to latency-sensitive.
   *
   * @var string
   */
  public $latencyTolerance;
  protected $regionsVersionType = RegionsVersion::class;
  protected $regionsVersionDataType = '';
  protected $subscriptionType = Subscription::class;
  protected $subscriptionDataType = '';
  /**
   * Required. The list of fields to be updated.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Optional. If set to true, and the subscription with the given package_name
   * and product_id doesn't exist, the subscription will be created. If a new
   * subscription is created, update_mask is ignored.
   *
   * @param bool $allowMissing
   */
  public function setAllowMissing($allowMissing)
  {
    $this->allowMissing = $allowMissing;
  }
  /**
   * @return bool
   */
  public function getAllowMissing()
  {
    return $this->allowMissing;
  }
  /**
   * Optional. The latency tolerance for the propagation of this product update.
   * Defaults to latency-sensitive.
   *
   * Accepted values: PRODUCT_UPDATE_LATENCY_TOLERANCE_UNSPECIFIED,
   * PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_SENSITIVE,
   * PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_TOLERANT
   *
   * @param self::LATENCY_TOLERANCE_* $latencyTolerance
   */
  public function setLatencyTolerance($latencyTolerance)
  {
    $this->latencyTolerance = $latencyTolerance;
  }
  /**
   * @return self::LATENCY_TOLERANCE_*
   */
  public function getLatencyTolerance()
  {
    return $this->latencyTolerance;
  }
  /**
   * Required. The version of the available regions being used for the
   * subscription.
   *
   * @param RegionsVersion $regionsVersion
   */
  public function setRegionsVersion(RegionsVersion $regionsVersion)
  {
    $this->regionsVersion = $regionsVersion;
  }
  /**
   * @return RegionsVersion
   */
  public function getRegionsVersion()
  {
    return $this->regionsVersion;
  }
  /**
   * Required. The subscription to update.
   *
   * @param Subscription $subscription
   */
  public function setSubscription(Subscription $subscription)
  {
    $this->subscription = $subscription;
  }
  /**
   * @return Subscription
   */
  public function getSubscription()
  {
    return $this->subscription;
  }
  /**
   * Required. The list of fields to be updated.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateSubscriptionRequest::class, 'Google_Service_AndroidPublisher_UpdateSubscriptionRequest');
