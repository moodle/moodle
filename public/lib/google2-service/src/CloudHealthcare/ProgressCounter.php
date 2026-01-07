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

namespace Google\Service\CloudHealthcare;

class ProgressCounter extends \Google\Model
{
  /**
   * The number of units that failed in the operation.
   *
   * @var string
   */
  public $failure;
  /**
   * The number of units that are pending in the operation.
   *
   * @var string
   */
  public $pending;
  /**
   * The number of secondary units that failed in the operation.
   *
   * @var string
   */
  public $secondaryFailure;
  /**
   * The number of secondary units that succeeded in the operation.
   *
   * @var string
   */
  public $secondarySuccess;
  /**
   * The number of units that succeeded in the operation.
   *
   * @var string
   */
  public $success;

  /**
   * The number of units that failed in the operation.
   *
   * @param string $failure
   */
  public function setFailure($failure)
  {
    $this->failure = $failure;
  }
  /**
   * @return string
   */
  public function getFailure()
  {
    return $this->failure;
  }
  /**
   * The number of units that are pending in the operation.
   *
   * @param string $pending
   */
  public function setPending($pending)
  {
    $this->pending = $pending;
  }
  /**
   * @return string
   */
  public function getPending()
  {
    return $this->pending;
  }
  /**
   * The number of secondary units that failed in the operation.
   *
   * @param string $secondaryFailure
   */
  public function setSecondaryFailure($secondaryFailure)
  {
    $this->secondaryFailure = $secondaryFailure;
  }
  /**
   * @return string
   */
  public function getSecondaryFailure()
  {
    return $this->secondaryFailure;
  }
  /**
   * The number of secondary units that succeeded in the operation.
   *
   * @param string $secondarySuccess
   */
  public function setSecondarySuccess($secondarySuccess)
  {
    $this->secondarySuccess = $secondarySuccess;
  }
  /**
   * @return string
   */
  public function getSecondarySuccess()
  {
    return $this->secondarySuccess;
  }
  /**
   * The number of units that succeeded in the operation.
   *
   * @param string $success
   */
  public function setSuccess($success)
  {
    $this->success = $success;
  }
  /**
   * @return string
   */
  public function getSuccess()
  {
    return $this->success;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProgressCounter::class, 'Google_Service_CloudHealthcare_ProgressCounter');
