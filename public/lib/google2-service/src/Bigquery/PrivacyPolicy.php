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

namespace Google\Service\Bigquery;

class PrivacyPolicy extends \Google\Model
{
  protected $aggregationThresholdPolicyType = AggregationThresholdPolicy::class;
  protected $aggregationThresholdPolicyDataType = '';
  protected $differentialPrivacyPolicyType = DifferentialPrivacyPolicy::class;
  protected $differentialPrivacyPolicyDataType = '';
  protected $joinRestrictionPolicyType = JoinRestrictionPolicy::class;
  protected $joinRestrictionPolicyDataType = '';

  /**
   * Optional. Policy used for aggregation thresholds.
   *
   * @param AggregationThresholdPolicy $aggregationThresholdPolicy
   */
  public function setAggregationThresholdPolicy(AggregationThresholdPolicy $aggregationThresholdPolicy)
  {
    $this->aggregationThresholdPolicy = $aggregationThresholdPolicy;
  }
  /**
   * @return AggregationThresholdPolicy
   */
  public function getAggregationThresholdPolicy()
  {
    return $this->aggregationThresholdPolicy;
  }
  /**
   * Optional. Policy used for differential privacy.
   *
   * @param DifferentialPrivacyPolicy $differentialPrivacyPolicy
   */
  public function setDifferentialPrivacyPolicy(DifferentialPrivacyPolicy $differentialPrivacyPolicy)
  {
    $this->differentialPrivacyPolicy = $differentialPrivacyPolicy;
  }
  /**
   * @return DifferentialPrivacyPolicy
   */
  public function getDifferentialPrivacyPolicy()
  {
    return $this->differentialPrivacyPolicy;
  }
  /**
   * Optional. Join restriction policy is outside of the one of policies, since
   * this policy can be set along with other policies. This policy gives data
   * providers the ability to enforce joins on the 'join_allowed_columns' when
   * data is queried from a privacy protected view.
   *
   * @param JoinRestrictionPolicy $joinRestrictionPolicy
   */
  public function setJoinRestrictionPolicy(JoinRestrictionPolicy $joinRestrictionPolicy)
  {
    $this->joinRestrictionPolicy = $joinRestrictionPolicy;
  }
  /**
   * @return JoinRestrictionPolicy
   */
  public function getJoinRestrictionPolicy()
  {
    return $this->joinRestrictionPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrivacyPolicy::class, 'Google_Service_Bigquery_PrivacyPolicy');
