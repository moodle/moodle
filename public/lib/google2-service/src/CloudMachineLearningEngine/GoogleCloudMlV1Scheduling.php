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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1Scheduling extends \Google\Model
{
  /**
   * Optional. The maximum job running time, expressed in seconds. The field can
   * contain up to nine fractional digits, terminated by `s`. If not specified,
   * this field defaults to `604800s` (seven days). If the training job is still
   * running after this duration, AI Platform Training cancels it. The duration
   * is measured from when the job enters the `RUNNING` state; therefore it does
   * not overlap with the duration limited by Scheduling.max_wait_time. For
   * example, if you want to ensure your job runs for no more than 2 hours, set
   * this field to `7200s` (2 hours * 60 minutes / hour * 60 seconds / minute).
   * If you submit your training job using the `gcloud` tool, you can [specify
   * this field in a `config.yaml` file](/ai-platform/training/docs/training-
   * jobs#formatting_your_configuration_parameters). For example: ```yaml
   * trainingInput: scheduling: maxRunningTime: 7200s ```
   *
   * @var string
   */
  public $maxRunningTime;
  /**
   * Optional. The maximum job wait time, expressed in seconds. The field can
   * contain up to nine fractional digits, terminated by `s`. If not specified,
   * there is no limit to the wait time. The minimum for this field is `1800s`
   * (30 minutes). If the training job has not entered the `RUNNING` state after
   * this duration, AI Platform Training cancels it. After the job begins
   * running, it can no longer be cancelled due to the maximum wait time.
   * Therefore the duration limited by this field does not overlap with the
   * duration limited by Scheduling.max_running_time. For example, if the job
   * temporarily stops running and retries due to a [VM restart](/ai-
   * platform/training/docs/overview#restarts), this cannot lead to a maximum
   * wait time cancellation. However, independently of this constraint, AI
   * Platform Training might stop a job if there are too many retries due to
   * exhausted resources in a region. The following example describes how you
   * might use this field: To cancel your job if it doesn't start running within
   * 1 hour, set this field to `3600s` (1 hour * 60 minutes / hour * 60 seconds
   * / minute). If the job is still in the `QUEUED` or `PREPARING` state after
   * an hour of waiting, AI Platform Training cancels the job. If you submit
   * your training job using the `gcloud` tool, you can [specify this field in a
   * `config.yaml` file](/ai-platform/training/docs/training-
   * jobs#formatting_your_configuration_parameters). For example: ```yaml
   * trainingInput: scheduling: maxWaitTime: 3600s ```
   *
   * @var string
   */
  public $maxWaitTime;
  /**
   * Optional. Job scheduling will be based on this priority, which in the range
   * [0, 1000]. The bigger the number, the higher the priority. Default to 0 if
   * not set. If there are multiple jobs requesting same type of accelerators,
   * the high priority job will be scheduled prior to ones with low priority.
   *
   * @var int
   */
  public $priority;

  /**
   * Optional. The maximum job running time, expressed in seconds. The field can
   * contain up to nine fractional digits, terminated by `s`. If not specified,
   * this field defaults to `604800s` (seven days). If the training job is still
   * running after this duration, AI Platform Training cancels it. The duration
   * is measured from when the job enters the `RUNNING` state; therefore it does
   * not overlap with the duration limited by Scheduling.max_wait_time. For
   * example, if you want to ensure your job runs for no more than 2 hours, set
   * this field to `7200s` (2 hours * 60 minutes / hour * 60 seconds / minute).
   * If you submit your training job using the `gcloud` tool, you can [specify
   * this field in a `config.yaml` file](/ai-platform/training/docs/training-
   * jobs#formatting_your_configuration_parameters). For example: ```yaml
   * trainingInput: scheduling: maxRunningTime: 7200s ```
   *
   * @param string $maxRunningTime
   */
  public function setMaxRunningTime($maxRunningTime)
  {
    $this->maxRunningTime = $maxRunningTime;
  }
  /**
   * @return string
   */
  public function getMaxRunningTime()
  {
    return $this->maxRunningTime;
  }
  /**
   * Optional. The maximum job wait time, expressed in seconds. The field can
   * contain up to nine fractional digits, terminated by `s`. If not specified,
   * there is no limit to the wait time. The minimum for this field is `1800s`
   * (30 minutes). If the training job has not entered the `RUNNING` state after
   * this duration, AI Platform Training cancels it. After the job begins
   * running, it can no longer be cancelled due to the maximum wait time.
   * Therefore the duration limited by this field does not overlap with the
   * duration limited by Scheduling.max_running_time. For example, if the job
   * temporarily stops running and retries due to a [VM restart](/ai-
   * platform/training/docs/overview#restarts), this cannot lead to a maximum
   * wait time cancellation. However, independently of this constraint, AI
   * Platform Training might stop a job if there are too many retries due to
   * exhausted resources in a region. The following example describes how you
   * might use this field: To cancel your job if it doesn't start running within
   * 1 hour, set this field to `3600s` (1 hour * 60 minutes / hour * 60 seconds
   * / minute). If the job is still in the `QUEUED` or `PREPARING` state after
   * an hour of waiting, AI Platform Training cancels the job. If you submit
   * your training job using the `gcloud` tool, you can [specify this field in a
   * `config.yaml` file](/ai-platform/training/docs/training-
   * jobs#formatting_your_configuration_parameters). For example: ```yaml
   * trainingInput: scheduling: maxWaitTime: 3600s ```
   *
   * @param string $maxWaitTime
   */
  public function setMaxWaitTime($maxWaitTime)
  {
    $this->maxWaitTime = $maxWaitTime;
  }
  /**
   * @return string
   */
  public function getMaxWaitTime()
  {
    return $this->maxWaitTime;
  }
  /**
   * Optional. Job scheduling will be based on this priority, which in the range
   * [0, 1000]. The bigger the number, the higher the priority. Default to 0 if
   * not set. If there are multiple jobs requesting same type of accelerators,
   * the high priority job will be scheduled prior to ones with low priority.
   *
   * @param int $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return int
   */
  public function getPriority()
  {
    return $this->priority;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1Scheduling::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1Scheduling');
