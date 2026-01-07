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

namespace Google\Service\AnalyticsHub;

class BigQueryDatasetSource extends \Google\Collection
{
  protected $collection_key = 'selectedResources';
  /**
   * Optional. Resource name of the dataset source for this listing. e.g.
   * `projects/myproject/datasets/123`
   *
   * @var string
   */
  public $dataset;
  protected $effectiveReplicasType = Replica::class;
  protected $effectiveReplicasDataType = 'array';
  /**
   * Optional. A list of regions where the publisher has created shared dataset
   * replicas.
   *
   * @var string[]
   */
  public $replicaLocations;
  protected $restrictedExportPolicyType = RestrictedExportPolicy::class;
  protected $restrictedExportPolicyDataType = '';
  protected $selectedResourcesType = SelectedResource::class;
  protected $selectedResourcesDataType = 'array';

  /**
   * Optional. Resource name of the dataset source for this listing. e.g.
   * `projects/myproject/datasets/123`
   *
   * @param string $dataset
   */
  public function setDataset($dataset)
  {
    $this->dataset = $dataset;
  }
  /**
   * @return string
   */
  public function getDataset()
  {
    return $this->dataset;
  }
  /**
   * Output only. Server-owned effective state of replicas. Contains both
   * primary and secondary replicas. Each replica includes a system-computed
   * (output-only) state and primary designation.
   *
   * @param Replica[] $effectiveReplicas
   */
  public function setEffectiveReplicas($effectiveReplicas)
  {
    $this->effectiveReplicas = $effectiveReplicas;
  }
  /**
   * @return Replica[]
   */
  public function getEffectiveReplicas()
  {
    return $this->effectiveReplicas;
  }
  /**
   * Optional. A list of regions where the publisher has created shared dataset
   * replicas.
   *
   * @param string[] $replicaLocations
   */
  public function setReplicaLocations($replicaLocations)
  {
    $this->replicaLocations = $replicaLocations;
  }
  /**
   * @return string[]
   */
  public function getReplicaLocations()
  {
    return $this->replicaLocations;
  }
  /**
   * Optional. If set, restricted export policy will be propagated and enforced
   * on the linked dataset.
   *
   * @param RestrictedExportPolicy $restrictedExportPolicy
   */
  public function setRestrictedExportPolicy(RestrictedExportPolicy $restrictedExportPolicy)
  {
    $this->restrictedExportPolicy = $restrictedExportPolicy;
  }
  /**
   * @return RestrictedExportPolicy
   */
  public function getRestrictedExportPolicy()
  {
    return $this->restrictedExportPolicy;
  }
  /**
   * Optional. Resource in this dataset that is selectively shared. This field
   * is required for data clean room exchanges.
   *
   * @param SelectedResource[] $selectedResources
   */
  public function setSelectedResources($selectedResources)
  {
    $this->selectedResources = $selectedResources;
  }
  /**
   * @return SelectedResource[]
   */
  public function getSelectedResources()
  {
    return $this->selectedResources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BigQueryDatasetSource::class, 'Google_Service_AnalyticsHub_BigQueryDatasetSource');
