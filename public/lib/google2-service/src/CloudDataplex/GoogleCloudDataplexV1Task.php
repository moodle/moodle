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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1Task extends \Google\Model
{
  /**
   * State is not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Resource is active, i.e., ready to use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Resource is under creation.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Resource is under deletion.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Resource is active but has unresolved actions.
   */
  public const STATE_ACTION_REQUIRED = 'ACTION_REQUIRED';
  /**
   * Output only. The time when the task was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the task.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. User friendly display name.
   *
   * @var string
   */
  public $displayName;
  protected $executionSpecType = GoogleCloudDataplexV1TaskExecutionSpec::class;
  protected $executionSpecDataType = '';
  protected $executionStatusType = GoogleCloudDataplexV1TaskExecutionStatus::class;
  protected $executionStatusDataType = '';
  /**
   * Optional. User-defined labels for the task.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The relative resource name of the task, of the form:
   * projects/{project_number}/locations/{location_id}/lakes/{lake_id}/
   * tasks/{task_id}.
   *
   * @var string
   */
  public $name;
  protected $notebookType = GoogleCloudDataplexV1TaskNotebookTaskConfig::class;
  protected $notebookDataType = '';
  protected $sparkType = GoogleCloudDataplexV1TaskSparkTaskConfig::class;
  protected $sparkDataType = '';
  /**
   * Output only. Current state of the task.
   *
   * @var string
   */
  public $state;
  protected $triggerSpecType = GoogleCloudDataplexV1TaskTriggerSpec::class;
  protected $triggerSpecDataType = '';
  /**
   * Output only. System generated globally unique ID for the task. This ID will
   * be different if the task is deleted and re-created with the same name.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the task was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time when the task was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Description of the task.
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
   * Optional. User friendly display name.
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
   * Required. Spec related to how a task is executed.
   *
   * @param GoogleCloudDataplexV1TaskExecutionSpec $executionSpec
   */
  public function setExecutionSpec(GoogleCloudDataplexV1TaskExecutionSpec $executionSpec)
  {
    $this->executionSpec = $executionSpec;
  }
  /**
   * @return GoogleCloudDataplexV1TaskExecutionSpec
   */
  public function getExecutionSpec()
  {
    return $this->executionSpec;
  }
  /**
   * Output only. Status of the latest task executions.
   *
   * @param GoogleCloudDataplexV1TaskExecutionStatus $executionStatus
   */
  public function setExecutionStatus(GoogleCloudDataplexV1TaskExecutionStatus $executionStatus)
  {
    $this->executionStatus = $executionStatus;
  }
  /**
   * @return GoogleCloudDataplexV1TaskExecutionStatus
   */
  public function getExecutionStatus()
  {
    return $this->executionStatus;
  }
  /**
   * Optional. User-defined labels for the task.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The relative resource name of the task, of the form:
   * projects/{project_number}/locations/{location_id}/lakes/{lake_id}/
   * tasks/{task_id}.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Config related to running scheduled Notebooks.
   *
   * @param GoogleCloudDataplexV1TaskNotebookTaskConfig $notebook
   */
  public function setNotebook(GoogleCloudDataplexV1TaskNotebookTaskConfig $notebook)
  {
    $this->notebook = $notebook;
  }
  /**
   * @return GoogleCloudDataplexV1TaskNotebookTaskConfig
   */
  public function getNotebook()
  {
    return $this->notebook;
  }
  /**
   * Config related to running custom Spark tasks.
   *
   * @param GoogleCloudDataplexV1TaskSparkTaskConfig $spark
   */
  public function setSpark(GoogleCloudDataplexV1TaskSparkTaskConfig $spark)
  {
    $this->spark = $spark;
  }
  /**
   * @return GoogleCloudDataplexV1TaskSparkTaskConfig
   */
  public function getSpark()
  {
    return $this->spark;
  }
  /**
   * Output only. Current state of the task.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING, DELETING,
   * ACTION_REQUIRED
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
   * Required. Spec related to how often and when a task should be triggered.
   *
   * @param GoogleCloudDataplexV1TaskTriggerSpec $triggerSpec
   */
  public function setTriggerSpec(GoogleCloudDataplexV1TaskTriggerSpec $triggerSpec)
  {
    $this->triggerSpec = $triggerSpec;
  }
  /**
   * @return GoogleCloudDataplexV1TaskTriggerSpec
   */
  public function getTriggerSpec()
  {
    return $this->triggerSpec;
  }
  /**
   * Output only. System generated globally unique ID for the task. This ID will
   * be different if the task is deleted and re-created with the same name.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The time when the task was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1Task::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1Task');
