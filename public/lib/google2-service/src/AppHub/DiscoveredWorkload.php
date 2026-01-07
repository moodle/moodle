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

namespace Google\Service\AppHub;

class DiscoveredWorkload extends \Google\Model
{
  /**
   * Identifier. The resource name of the discovered workload. Format:
   * `"projects/{host-project-
   * id}/locations/{location}/discoveredWorkloads/{uuid}"`
   *
   * @var string
   */
  public $name;
  protected $workloadPropertiesType = WorkloadProperties::class;
  protected $workloadPropertiesDataType = '';
  protected $workloadReferenceType = WorkloadReference::class;
  protected $workloadReferenceDataType = '';

  /**
   * Identifier. The resource name of the discovered workload. Format:
   * `"projects/{host-project-
   * id}/locations/{location}/discoveredWorkloads/{uuid}"`
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
   * Output only. Properties of an underlying compute resource represented by
   * the Workload. These are immutable.
   *
   * @param WorkloadProperties $workloadProperties
   */
  public function setWorkloadProperties(WorkloadProperties $workloadProperties)
  {
    $this->workloadProperties = $workloadProperties;
  }
  /**
   * @return WorkloadProperties
   */
  public function getWorkloadProperties()
  {
    return $this->workloadProperties;
  }
  /**
   * Output only. Reference of an underlying compute resource represented by the
   * Workload. These are immutable.
   *
   * @param WorkloadReference $workloadReference
   */
  public function setWorkloadReference(WorkloadReference $workloadReference)
  {
    $this->workloadReference = $workloadReference;
  }
  /**
   * @return WorkloadReference
   */
  public function getWorkloadReference()
  {
    return $this->workloadReference;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiscoveredWorkload::class, 'Google_Service_AppHub_DiscoveredWorkload');
