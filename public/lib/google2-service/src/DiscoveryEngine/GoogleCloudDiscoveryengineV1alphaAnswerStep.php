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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaAnswerStep extends \Google\Collection
{
  /**
   * Unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Step is currently in progress.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Step currently failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Step has succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  protected $collection_key = 'actions';
  protected $actionsType = GoogleCloudDiscoveryengineV1alphaAnswerStepAction::class;
  protected $actionsDataType = 'array';
  /**
   * The description of the step.
   *
   * @var string
   */
  public $description;
  /**
   * The state of the step.
   *
   * @var string
   */
  public $state;
  /**
   * The thought of the step.
   *
   * @var string
   */
  public $thought;

  /**
   * Actions.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAnswerStepAction[] $actions
   */
  public function setActions($actions)
  {
    $this->actions = $actions;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAnswerStepAction[]
   */
  public function getActions()
  {
    return $this->actions;
  }
  /**
   * The description of the step.
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
   * The state of the step.
   *
   * Accepted values: STATE_UNSPECIFIED, IN_PROGRESS, FAILED, SUCCEEDED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The thought of the step.
   *
   * @param string $thought
   */
  public function setThought($thought)
  {
    $this->thought = $thought;
  }
  /**
   * @return string
   */
  public function getThought()
  {
    return $this->thought;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaAnswerStep::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaAnswerStep');
