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

class UpgradeTargetingRule extends \Google\Model
{
  /**
   * The specific billing period duration, specified in ISO 8601 format, that a
   * user must be currently subscribed to to be eligible for this rule. If not
   * specified, users subscribed to any billing period are matched.
   *
   * @var string
   */
  public $billingPeriodDuration;
  /**
   * Limit this offer to only once per user. If set to true, a user can never be
   * eligible for this offer again if they ever subscribed to this offer.
   *
   * @var bool
   */
  public $oncePerUser;
  protected $scopeType = TargetingRuleScope::class;
  protected $scopeDataType = '';

  /**
   * The specific billing period duration, specified in ISO 8601 format, that a
   * user must be currently subscribed to to be eligible for this rule. If not
   * specified, users subscribed to any billing period are matched.
   *
   * @param string $billingPeriodDuration
   */
  public function setBillingPeriodDuration($billingPeriodDuration)
  {
    $this->billingPeriodDuration = $billingPeriodDuration;
  }
  /**
   * @return string
   */
  public function getBillingPeriodDuration()
  {
    return $this->billingPeriodDuration;
  }
  /**
   * Limit this offer to only once per user. If set to true, a user can never be
   * eligible for this offer again if they ever subscribed to this offer.
   *
   * @param bool $oncePerUser
   */
  public function setOncePerUser($oncePerUser)
  {
    $this->oncePerUser = $oncePerUser;
  }
  /**
   * @return bool
   */
  public function getOncePerUser()
  {
    return $this->oncePerUser;
  }
  /**
   * Required. The scope of subscriptions this rule considers. Only allows "this
   * subscription" and "specific subscription in app".
   *
   * @param TargetingRuleScope $scope
   */
  public function setScope(TargetingRuleScope $scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return TargetingRuleScope
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpgradeTargetingRule::class, 'Google_Service_AndroidPublisher_UpgradeTargetingRule');
