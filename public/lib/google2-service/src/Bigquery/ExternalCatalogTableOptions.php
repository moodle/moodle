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

namespace Google\Service\Bigquery;

class ExternalCatalogTableOptions extends \Google\Model
{
  /**
   * Optional. A connection ID that specifies the credentials to be used to read
   * external storage, such as Azure Blob, Cloud Storage, or Amazon S3. This
   * connection is needed to read the open source table from BigQuery. The
   * connection_id format must be either `..` or
   * `projects//locations//connections/`.
   *
   * @var string
   */
  public $connectionId;
  /**
   * Optional. A map of the key-value pairs defining the parameters and
   * properties of the open source table. Corresponds with Hive metastore table
   * parameters. Maximum size of 4MiB.
   *
   * @var string[]
   */
  public $parameters;
  protected $storageDescriptorType = StorageDescriptor::class;
  protected $storageDescriptorDataType = '';

  /**
   * Optional. A connection ID that specifies the credentials to be used to read
   * external storage, such as Azure Blob, Cloud Storage, or Amazon S3. This
   * connection is needed to read the open source table from BigQuery. The
   * connection_id format must be either `..` or
   * `projects//locations//connections/`.
   *
   * @param string $connectionId
   */
  public function setConnectionId($connectionId)
  {
    $this->connectionId = $connectionId;
  }
  /**
   * @return string
   */
  public function getConnectionId()
  {
    return $this->connectionId;
  }
  /**
   * Optional. A map of the key-value pairs defining the parameters and
   * properties of the open source table. Corresponds with Hive metastore table
   * parameters. Maximum size of 4MiB.
   *
   * @param string[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return string[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Optional. A storage descriptor containing information about the physical
   * storage of this table.
   *
   * @param StorageDescriptor $storageDescriptor
   */
  public function setStorageDescriptor(StorageDescriptor $storageDescriptor)
  {
    $this->storageDescriptor = $storageDescriptor;
  }
  /**
   * @return StorageDescriptor
   */
  public function getStorageDescriptor()
  {
    return $this->storageDescriptor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExternalCatalogTableOptions::class, 'Google_Service_Bigquery_ExternalCatalogTableOptions');
