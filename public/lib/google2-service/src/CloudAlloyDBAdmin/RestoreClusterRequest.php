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

class RestoreClusterRequest extends \Google\Model
{
  protected $backupSourceType = BackupSource::class;
  protected $backupSourceDataType = '';
  protected $backupdrBackupSourceType = BackupDrBackupSource::class;
  protected $backupdrBackupSourceDataType = '';
  protected $backupdrPitrSourceType = BackupDrPitrSource::class;
  protected $backupdrPitrSourceDataType = '';
  protected $clusterType = Cluster::class;
  protected $clusterDataType = '';
  /**
   * Required. ID of the requesting object.
   *
   * @var string
   */
  public $clusterId;
  protected $continuousBackupSourceType = ContinuousBackupSource::class;
  protected $continuousBackupSourceDataType = '';
  /**
   * Optional. An optional request ID to identify requests. Specify a unique
   * request ID so that if you must retry your request, the server ignores the
   * request if it has already been completed. The server guarantees that for at
   * least 60 minutes since the first request. For example, consider a situation
   * where you make an initial request and the request times out. If you make
   * the request again with the same request ID, the server can check if the
   * original operation with the same request ID was received, and if so,
   * ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with
   * the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @var string
   */
  public $requestId;
  /**
   * Optional. If set, performs request validation, for example, permission
   * checks and any other type of validation, but does not actually execute the
   * create request.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * Backup source.
   *
   * @param BackupSource $backupSource
   */
  public function setBackupSource(BackupSource $backupSource)
  {
    $this->backupSource = $backupSource;
  }
  /**
   * @return BackupSource
   */
  public function getBackupSource()
  {
    return $this->backupSource;
  }
  /**
   * BackupDR backup source.
   *
   * @param BackupDrBackupSource $backupdrBackupSource
   */
  public function setBackupdrBackupSource(BackupDrBackupSource $backupdrBackupSource)
  {
    $this->backupdrBackupSource = $backupdrBackupSource;
  }
  /**
   * @return BackupDrBackupSource
   */
  public function getBackupdrBackupSource()
  {
    return $this->backupdrBackupSource;
  }
  /**
   * BackupDR source used for point in time recovery.
   *
   * @param BackupDrPitrSource $backupdrPitrSource
   */
  public function setBackupdrPitrSource(BackupDrPitrSource $backupdrPitrSource)
  {
    $this->backupdrPitrSource = $backupdrPitrSource;
  }
  /**
   * @return BackupDrPitrSource
   */
  public function getBackupdrPitrSource()
  {
    return $this->backupdrPitrSource;
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
  /**
   * ContinuousBackup source. Continuous backup needs to be enabled in the
   * source cluster for this operation to succeed.
   *
   * @param ContinuousBackupSource $continuousBackupSource
   */
  public function setContinuousBackupSource(ContinuousBackupSource $continuousBackupSource)
  {
    $this->continuousBackupSource = $continuousBackupSource;
  }
  /**
   * @return ContinuousBackupSource
   */
  public function getContinuousBackupSource()
  {
    return $this->continuousBackupSource;
  }
  /**
   * Optional. An optional request ID to identify requests. Specify a unique
   * request ID so that if you must retry your request, the server ignores the
   * request if it has already been completed. The server guarantees that for at
   * least 60 minutes since the first request. For example, consider a situation
   * where you make an initial request and the request times out. If you make
   * the request again with the same request ID, the server can check if the
   * original operation with the same request ID was received, and if so,
   * ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with
   * the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * Optional. If set, performs request validation, for example, permission
   * checks and any other type of validation, but does not actually execute the
   * create request.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RestoreClusterRequest::class, 'Google_Service_CloudAlloyDBAdmin_RestoreClusterRequest');
