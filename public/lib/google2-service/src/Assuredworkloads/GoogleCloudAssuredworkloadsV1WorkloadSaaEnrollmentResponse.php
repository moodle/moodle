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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1WorkloadSaaEnrollmentResponse extends \Google\Collection
{
  /**
   * Unspecified.
   */
  public const SETUP_STATUS_SETUP_STATE_UNSPECIFIED = 'SETUP_STATE_UNSPECIFIED';
  /**
   * SAA enrollment pending.
   */
  public const SETUP_STATUS_STATUS_PENDING = 'STATUS_PENDING';
  /**
   * SAA enrollment comopleted.
   */
  public const SETUP_STATUS_STATUS_COMPLETE = 'STATUS_COMPLETE';
  protected $collection_key = 'setupErrors';
  /**
   * Indicates SAA enrollment setup error if any.
   *
   * @var string[]
   */
  public $setupErrors;
  /**
   * Output only. Indicates SAA enrollment status of a given workload.
   *
   * @var string
   */
  public $setupStatus;

  /**
   * Indicates SAA enrollment setup error if any.
   *
   * @param string[] $setupErrors
   */
  public function setSetupErrors($setupErrors)
  {
    $this->setupErrors = $setupErrors;
  }
  /**
   * @return string[]
   */
  public function getSetupErrors()
  {
    return $this->setupErrors;
  }
  /**
   * Output only. Indicates SAA enrollment status of a given workload.
   *
   * Accepted values: SETUP_STATE_UNSPECIFIED, STATUS_PENDING, STATUS_COMPLETE
   *
   * @param self::SETUP_STATUS_* $setupStatus
   */
  public function setSetupStatus($setupStatus)
  {
    $this->setupStatus = $setupStatus;
  }
  /**
   * @return self::SETUP_STATUS_*
   */
  public function getSetupStatus()
  {
    return $this->setupStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1WorkloadSaaEnrollmentResponse::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1WorkloadSaaEnrollmentResponse');
