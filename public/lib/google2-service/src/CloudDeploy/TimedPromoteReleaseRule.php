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

class TimedPromoteReleaseRule extends \Google\Model
{
  protected $conditionType = AutomationRuleCondition::class;
  protected $conditionDataType = '';
  /**
   * Optional. The starting phase of the rollout created by this rule. Default
   * to the first phase.
   *
   * @var string
   */
  public $destinationPhase;
  /**
   * Optional. The ID of the stage in the pipeline to which this `Release` is
   * deploying. If unspecified, default it to the next stage in the promotion
   * flow. The value of this field could be one of the following: * The last
   * segment of a target name * "@next", the next target in the promotion
   * sequence
   *
   * @var string
   */
  public $destinationTargetId;
  /**
   * Required. ID of the rule. This ID must be unique in the `Automation`
   * resource to which this rule belongs. The format is
   * `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`.
   *
   * @var string
   */
  public $id;
  /**
   * Required. Schedule in crontab format. e.g. "0 9 * * 1" for every Monday at
   * 9am.
   *
   * @var string
   */
  public $schedule;
  /**
   * Required. The time zone in IANA format [IANA Time Zone
   * Database](https://www.iana.org/time-zones) (e.g. America/New_York).
   *
   * @var string
   */
  public $timeZone;

  /**
   * Output only. Information around the state of the Automation rule.
   *
   * @param AutomationRuleCondition $condition
   */
  public function setCondition(AutomationRuleCondition $condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return AutomationRuleCondition
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Optional. The starting phase of the rollout created by this rule. Default
   * to the first phase.
   *
   * @param string $destinationPhase
   */
  public function setDestinationPhase($destinationPhase)
  {
    $this->destinationPhase = $destinationPhase;
  }
  /**
   * @return string
   */
  public function getDestinationPhase()
  {
    return $this->destinationPhase;
  }
  /**
   * Optional. The ID of the stage in the pipeline to which this `Release` is
   * deploying. If unspecified, default it to the next stage in the promotion
   * flow. The value of this field could be one of the following: * The last
   * segment of a target name * "@next", the next target in the promotion
   * sequence
   *
   * @param string $destinationTargetId
   */
  public function setDestinationTargetId($destinationTargetId)
  {
    $this->destinationTargetId = $destinationTargetId;
  }
  /**
   * @return string
   */
  public function getDestinationTargetId()
  {
    return $this->destinationTargetId;
  }
  /**
   * Required. ID of the rule. This ID must be unique in the `Automation`
   * resource to which this rule belongs. The format is
   * `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Required. Schedule in crontab format. e.g. "0 9 * * 1" for every Monday at
   * 9am.
   *
   * @param string $schedule
   */
  public function setSchedule($schedule)
  {
    $this->schedule = $schedule;
  }
  /**
   * @return string
   */
  public function getSchedule()
  {
    return $this->schedule;
  }
  /**
   * Required. The time zone in IANA format [IANA Time Zone
   * Database](https://www.iana.org/time-zones) (e.g. America/New_York).
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TimedPromoteReleaseRule::class, 'Google_Service_CloudDeploy_TimedPromoteReleaseRule');
