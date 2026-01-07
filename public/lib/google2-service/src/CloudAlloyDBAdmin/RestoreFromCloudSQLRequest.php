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

namespace Google\Service\CloudAlloyDBAdmin;

class RestoreFromCloudSQLRequest extends \Google\Model
{
  protected $cloudsqlBackupRunSourceType = CloudSQLBackupRunSource::class;
  protected $cloudsqlBackupRunSourceDataType = '';
  protected $clusterType = Cluster::class;
  protected $clusterDataType = '';
  /**
   * Required. ID of the requesting object.
   *
   * @var string
   */
  public $clusterId;

  /**
   * Cluster created from CloudSQL backup run.
   *
   * @param CloudSQLBackupRunSource $cloudsqlBackupRunSource
   */
  public function setCloudsqlBackupRunSource(CloudSQLBackupRunSource $cloudsqlBackupRunSource)
  {
    $this->cloudsqlBackupRunSource = $cloudsqlBackupRunSource;
  }
  /**
   * @return CloudSQLBackupRunSource
   */
  public function getCloudsqlBackupRunSource()
  {
    return $this->cloudsqlBackupRunSource;
  }
  /**
   * Required. The resource being created
   *
   * @param Cluster $cluster
   */
  public function setCluster(Cluster $cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return Cluster
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * Required. ID of the requesting object.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RestoreFromCloudSQLRequest::class, 'Google_Service_CloudAlloyDBAdmin_RestoreFromCloudSQLRequest');
