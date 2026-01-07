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

class TargetingRuleScope extends \Google\Model
{
  protected $anySubscriptionInAppType = TargetingRuleScopeAnySubscriptionInApp::class;
  protected $anySubscriptionInAppDataType = '';
  /**
   * The scope of the current targeting rule is the subscription with the
   * specified subscription ID. Must be a subscription within the same parent
   * app.
   *
   * @var string
   */
  public $specificSubscriptionInApp;
  protected $thisSubscriptionType = TargetingRuleScopeThisSubscription::class;
  protected $thisSubscriptionDataType = '';

  /**
   * The scope of the current targeting rule is any subscription in the parent
   * app.
   *
   * @param TargetingRuleScopeAnySubscriptionInApp $anySubscriptionInApp
   */
  public function setAnySubscriptionInApp(TargetingRuleScopeAnySubscriptionInApp $anySubscriptionInApp)
  {
    $this->anySubscriptionInApp = $anySubscriptionInApp;
  }
  /**
   * @return TargetingRuleScopeAnySubscriptionInApp
   */
  public function getAnySubscriptionInApp()
  {
    return $this->anySubscriptionInApp;
  }
  /**
   * The scope of the current targeting rule is the subscription with the
   * specified subscription ID. Must be a subscription within the same parent
   * app.
   *
   * @param string $specificSubscriptionInApp
   */
  public function setSpecificSubscriptionInApp($specificSubscriptionInApp)
  {
    $this->specificSubscriptionInApp = $specificSubscriptionInApp;
  }
  /**
   * @return string
   */
  public function getSpecificSubscriptionInApp()
  {
    return $this->specificSubscriptionInApp;
  }
  /**
   * The scope of the current targeting rule is the subscription in which this
   * offer is defined.
   *
   * @param TargetingRuleScopeThisSubscription $thisSubscription
   */
  public function setThisSubscription(TargetingRuleScopeThisSubscription $thisSubscription)
  {
    $this->thisSubscription = $thisSubscription;
  }
  /**
   * @return TargetingRuleScopeThisSubscription
   */
  public function getThisSubscription()
  {
    return $this->thisSubscription;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetingRuleScope::class, 'Google_Service_AndroidPublisher_TargetingRuleScope');
