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

namespace Google\Service\CloudRedis;

class InternalResourceMetadata extends \Google\Model
{
  protected $backupConfigurationType = BackupConfiguration::class;
  protected $backupConfigurationDataType = '';
  protected $backupRunType = BackupRun::class;
  protected $backupRunDataType = '';
  /**
   * Whether deletion protection is enabled for this internal resource.
   *
   * @var bool
   */
  public $isDeletionProtectionEnabled;
  protected $productType = Product::class;
  protected $productDataType = '';
  protected $resourceIdType = DatabaseResourceId::class;
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
   * @param BackupConfiguration $backupConfiguration
   */
  public function setBackupConfiguration(BackupConfiguration $backupConfiguration)
  {
    $this->backupConfiguration = $backupConfiguration;
  }
  /**
   * @return BackupConfiguration
   */
  public function getBackupConfiguration()
  {
    return $this->backupConfiguration;
  }
  /**
   * Information about the last backup attempt for this database
   *
   * @param BackupRun $backupRun
   */
  public function setBackupRun(BackupRun $backupRun)
  {
    $this->backupRun = $backupRun;
  }
  /**
   * @return BackupRun
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
   * @param Product $product
   */
  public function setProduct(Product $product)
  {
    $this->product = $product;
  }
  /**
   * @return Product
   */
  public function getProduct()
  {
    return $this->product;
  }
  /**
   * @param DatabaseResourceId $resourceId
   */
  public function setResourceId(DatabaseResourceId $resourceId)
  {
    $this->resourceId = $resourceId;
  }
  /**
   * @return DatabaseResourceId
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
class_alias(InternalResourceMetadata::class, 'Google_Service_CloudRedis_InternalResourceMetadata');
