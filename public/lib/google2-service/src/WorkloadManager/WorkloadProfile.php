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

class WorkloadProfile extends \Google\Model
{
  /**
   * unspecified workload type
   */
  public const WORKLOAD_TYPE_WORKLOAD_TYPE_UNSPECIFIED = 'WORKLOAD_TYPE_UNSPECIFIED';
  /**
   * running sap workload s4/hana
   */
  public const WORKLOAD_TYPE_S4_HANA = 'S4_HANA';
  /**
   * Optional. such as name, description, version. More example can be found in
   * deployment
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. name of resource names have the form
   * 'projects/{project_id}/locations/{location}/workloadProfiles/{workload_id}'
   *
   * @var string
   */
  public $name;
  /**
   * Required. time when the workload data was refreshed
   *
   * @var string
   */
  public $refreshedTime;
  protected $sapWorkloadType = SapWorkload::class;
  protected $sapWorkloadDataType = '';
  /**
   * Required. The type of the workload
   *
   * @var string
   */
  public $workloadType;

  /**
   * Optional. such as name, description, version. More example can be found in
   * deployment
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
   * Identifier. name of resource names have the form
   * 'projects/{project_id}/locations/{location}/workloadProfiles/{workload_id}'
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
   * Required. time when the workload data was refreshed
   *
   * @param string $refreshedTime
   */
  public function setRefreshedTime($refreshedTime)
  {
    $this->refreshedTime = $refreshedTime;
  }
  /**
   * @return string
   */
  public function getRefreshedTime()
  {
    return $this->refreshedTime;
  }
  /**
   * The sap workload content
   *
   * @param SapWorkload $sapWorkload
   */
  public function setSapWorkload(SapWorkload $sapWorkload)
  {
    $this->sapWorkload = $sapWorkload;
  }
  /**
   * @return SapWorkload
   */
  public function getSapWorkload()
  {
    return $this->sapWorkload;
  }
  /**
   * Required. The type of the workload
   *
   * Accepted values: WORKLOAD_TYPE_UNSPECIFIED, S4_HANA
   *
   * @param self::WORKLOAD_TYPE_* $workloadType
   */
  public function setWorkloadType($workloadType)
  {
    $this->workloadType = $workloadType;
  }
  /**
   * @return self::WORKLOAD_TYPE_*
   */
  public function getWorkloadType()
  {
    return $this->workloadType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkloadProfile::class, 'Google_Service_WorkloadManager_WorkloadProfile');
