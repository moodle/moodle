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

namespace Google\Service\Reseller;

class SubscriptionPlan extends \Google\Model
{
  protected $commitmentIntervalType = SubscriptionPlanCommitmentInterval::class;
  protected $commitmentIntervalDataType = '';
  /**
   * The `isCommitmentPlan` property's boolean value identifies the plan as an
   * annual commitment plan: - `true` — The subscription's plan is an annual
   * commitment plan. - `false` — The plan is not an annual commitment plan.
   *
   * @var bool
   */
  public $isCommitmentPlan;
  /**
   * The `planName` property is required. This is the name of the subscription's
   * plan. For more information about the Google payment plans, see the API
   * concepts. Possible values are: - `ANNUAL_MONTHLY_PAY` — The annual
   * commitment plan with monthly payments. *Caution: *`ANNUAL_MONTHLY_PAY` is
   * returned as `ANNUAL` in all API responses. - `ANNUAL_YEARLY_PAY` — The
   * annual commitment plan with yearly payments - `FLEXIBLE` — The flexible
   * plan - `TRIAL` — The 30-day free trial plan. A subscription in trial will
   * be suspended after the 30th free day if no payment plan is assigned.
   * Calling `changePlan` will assign a payment plan to a trial but will not
   * activate the plan. A trial will automatically begin its assigned payment
   * plan after its 30th free day or immediately after calling
   * `startPaidService`. - `FREE` — The free plan is exclusive to the Cloud
   * Identity SKU and does not incur any billing.
   *
   * @var string
   */
  public $planName;

  /**
   * In this version of the API, annual commitment plan's interval is one year.
   * *Note: *When `billingMethod` value is `OFFLINE`, the subscription property
   * object `plan.commitmentInterval` is omitted in all API responses.
   *
   * @param SubscriptionPlanCommitmentInterval $commitmentInterval
   */
  public function setCommitmentInterval(SubscriptionPlanCommitmentInterval $commitmentInterval)
  {
    $this->commitmentInterval = $commitmentInterval;
  }
  /**
   * @return SubscriptionPlanCommitmentInterval
   */
  public function getCommitmentInterval()
  {
    return $this->commitmentInterval;
  }
  /**
   * The `isCommitmentPlan` property's boolean value identifies the plan as an
   * annual commitment plan: - `true` — The subscription's plan is an annual
   * commitment plan. - `false` — The plan is not an annual commitment plan.
   *
   * @param bool $isCommitmentPlan
   */
  public function setIsCommitmentPlan($isCommitmentPlan)
  {
    $this->isCommitmentPlan = $isCommitmentPlan;
  }
  /**
   * @return bool
   */
  public function getIsCommitmentPlan()
  {
    return $this->isCommitmentPlan;
  }
  /**
   * The `planName` property is required. This is the name of the subscription's
   * plan. For more information about the Google payment plans, see the API
   * concepts. Possible values are: - `ANNUAL_MONTHLY_PAY` — The annual
   * commitment plan with monthly payments. *Caution: *`ANNUAL_MONTHLY_PAY` is
   * returned as `ANNUAL` in all API responses. - `ANNUAL_YEARLY_PAY` — The
   * annual commitment plan with yearly payments - `FLEXIBLE` — The flexible
   * plan - `TRIAL` — The 30-day free trial plan. A subscription in trial will
   * be suspended after the 30th free day if no payment plan is assigned.
   * Calling `changePlan` will assign a payment plan to a trial but will not
   * activate the plan. A trial will automatically begin its assigned payment
   * plan after its 30th free day or immediately after calling
   * `startPaidService`. - `FREE` — The free plan is exclusive to the Cloud
   * Identity SKU and does not incur any billing.
   *
   * @param string $planName
   */
  public function setPlanName($planName)
  {
    $this->planName = $planName;
  }
  /**
   * @return string
   */
  public function getPlanName()
  {
    return $this->planName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscriptionPlan::class, 'Google_Service_Reseller_SubscriptionPlan');
