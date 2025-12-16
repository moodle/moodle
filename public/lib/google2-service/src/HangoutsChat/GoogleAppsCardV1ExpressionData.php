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

namespace Google\Service\HangoutsChat;

class GoogleAppsCardV1ExpressionData extends \Google\Collection
{
  protected $collection_key = 'eventActions';
  protected $conditionsType = GoogleAppsCardV1Condition::class;
  protected $conditionsDataType = 'array';
  protected $eventActionsType = GoogleAppsCardV1EventAction::class;
  protected $eventActionsDataType = 'array';
  /**
   * The uncompiled expression.
   *
   * @var string
   */
  public $expression;
  /**
   * The unique identifier of the ExpressionData.
   *
   * @var string
   */
  public $id;

  /**
   * The list of conditions that are determined by the expression evaluation
   * result.
   *
   * @param GoogleAppsCardV1Condition[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return GoogleAppsCardV1Condition[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * The list of actions that the ExpressionData can be used.
   *
   * @param GoogleAppsCardV1EventAction[] $eventActions
   */
  public function setEventActions($eventActions)
  {
    $this->eventActions = $eventActions;
  }
  /**
   * @return GoogleAppsCardV1EventAction[]
   */
  public function getEventActions()
  {
    return $this->eventActions;
  }
  /**
   * The uncompiled expression.
   *
   * @param string $expression
   */
  public function setExpression($expression)
  {
    $this->expression = $expression;
  }
  /**
   * @return string
   */
  public function getExpression()
  {
    return $this->expression;
  }
  /**
   * The unique identifier of the ExpressionData.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1ExpressionData::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1ExpressionData');
