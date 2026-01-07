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

namespace Google\Service\CloudDeploy;

class TargetsTypeCondition extends \Google\Model
{
  /**
   * Human readable error message.
   *
   * @var string
   */
  public $errorDetails;
  /**
   * True if the targets are all a comparable type. For example this is true if
   * all targets are GKE clusters. This is false if some targets are Cloud Run
   * targets and others are GKE clusters.
   *
   * @var bool
   */
  public $status;

  /**
   * Human readable error message.
   *
   * @param string $errorDetails
   */
  public function setErrorDetails($errorDetails)
  {
    $this->errorDetails = $errorDetails;
  }
  /**
   * @return string
   */
  public function getErrorDetails()
  {
    return $this->errorDetails;
  }
  /**
   * True if the targets are all a comparable type. For example this is true if
   * all targets are GKE clusters. This is false if some targets are Cloud Run
   * targets and others are GKE clusters.
   *
   * @param bool $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return bool
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetsTypeCondition::class, 'Google_Service_CloudDeploy_TargetsTypeCondition');
