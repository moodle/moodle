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

class AdvanceRolloutRule extends \Google\Collection
{
  protected $collection_key = 'sourcePhases';
  protected $conditionType = AutomationRuleCondition::class;
  protected $conditionDataType = '';
  /**
   * Required. ID of the rule. This id must be unique in the `Automation`
   * resource to which this rule belongs. The format is
   * `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`.
   *
   * @var string
   */
  public $id;
  /**
   * Optional. Proceeds only after phase name matched any one in the list. This
   * value must consist of lower-case letters, numbers, and hyphens, start with
   * a letter and end with a letter or a number, and have a max length of 63
   * characters. In other words, it must match the following regex:
   * `^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$`.
   *
   * @var string[]
   */
  public $sourcePhases;
  /**
   * Optional. How long to wait after a rollout is finished.
   *
   * @var string
   */
  public $wait;

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
   * Required. ID of the rule. This id must be unique in the `Automation`
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
   * Optional. Proceeds only after phase name matched any one in the list. This
   * value must consist of lower-case letters, numbers, and hyphens, start with
   * a letter and end with a letter or a number, and have a max length of 63
   * characters. In other words, it must match the following regex:
   * `^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$`.
   *
   * @param string[] $sourcePhases
   */
  public function setSourcePhases($sourcePhases)
  {
    $this->sourcePhases = $sourcePhases;
  }
  /**
   * @return string[]
   */
  public function getSourcePhases()
  {
    return $this->sourcePhases;
  }
  /**
   * Optional. How long to wait after a rollout is finished.
   *
   * @param string $wait
   */
  public function setWait($wait)
  {
    $this->wait = $wait;
  }
  /**
   * @return string
   */
  public function getWait()
  {
    return $this->wait;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvanceRolloutRule::class, 'Google_Service_CloudDeploy_AdvanceRolloutRule');
