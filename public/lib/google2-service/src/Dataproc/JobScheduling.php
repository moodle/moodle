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

namespace Google\Service\Dataproc;

class JobScheduling extends \Google\Model
{
  /**
   * Optional. Maximum number of times per hour a driver can be restarted as a
   * result of driver exiting with non-zero code before job is reported failed.A
   * job might be reported as thrashing if the driver exits with a non-zero code
   * four times within a 10-minute window.Maximum value is 10.Note: This
   * restartable job option is not supported in Dataproc workflow templates
   * (https://cloud.google.com/dataproc/docs/concepts/workflows/using-
   * workflows#adding_jobs_to_a_template).
   *
   * @var int
   */
  public $maxFailuresPerHour;
  /**
   * Optional. Maximum total number of times a driver can be restarted as a
   * result of the driver exiting with a non-zero code. After the maximum number
   * is reached, the job will be reported as failed.Maximum value is 240.Note:
   * Currently, this restartable job option is not supported in Dataproc
   * workflow templates
   * (https://cloud.google.com/dataproc/docs/concepts/workflows/using-
   * workflows#adding_jobs_to_a_template).
   *
   * @var int
   */
  public $maxFailuresTotal;

  /**
   * Optional. Maximum number of times per hour a driver can be restarted as a
   * result of driver exiting with non-zero code before job is reported failed.A
   * job might be reported as thrashing if the driver exits with a non-zero code
   * four times within a 10-minute window.Maximum value is 10.Note: This
   * restartable job option is not supported in Dataproc workflow templates
   * (https://cloud.google.com/dataproc/docs/concepts/workflows/using-
   * workflows#adding_jobs_to_a_template).
   *
   * @param int $maxFailuresPerHour
   */
  public function setMaxFailuresPerHour($maxFailuresPerHour)
  {
    $this->maxFailuresPerHour = $maxFailuresPerHour;
  }
  /**
   * @return int
   */
  public function getMaxFailuresPerHour()
  {
    return $this->maxFailuresPerHour;
  }
  /**
   * Optional. Maximum total number of times a driver can be restarted as a
   * result of the driver exiting with a non-zero code. After the maximum number
   * is reached, the job will be reported as failed.Maximum value is 240.Note:
   * Currently, this restartable job option is not supported in Dataproc
   * workflow templates
   * (https://cloud.google.com/dataproc/docs/concepts/workflows/using-
   * workflows#adding_jobs_to_a_template).
   *
   * @param int $maxFailuresTotal
   */
  public function setMaxFailuresTotal($maxFailuresTotal)
  {
    $this->maxFailuresTotal = $maxFailuresTotal;
  }
  /**
   * @return int
   */
  public function getMaxFailuresTotal()
  {
    return $this->maxFailuresTotal;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobScheduling::class, 'Google_Service_Dataproc_JobScheduling');
