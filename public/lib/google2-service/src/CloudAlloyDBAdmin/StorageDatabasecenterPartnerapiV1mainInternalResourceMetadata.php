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

class StorageDatabasecenterPartnerapiV1mainInternalResourceMetadata extends \Google\Model
{
  protected $backupConfigurationType = StorageDatabasecenterPartnerapiV1mainBackupConfiguration::class;
  protected $backupConfigurationDataType = '';
  protected $backupRunType = StorageDatabasecenterPartnerapiV1mainBackupRun::class;
  protected $backupRunDataType = '';
  /**
   * Whether deletion protection is enabled for this internal resource.
   *
   * @var bool
   */
  public $isDeletionProtectionEnabled;
  protected $productType = StorageDatabasecenterProtoCommonProduct::class;
  protected $productDataType = '';
  protected $resourceIdType = StorageDatabasecenterPartnerapiV1mainDatabaseResourceId::class;
  protected $resourceIdDataType = '';
  /**
   * Required. internal resource name for spanner this will be database name
   * e.g."spanner.googleapis.com/projects/123/abc/instances/inst1/databases/db1"
   *
   * @var string
   */
  public $resourceName;

  /**
   * Backup configuration for this database
   *
   * @param StorageDatabasecenterPartnerapiV1mainBackupConfiguration $backupConfiguration
   */
  public function setBackupConfiguration(StorageDatabasecenterPartnerapiV1mainBackupConfiguration $backupConfiguration)
  {
    $this->backupConfiguration = $backupConfiguration;
  }
  /**
   * @return StorageDatabasecenterPartnerapiV1mainBackupConfiguration
   */
  public function getBackupConfiguration()
  {
    return $this->backupConfiguration;
  }
  /**
   * Information about the last backup attempt for this database
   *
   * @param StorageDatabasecenterPartnerapiV1mainBackupRun $backupRun
   */
  public function setBackupRun(StorageDatabasecenterPartnerapiV1mainBackupRun $backupRun)
  {
    $this->backupRun = $backupRun;
  }
  /**
   * @return StorageDatabasecenterPartnerapiV1mainBackupRun
   */
  public function getBackupRun()
  {
    return $this->backupRun;
  }
  /**
   * Whether deletion protection is enabled for this internal resource.
   *
   * @param bool $isDeletionProtectionEnabled
   */
  public function setIsDeletionProtectionEnabled($isDeletionProtectionEnabled)
  {
    $this->isDeletionProtectionEnabled = $isDeletionProtectionEnabled;
  }
  /**
   * @return bool
   */
  public function getIsDeletionProtectionEnabled()
  {
    return $this->isDeletionProtectionEnabled;
  }
  /**
   * @param StorageDatabasecenterProtoCommonProduct $product
   */
  public function setProduct(StorageDatabasecenterProtoCommonProduct $product)
  {
    $this->product = $product;
  }
  /**
   * @return StorageDatabasecenterProtoCommonProduct
   */
  public function getProduct()
  {
    return $this->product;
  }
  /**
   * @param StorageDatabasecenterPartnerapiV1mainDatabaseResourceId $resourceId
   */
  public function setResourceId(StorageDatabasecenterPartnerapiV1mainDatabaseResourceId $resourceId)
  {
    $this->resourceId = $resourceId;
  }
  /**
   * @return StorageDatabasecenterPartnerapiV1mainDatabaseResourceId
   */
  public function getResourceId()
  {
    return $this->resourceId;
  }
  /**
   * Required. internal resource name for spanner this will be database name
   * e.g."spanner.googleapis.com/projects/123/abc/instances/inst1/databases/db1"
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StorageDatabasecenterPartnerapiV1mainInternalResourceMetadata::class, 'Google_Service_CloudAlloyDBAdmin_StorageDatabasecenterPartnerapiV1mainInternalResourceMetadata');
