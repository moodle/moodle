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

namespace Google\Service\Batch;

class Job extends \Google\Collection
{
  protected $collection_key = 'taskGroups';
  protected $allocationPolicyType = AllocationPolicy::class;
  protected $allocationPolicyDataType = '';
  /**
   * Output only. When the Job was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Custom labels to apply to the job and any Cloud Logging [LogEntry](https://
   * cloud.google.com/logging/docs/reference/v2/rest/v2/LogEntry) that it
   * generates. Use labels to group and describe the resources they are applied
   * to. Batch automatically applies predefined labels and supports multiple
   * `labels` fields for each job, which each let you apply custom labels to
   * various resources. Label names that start with "goog-" or "google-" are
   * reserved for predefined labels. For more information about labels with
   * Batch, see [Organize resources using
   * labels](https://cloud.google.com/batch/docs/organize-resources-using-
   * labels).
   *
   * @var string[]
   */
  public $labels;
  protected $logsPolicyType = LogsPolicy::class;
  protected $logsPolicyDataType = '';
  /**
   * Output only. Job name. For example: "projects/123456/locations/us-
   * central1/jobs/job01".
   *
   * @var string
   */
  public $name;
  protected $notificationsType = JobNotification::class;
  protected $notificationsDataType = 'array';
  /**
   * Priority of the Job. The valid value range is [0, 100). Default value is 0.
   * Higher value indicates higher priority. A job with higher priority value is
   * more likely to run earlier if all other requirements are satisfied.
   *
   * @var string
   */
  public $priority;
  protected $statusType = JobStatus::class;
  protected $statusDataType = '';
  protected $taskGroupsType = TaskGroup::class;
  protected $taskGroupsDataType = 'array';
  /**
   * Output only. A system generated unique ID for the Job.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The last time the Job was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Compute resource allocation for all TaskGroups in the Job.
   *
   * @param AllocationPolicy $allocationPolicy
   */
  public function setAllocationPolicy(AllocationPolicy $allocationPolicy)
  {
    $this->allocationPolicy = $allocationPolicy;
  }
  /**
   * @return AllocationPolicy
   */
  public function getAllocationPolicy()
  {
    return $this->allocationPolicy;
  }
  /**
   * Output only. When the Job was created.
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
   * Custom labels to apply to the job and any Cloud Logging [LogEntry](https://
   * cloud.google.com/logging/docs/reference/v2/rest/v2/LogEntry) that it
   * generates. Use labels to group and describe the resources they are applied
   * to. Batch automatically applies predefined labels and supports multiple
   * `labels` fields for each job, which each let you apply custom labels to
   * various resources. Label names that start with "goog-" or "google-" are
   * reserved for predefined labels. For more information about labels with
   * Batch, see [Organize resources using
   * labels](https://cloud.google.com/batch/docs/organize-resources-using-
   * labels).
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
   * Log preservation policy for the Job.
   *
   * @param LogsPolicy $logsPolicy
   */
  public function setLogsPolicy(LogsPolicy $logsPolicy)
  {
    $this->logsPolicy = $logsPolicy;
  }
  /**
   * @return LogsPolicy
   */
  public function getLogsPolicy()
  {
    return $this->logsPolicy;
  }
  /**
   * Output only. Job name. For example: "projects/123456/locations/us-
   * central1/jobs/job01".
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
   * Notification configurations.
   *
   * @param JobNotification[] $notifications
   */
  public function setNotifications($notifications)
  {
    $this->notifications = $notifications;
  }
  /**
   * @return JobNotification[]
   */
  public function getNotifications()
  {
    return $this->notifications;
  }
  /**
   * Priority of the Job. The valid value range is [0, 100). Default value is 0.
   * Higher value indicates higher priority. A job with higher priority value is
   * more likely to run earlier if all other requirements are satisfied.
   *
   * @param string $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return string
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * Output only. Job status. It is read only for users.
   *
   * @param JobStatus $status
   */
  public function setStatus(JobStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return JobStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Required. TaskGroups in the Job. Only one TaskGroup is supported now.
   *
   * @param TaskGroup[] $taskGroups
   */
  public function setTaskGroups($taskGroups)
  {
    $this->taskGroups = $taskGroups;
  }
  /**
   * @return TaskGroup[]
   */
  public function getTaskGroups()
  {
    return $this->taskGroups;
  }
  /**
   * Output only. A system generated unique ID for the Job.
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
   * Output only. The last time the Job was updated.
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
class_alias(Job::class, 'Google_Service_Batch_Job');
