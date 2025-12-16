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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1Rule extends \Google\Collection
{
  /**
   * Trigger for unknown action.
   */
  public const TRIGGER_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Trigger for create document action.
   */
  public const TRIGGER_TYPE_ON_CREATE = 'ON_CREATE';
  /**
   * Trigger for update document action.
   */
  public const TRIGGER_TYPE_ON_UPDATE = 'ON_UPDATE';
  /**
   * Trigger for create link action.
   */
  public const TRIGGER_TYPE_ON_CREATE_LINK = 'ON_CREATE_LINK';
  /**
   * Trigger for delete link action.
   */
  public const TRIGGER_TYPE_ON_DELETE_LINK = 'ON_DELETE_LINK';
  protected $collection_key = 'actions';
  protected $actionsType = GoogleCloudContentwarehouseV1Action::class;
  protected $actionsDataType = 'array';
  /**
   * Represents the conditional expression to be evaluated. Expression should
   * evaluate to a boolean result. When the condition is true actions are
   * executed. Example: user_role = "hsbc_role_1" AND doc.salary > 20000
   *
   * @var string
   */
  public $condition;
  /**
   * Short description of the rule and its context.
   *
   * @var string
   */
  public $description;
  /**
   * ID of the rule. It has to be unique across all the examples. This is
   * managed internally.
   *
   * @var string
   */
  public $ruleId;
  /**
   * Identifies the trigger type for running the policy.
   *
   * @var string
   */
  public $triggerType;

  /**
   * List of actions that are executed when the rule is satisfied.
   *
   * @param GoogleCloudContentwarehouseV1Action[] $actions
   */
  public function setActions($actions)
  {
    $this->actions = $actions;
  }
  /**
   * @return GoogleCloudContentwarehouseV1Action[]
   */
  public function getActions()
  {
    return $this->actions;
  }
  /**
   * Represents the conditional expression to be evaluated. Expression should
   * evaluate to a boolean result. When the condition is true actions are
   * executed. Example: user_role = "hsbc_role_1" AND doc.salary > 20000
   *
   * @param string $condition
   */
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return string
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Short description of the rule and its context.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * ID of the rule. It has to be unique across all the examples. This is
   * managed internally.
   *
   * @param string $ruleId
   */
  public function setRuleId($ruleId)
  {
    $this->ruleId = $ruleId;
  }
  /**
   * @return string
   */
  public function getRuleId()
  {
    return $this->ruleId;
  }
  /**
   * Identifies the trigger type for running the policy.
   *
   * Accepted values: UNKNOWN, ON_CREATE, ON_UPDATE, ON_CREATE_LINK,
   * ON_DELETE_LINK
   *
   * @param self::TRIGGER_TYPE_* $triggerType
   */
  public function setTriggerType($triggerType)
  {
    $this->triggerType = $triggerType;
  }
  /**
   * @return self::TRIGGER_TYPE_*
   */
  public function getTriggerType()
  {
    return $this->triggerType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1Rule::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1Rule');
