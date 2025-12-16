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

namespace Google\Service\Backupdr;

class DataSourceGcpResource extends \Google\Model
{
  protected $alloyDbClusterDatasourcePropertiesType = AlloyDBClusterDataSourceProperties::class;
  protected $alloyDbClusterDatasourcePropertiesDataType = '';
  protected $cloudSqlInstanceDatasourcePropertiesType = CloudSqlInstanceDataSourceProperties::class;
  protected $cloudSqlInstanceDatasourcePropertiesDataType = '';
  protected $computeInstanceDatasourcePropertiesType = ComputeInstanceDataSourceProperties::class;
  protected $computeInstanceDatasourcePropertiesDataType = '';
  protected $diskDatasourcePropertiesType = DiskDataSourceProperties::class;
  protected $diskDatasourcePropertiesDataType = '';
  /**
   * Output only. Full resource pathname URL of the source Google Cloud
   * resource.
   *
   * @var string
   */
  public $gcpResourcename;
  /**
   * Location of the resource: //"global"/"unspecified".
   *
   * @var string
   */
  public $location;
  /**
   * The type of the Google Cloud resource. Use the Unified Resource Type, eg.
   * compute.googleapis.com/Instance.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. AlloyDBClusterDataSourceProperties has a subset of AlloyDB
   * cluster properties that are useful at the Datasource level. Currently none
   * of its child properties are auditable. If new auditable properties are
   * added, the AUDIT annotation should be added.
   *
   * @param AlloyDBClusterDataSourceProperties $alloyDbClusterDatasourceProperties
   */
  public function setAlloyDbClusterDatasourceProperties(AlloyDBClusterDataSourceProperties $alloyDbClusterDatasourceProperties)
  {
    $this->alloyDbClusterDatasourceProperties = $alloyDbClusterDatasourceProperties;
  }
  /**
   * @return AlloyDBClusterDataSourceProperties
   */
  public function getAlloyDbClusterDatasourceProperties()
  {
    return $this->alloyDbClusterDatasourceProperties;
  }
  /**
   * Output only. CloudSqlInstanceDataSourceProperties has a subset of Cloud SQL
   * Instance properties that are useful at the Datasource level.
   *
   * @param CloudSqlInstanceDataSourceProperties $cloudSqlInstanceDatasourceProperties
   */
  public function setCloudSqlInstanceDatasourceProperties(CloudSqlInstanceDataSourceProperties $cloudSqlInstanceDatasourceProperties)
  {
    $this->cloudSqlInstanceDatasourceProperties = $cloudSqlInstanceDatasourceProperties;
  }
  /**
   * @return CloudSqlInstanceDataSourceProperties
   */
  public function getCloudSqlInstanceDatasourceProperties()
  {
    return $this->cloudSqlInstanceDatasourceProperties;
  }
  /**
   * ComputeInstanceDataSourceProperties has a subset of Compute Instance
   * properties that are useful at the Datasource level.
   *
   * @param ComputeInstanceDataSourceProperties $computeInstanceDatasourceProperties
   */
  public function setComputeInstanceDatasourceProperties(ComputeInstanceDataSourceProperties $computeInstanceDatasourceProperties)
  {
    $this->computeInstanceDatasourceProperties = $computeInstanceDatasourceProperties;
  }
  /**
   * @return ComputeInstanceDataSourceProperties
   */
  public function getComputeInstanceDatasourceProperties()
  {
    return $this->computeInstanceDatasourceProperties;
  }
  /**
   * DiskDataSourceProperties has a subset of Disk properties that are useful at
   * the Datasource level.
   *
   * @param DiskDataSourceProperties $diskDatasourceProperties
   */
  public function setDiskDatasourceProperties(DiskDataSourceProperties $diskDatasourceProperties)
  {
    $this->diskDatasourceProperties = $diskDatasourceProperties;
  }
  /**
   * @return DiskDataSourceProperties
   */
  public function getDiskDatasourceProperties()
  {
    return $this->diskDatasourceProperties;
  }
  /**
   * Output only. Full resource pathname URL of the source Google Cloud
   * resource.
   *
   * @param string $gcpResourcename
   */
  public function setGcpResourcename($gcpResourcename)
  {
    $this->gcpResourcename = $gcpResourcename;
  }
  /**
   * @return string
   */
  public function getGcpResourcename()
  {
    return $this->gcpResourcename;
  }
  /**
   * Location of the resource: //"global"/"unspecified".
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The type of the Google Cloud resource. Use the Unified Resource Type, eg.
   * compute.googleapis.com/Instance.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataSourceGcpResource::class, 'Google_Service_Backupdr_DataSourceGcpResource');
