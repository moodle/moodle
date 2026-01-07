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

class DataSourceGcpResourceInfo extends \Google\Model
{
  protected $cloudSqlInstancePropertiesType = CloudSqlInstanceDataSourceReferenceProperties::class;
  protected $cloudSqlInstancePropertiesDataType = '';
  /**
   * Output only. The resource name of the GCP resource. Ex:
   * projects/{project}/zones/{zone}/instances/{instance}
   *
   * @var string
   */
  public $gcpResourcename;
  /**
   * Output only. The location of the GCP resource. Ex: //"global"/"unspecified"
   *
   * @var string
   */
  public $location;
  /**
   * Output only. The type of the GCP resource. Ex:
   * compute.googleapis.com/Instance
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The properties of the Cloud SQL instance.
   *
   * @param CloudSqlInstanceDataSourceReferenceProperties $cloudSqlInstanceProperties
   */
  public function setCloudSqlInstanceProperties(CloudSqlInstanceDataSourceReferenceProperties $cloudSqlInstanceProperties)
  {
    $this->cloudSqlInstanceProperties = $cloudSqlInstanceProperties;
  }
  /**
   * @return CloudSqlInstanceDataSourceReferenceProperties
   */
  public function getCloudSqlInstanceProperties()
  {
    return $this->cloudSqlInstanceProperties;
  }
  /**
   * Output only. The resource name of the GCP resource. Ex:
   * projects/{project}/zones/{zone}/instances/{instance}
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
   * Output only. The location of the GCP resource. Ex: //"global"/"unspecified"
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
   * Output only. The type of the GCP resource. Ex:
   * compute.googleapis.com/Instance
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
class_alias(DataSourceGcpResourceInfo::class, 'Google_Service_Backupdr_DataSourceGcpResourceInfo');
