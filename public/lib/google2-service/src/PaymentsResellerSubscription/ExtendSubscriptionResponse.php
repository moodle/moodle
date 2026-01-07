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

namespace Google\Service\PaymentsResellerSubscription;

class ExtendSubscriptionResponse extends \Google\Model
{
  /**
   * The time at which the subscription is expected to be extended, in ISO 8061
   * format. UTC timezone. Example, "cycleEndTime":"2019-08-31T17:28:54.564Z"
   *
   * @var string
   */
  public $cycleEndTime;
  /**
   * End of the free trial period, in ISO 8061 format. UTC timezone. Example,
   * "freeTrialEndTime":"2019-08-31T17:28:54.564Z" This time will be set the
   * same as initial subscription creation time if no free trial period is
   * offered to the partner.
   *
   * @var string
   */
  public $freeTrialEndTime;
  /**
   * Output only. The time at which the subscription is expected to be renewed
   * by Google - a new charge will be incurred and the service entitlement will
   * be renewed. A non-immediate cancellation will take place at this time too,
   * before which, the service entitlement for the end user will remain valid.
   * UTC timezone in ISO 8061 format. For example: "2019-08-31T17:28:54.564Z"
   *
   * @var string
   */
  public $renewalTime;

  /**
   * The time at which the subscription is expected to be extended, in ISO 8061
   * format. UTC timezone. Example, "cycleEndTime":"2019-08-31T17:28:54.564Z"
   *
   * @param string $cycleEndTime
   */
  public function setCycleEndTime($cycleEndTime)
  {
    $this->cycleEndTime = $cycleEndTime;
  }
  /**
   * @return string
   */
  public function getCycleEndTime()
  {
    return $this->cycleEndTime;
  }
  /**
   * End of the free trial period, in ISO 8061 format. UTC timezone. Example,
   * "freeTrialEndTime":"2019-08-31T17:28:54.564Z" This time will be set the
   * same as initial subscription creation time if no free trial period is
   * offered to the partner.
   *
   * @param string $freeTrialEndTime
   */
  public function setFreeTrialEndTime($freeTrialEndTime)
  {
    $this->freeTrialEndTime = $freeTrialEndTime;
  }
  /**
   * @return string
   */
  public function getFreeTrialEndTime()
  {
    return $this->freeTrialEndTime;
  }
  /**
   * Output only. The time at which the subscription is expected to be renewed
   * by Google - a new charge will be incurred and the service entitlement will
   * be renewed. A non-immediate cancellation will take place at this time too,
   * before which, the service entitlement for the end user will remain valid.
   * UTC timezone in ISO 8061 format. For example: "2019-08-31T17:28:54.564Z"
   *
   * @param string $renewalTime
   */
  public function setRenewalTime($renewalTime)
  {
    $this->renewalTime = $renewalTime;
  }
  /**
   * @return string
   */
  public function getRenewalTime()
  {
    return $this->renewalTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExtendSubscriptionResponse::class, 'Google_Service_PaymentsResellerSubscription_ExtendSubscriptionResponse');
