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

namespace Google\Service\CloudDeploy;

class AutomationRuleCondition extends \Google\Model
{
  protected $targetsPresentConditionType = TargetsPresentCondition::class;
  protected $targetsPresentConditionDataType = '';
  protected $timedPromoteReleaseConditionType = TimedPromoteReleaseCondition::class;
  protected $timedPromoteReleaseConditionDataType = '';

  /**
   * Optional. Details around targets enumerated in the rule.
   *
   * @param TargetsPresentCondition $targetsPresentCondition
   */
  public function setTargetsPresentCondition(TargetsPresentCondition $targetsPresentCondition)
  {
    $this->targetsPresentCondition = $targetsPresentCondition;
  }
  /**
   * @return TargetsPresentCondition
   */
  public function getTargetsPresentCondition()
  {
    return $this->targetsPresentCondition;
  }
  /**
   * Optional. TimedPromoteReleaseCondition contains rule conditions specific to
   * a an Automation with a timed promote release rule defined.
   *
   * @param TimedPromoteReleaseCondition $timedPromoteReleaseCondition
   */
  public function setTimedPromoteReleaseCondition(TimedPromoteReleaseCondition $timedPromoteReleaseCondition)
  {
    $this->timedPromoteReleaseCondition = $timedPromoteReleaseCondition;
  }
  /**
   * @return TimedPromoteReleaseCondition
   */
  public function getTimedPromoteReleaseCondition()
  {
    return $this->timedPromoteReleaseCondition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutomationRuleCondition::class, 'Google_Service_CloudDeploy_AutomationRuleCondition');
