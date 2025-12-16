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

class WorkflowTemplatePlacement extends \Google\Model
{
  protected $clusterSelectorType = ClusterSelector::class;
  protected $clusterSelectorDataType = '';
  protected $managedClusterType = ManagedCluster::class;
  protected $managedClusterDataType = '';

  /**
   * Optional. A selector that chooses target cluster for jobs based on
   * metadata.The selector is evaluated at the time each job is submitted.
   *
   * @param ClusterSelector $clusterSelector
   */
  public function setClusterSelector(ClusterSelector $clusterSelector)
  {
    $this->clusterSelector = $clusterSelector;
  }
  /**
   * @return ClusterSelector
   */
  public function getClusterSelector()
  {
    return $this->clusterSelector;
  }
  /**
   * A cluster that is managed by the workflow.
   *
   * @param ManagedCluster $managedCluster
   */
  public function setManagedCluster(ManagedCluster $managedCluster)
  {
    $this->managedCluster = $managedCluster;
  }
  /**
   * @return ManagedCluster
   */
  public function getManagedCluster()
  {
    return $this->managedCluster;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkflowTemplatePlacement::class, 'Google_Service_Dataproc_WorkflowTemplatePlacement');
