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

namespace Google\Service\DisplayVideo;

class CampaignGoal extends \Google\Model
{
  /**
   * Goal value is not specified or unknown in this version.
   */
  public const CAMPAIGN_GOAL_TYPE_CAMPAIGN_GOAL_TYPE_UNSPECIFIED = 'CAMPAIGN_GOAL_TYPE_UNSPECIFIED';
  /**
   * Drive app installs or engagements.
   */
  public const CAMPAIGN_GOAL_TYPE_CAMPAIGN_GOAL_TYPE_APP_INSTALL = 'CAMPAIGN_GOAL_TYPE_APP_INSTALL';
  /**
   * Raise awareness of a brand or product.
   */
  public const CAMPAIGN_GOAL_TYPE_CAMPAIGN_GOAL_TYPE_BRAND_AWARENESS = 'CAMPAIGN_GOAL_TYPE_BRAND_AWARENESS';
  /**
   * Drive offline or in-store sales.
   */
  public const CAMPAIGN_GOAL_TYPE_CAMPAIGN_GOAL_TYPE_OFFLINE_ACTION = 'CAMPAIGN_GOAL_TYPE_OFFLINE_ACTION';
  /**
   * Drive online action or visits.
   */
  public const CAMPAIGN_GOAL_TYPE_CAMPAIGN_GOAL_TYPE_ONLINE_ACTION = 'CAMPAIGN_GOAL_TYPE_ONLINE_ACTION';
  /**
   * Required. The type of the campaign goal.
   *
   * @var string
   */
  public $campaignGoalType;
  protected $performanceGoalType = PerformanceGoal::class;
  protected $performanceGoalDataType = '';

  /**
   * Required. The type of the campaign goal.
   *
   * Accepted values: CAMPAIGN_GOAL_TYPE_UNSPECIFIED,
   * CAMPAIGN_GOAL_TYPE_APP_INSTALL, CAMPAIGN_GOAL_TYPE_BRAND_AWARENESS,
   * CAMPAIGN_GOAL_TYPE_OFFLINE_ACTION, CAMPAIGN_GOAL_TYPE_ONLINE_ACTION
   *
   * @param self::CAMPAIGN_GOAL_TYPE_* $campaignGoalType
   */
  public function setCampaignGoalType($campaignGoalType)
  {
    $this->campaignGoalType = $campaignGoalType;
  }
  /**
   * @return self::CAMPAIGN_GOAL_TYPE_*
   */
  public function getCampaignGoalType()
  {
    return $this->campaignGoalType;
  }
  /**
   * Required. The performance goal of the campaign. Acceptable values for
   * performance_goal_type are: * `PERFORMANCE_GOAL_TYPE_CPM` *
   * `PERFORMANCE_GOAL_TYPE_CPC` * `PERFORMANCE_GOAL_TYPE_CPA` *
   * `PERFORMANCE_GOAL_TYPE_CPIAVC` * `PERFORMANCE_GOAL_TYPE_CTR` *
   * `PERFORMANCE_GOAL_TYPE_VIEWABILITY` * `PERFORMANCE_GOAL_TYPE_OTHER`
   *
   * @param PerformanceGoal $performanceGoal
   */
  public function setPerformanceGoal(PerformanceGoal $performanceGoal)
  {
    $this->performanceGoal = $performanceGoal;
  }
  /**
   * @return PerformanceGoal
   */
  public function getPerformanceGoal()
  {
    return $this->performanceGoal;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CampaignGoal::class, 'Google_Service_DisplayVideo_CampaignGoal');
