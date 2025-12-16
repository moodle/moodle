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

namespace Google\Service\WorkloadManager;

class OpenShiftValidation extends \Google\Model
{
  /**
   * Required. The OpenShift cluster ID (e.g.
   * 8371bb05-7cac-4d38-82c0-0f58c4f6f936).
   *
   * @var string
   */
  public $clusterId;
  /**
   * Required. The validation details of the OpenShift cluster in JSON format.
   *
   * @var array[]
   */
  public $validationDetails;

  /**
   * Required. The OpenShift cluster ID (e.g.
   * 8371bb05-7cac-4d38-82c0-0f58c4f6f936).
   *
   * @param string $clusterId
   */
  public function setClusterId($clusterId)
  {
    $this->clusterId = $clusterId;
  }
  /**
   * @return string
   */
  public function getClusterId()
  {
    return $this->clusterId;
  }
  /**
   * Required. The validation details of the OpenShift cluster in JSON format.
   *
   * @param array[] $validationDetails
   */
  public function setValidationDetails($validationDetails)
  {
    $this->validationDetails = $validationDetails;
  }
  /**
   * @return array[]
   */
  public function getValidationDetails()
  {
    return $this->validationDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OpenShiftValidation::class, 'Google_Service_WorkloadManager_OpenShiftValidation');
