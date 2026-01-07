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

class TaskExecution extends \Google\Model
{
  /**
   * The exit code of a finished task. If the task succeeded, the exit code will
   * be 0. If the task failed but not due to the following reasons, the exit
   * code will be 50000. Otherwise, it can be from different sources: * Batch
   * known failures:
   * https://cloud.google.com/batch/docs/troubleshooting#reserved-exit-codes. *
   * Batch runnable execution failures; you can rely on Batch logs to further
   * diagnose: https://cloud.google.com/batch/docs/analyze-job-using-logs. If
   * there are multiple runnables failures, Batch only exposes the first error.
   *
   * @var int
   */
  public $exitCode;

  /**
   * The exit code of a finished task. If the task succeeded, the exit code will
   * be 0. If the task failed but not due to the following reasons, the exit
   * code will be 50000. Otherwise, it can be from different sources: * Batch
   * known failures:
   * https://cloud.google.com/batch/docs/troubleshooting#reserved-exit-codes. *
   * Batch runnable execution failures; you can rely on Batch logs to further
   * diagnose: https://cloud.google.com/batch/docs/analyze-job-using-logs. If
   * there are multiple runnables failures, Batch only exposes the first error.
   *
   * @param int $exitCode
   */
  public function setExitCode($exitCode)
  {
    $this->exitCode = $exitCode;
  }
  /**
   * @return int
   */
  public function getExitCode()
  {
    return $this->exitCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TaskExecution::class, 'Google_Service_Batch_TaskExecution');
