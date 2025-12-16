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

namespace Google\Service\Compute;

class InstanceManagedByIgmError extends \Google\Model
{
  protected $errorType = InstanceManagedByIgmErrorManagedInstanceError::class;
  protected $errorDataType = '';
  protected $instanceActionDetailsType = InstanceManagedByIgmErrorInstanceActionDetails::class;
  protected $instanceActionDetailsDataType = '';
  /**
   * Output only. [Output Only] The time that this error occurred. This value is
   * in RFC3339 text format.
   *
   * @var string
   */
  public $timestamp;

  /**
   * Output only. [Output Only] Contents of the error.
   *
   * @param InstanceManagedByIgmErrorManagedInstanceError $error
   */
  public function setError(InstanceManagedByIgmErrorManagedInstanceError $error)
  {
    $this->error = $error;
  }
  /**
   * @return InstanceManagedByIgmErrorManagedInstanceError
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. [Output Only] Details of the instance action that triggered
   * this error. May be null, if the error was not caused by an action on an
   * instance. This field is optional.
   *
   * @param InstanceManagedByIgmErrorInstanceActionDetails $instanceActionDetails
   */
  public function setInstanceActionDetails(InstanceManagedByIgmErrorInstanceActionDetails $instanceActionDetails)
  {
    $this->instanceActionDetails = $instanceActionDetails;
  }
  /**
   * @return InstanceManagedByIgmErrorInstanceActionDetails
   */
  public function getInstanceActionDetails()
  {
    return $this->instanceActionDetails;
  }
  /**
   * Output only. [Output Only] The time that this error occurred. This value is
   * in RFC3339 text format.
   *
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceManagedByIgmError::class, 'Google_Service_Compute_InstanceManagedByIgmError');
