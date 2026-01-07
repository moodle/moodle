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

class SapValidation extends \Google\Collection
{
  protected $collection_key = 'validationDetails';
  /**
   * Required. The project_id of the cloud project that the Insight data comes
   * from.
   *
   * @var string
   */
  public $projectId;
  protected $validationDetailsType = SapValidationValidationDetail::class;
  protected $validationDetailsDataType = 'array';
  /**
   * Optional. The zone of the instance that the Insight data comes from.
   *
   * @var string
   */
  public $zone;

  /**
   * Required. The project_id of the cloud project that the Insight data comes
   * from.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Optional. A list of SAP validation metrics data.
   *
   * @param SapValidationValidationDetail[] $validationDetails
   */
  public function setValidationDetails($validationDetails)
  {
    $this->validationDetails = $validationDetails;
  }
  /**
   * @return SapValidationValidationDetail[]
   */
  public function getValidationDetails()
  {
    return $this->validationDetails;
  }
  /**
   * Optional. The zone of the instance that the Insight data comes from.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SapValidation::class, 'Google_Service_WorkloadManager_SapValidation');
