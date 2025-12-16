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

class ClusterSelector extends \Google\Model
{
  /**
   * Required. The cluster labels. Cluster must have all labels to match.
   *
   * @var string[]
   */
  public $clusterLabels;
  /**
   * Optional. The zone where workflow process executes. This parameter does not
   * affect the selection of the cluster.If unspecified, the zone of the first
   * cluster matching the selector is used.
   *
   * @var string
   */
  public $zone;

  /**
   * Required. The cluster labels. Cluster must have all labels to match.
   *
   * @param string[] $clusterLabels
   */
  public function setClusterLabels($clusterLabels)
  {
    $this->clusterLabels = $clusterLabels;
  }
  /**
   * @return string[]
   */
  public function getClusterLabels()
  {
    return $this->clusterLabels;
  }
  /**
   * Optional. The zone where workflow process executes. This parameter does not
   * affect the selection of the cluster.If unspecified, the zone of the first
   * cluster matching the selector is used.
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
class_alias(ClusterSelector::class, 'Google_Service_Dataproc_ClusterSelector');
