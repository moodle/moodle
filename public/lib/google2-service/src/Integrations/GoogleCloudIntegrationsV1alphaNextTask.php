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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaNextTask extends \Google\Model
{
  /**
   * Standard filter expression for this task to become an eligible next task.
   *
   * @var string
   */
  public $condition;
  /**
   * User-provided description intended to give additional business context
   * about the task.
   *
   * @var string
   */
  public $description;
  /**
   * User-provided label that is attached to this edge in the UI.
   *
   * @var string
   */
  public $displayName;
  /**
   * ID of the next task.
   *
   * @var string
   */
  public $taskConfigId;
  /**
   * Task number of the next task.
   *
   * @var string
   */
  public $taskId;

  /**
   * Standard filter expression for this task to become an eligible next task.
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
   * User-provided description intended to give additional business context
   * about the task.
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
   * User-provided label that is attached to this edge in the UI.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * ID of the next task.
   *
   * @param string $taskConfigId
   */
  public function setTaskConfigId($taskConfigId)
  {
    $this->taskConfigId = $taskConfigId;
  }
  /**
   * @return string
   */
  public function getTaskConfigId()
  {
    return $this->taskConfigId;
  }
  /**
   * Task number of the next task.
   *
   * @param string $taskId
   */
  public function setTaskId($taskId)
  {
    $this->taskId = $taskId;
  }
  /**
   * @return string
   */
  public function getTaskId()
  {
    return $this->taskId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaNextTask::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaNextTask');
