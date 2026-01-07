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

namespace Google\Service\ChecksService;

class GoogleChecksReportV1alphaDataMonitoring extends \Google\Collection
{
  protected $collection_key = 'sdks';
  protected $dataTypesType = GoogleChecksReportV1alphaDataMonitoringDataTypeResult::class;
  protected $dataTypesDataType = 'array';
  protected $endpointsType = GoogleChecksReportV1alphaDataMonitoringEndpointResult::class;
  protected $endpointsDataType = 'array';
  protected $permissionsType = GoogleChecksReportV1alphaDataMonitoringPermissionResult::class;
  protected $permissionsDataType = 'array';
  protected $sdksType = GoogleChecksReportV1alphaDataMonitoringSdkResult::class;
  protected $sdksDataType = 'array';

  /**
   * Data types that your app shares or collects.
   *
   * @param GoogleChecksReportV1alphaDataMonitoringDataTypeResult[] $dataTypes
   */
  public function setDataTypes($dataTypes)
  {
    $this->dataTypes = $dataTypes;
  }
  /**
   * @return GoogleChecksReportV1alphaDataMonitoringDataTypeResult[]
   */
  public function getDataTypes()
  {
    return $this->dataTypes;
  }
  /**
   * Endpoints that were found by dynamic analysis of your app.
   *
   * @param GoogleChecksReportV1alphaDataMonitoringEndpointResult[] $endpoints
   */
  public function setEndpoints($endpoints)
  {
    $this->endpoints = $endpoints;
  }
  /**
   * @return GoogleChecksReportV1alphaDataMonitoringEndpointResult[]
   */
  public function getEndpoints()
  {
    return $this->endpoints;
  }
  /**
   * Permissions that your app uses.
   *
   * @param GoogleChecksReportV1alphaDataMonitoringPermissionResult[] $permissions
   */
  public function setPermissions($permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return GoogleChecksReportV1alphaDataMonitoringPermissionResult[]
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
  /**
   * SDKs that your app uses.
   *
   * @param GoogleChecksReportV1alphaDataMonitoringSdkResult[] $sdks
   */
  public function setSdks($sdks)
  {
    $this->sdks = $sdks;
  }
  /**
   * @return GoogleChecksReportV1alphaDataMonitoringSdkResult[]
   */
  public function getSdks()
  {
    return $this->sdks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksReportV1alphaDataMonitoring::class, 'Google_Service_ChecksService_GoogleChecksReportV1alphaDataMonitoring');
