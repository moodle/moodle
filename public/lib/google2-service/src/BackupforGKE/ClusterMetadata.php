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

namespace Google\Service\BackupforGKE;

class ClusterMetadata extends \Google\Model
{
  /**
   * Output only. Anthos version
   *
   * @var string
   */
  public $anthosVersion;
  /**
   * Output only. A list of the Backup for GKE CRD versions found in the
   * cluster.
   *
   * @var string[]
   */
  public $backupCrdVersions;
  /**
   * Output only. The source cluster from which this Backup was created. Valid
   * formats: - `projects/locations/clusters` - `projects/zones/clusters` This
   * is inherited from the parent BackupPlan's cluster field.
   *
   * @var string
   */
  public $cluster;
  /**
   * Output only. GKE version
   *
   * @var string
   */
  public $gkeVersion;
  /**
   * Output only. The Kubernetes server version of the source cluster.
   *
   * @var string
   */
  public $k8sVersion;

  /**
   * Output only. Anthos version
   *
   * @param string $anthosVersion
   */
  public function setAnthosVersion($anthosVersion)
  {
    $this->anthosVersion = $anthosVersion;
  }
  /**
   * @return string
   */
  public function getAnthosVersion()
  {
    return $this->anthosVersion;
  }
  /**
   * Output only. A list of the Backup for GKE CRD versions found in the
   * cluster.
   *
   * @param string[] $backupCrdVersions
   */
  public function setBackupCrdVersions($backupCrdVersions)
  {
    $this->backupCrdVersions = $backupCrdVersions;
  }
  /**
   * @return string[]
   */
  public function getBackupCrdVersions()
  {
    return $this->backupCrdVersions;
  }
  /**
   * Output only. The source cluster from which this Backup was created. Valid
   * formats: - `projects/locations/clusters` - `projects/zones/clusters` This
   * is inherited from the parent BackupPlan's cluster field.
   *
   * @param string $cluster
   */
  public function setCluster($cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return string
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * Output only. GKE version
   *
   * @param string $gkeVersion
   */
  public function setGkeVersion($gkeVersion)
  {
    $this->gkeVersion = $gkeVersion;
  }
  /**
   * @return string
   */
  public function getGkeVersion()
  {
    return $this->gkeVersion;
  }
  /**
   * Output only. The Kubernetes server version of the source cluster.
   *
   * @param string $k8sVersion
   */
  public function setK8sVersion($k8sVersion)
  {
    $this->k8sVersion = $k8sVersion;
  }
  /**
   * @return string
   */
  public function getK8sVersion()
  {
    return $this->k8sVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterMetadata::class, 'Google_Service_BackupforGKE_ClusterMetadata');
