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

class RestoreBackupRequest extends \Google\Model
{
  /**
   * Optional. A field mask used to clear server-side default values for fields
   * within the `instance_properties` oneof. When a field in this mask is
   * cleared, the server will not apply its default logic (like inheriting a
   * value from the source) for that field. The most common current use case is
   * clearing default encryption keys. Examples of field mask paths: - Compute
   * Instance Disks:
   * `compute_instance_restore_properties.disks.*.disk_encryption_key` - Single
   * Disk: `disk_restore_properties.disk_encryption_key`
   *
   * @var string
   */
  public $clearOverridesFieldMask;
  protected $computeInstanceRestorePropertiesType = ComputeInstanceRestoreProperties::class;
  protected $computeInstanceRestorePropertiesDataType = '';
  protected $computeInstanceTargetEnvironmentType = ComputeInstanceTargetEnvironment::class;
  protected $computeInstanceTargetEnvironmentDataType = '';
  protected $diskRestorePropertiesType = DiskRestoreProperties::class;
  protected $diskRestorePropertiesDataType = '';
  protected $diskTargetEnvironmentType = DiskTargetEnvironment::class;
  protected $diskTargetEnvironmentDataType = '';
  protected $regionDiskTargetEnvironmentType = RegionDiskTargetEnvironment::class;
  protected $regionDiskTargetEnvironmentDataType = '';
  /**
   * Optional. An optional request ID to identify requests. Specify a unique
   * request ID so that if you must retry your request, the server will know to
   * ignore the request if it has already been completed. The server will
   * guarantee that for at least 60 minutes after the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @var string
   */
  public $requestId;

  /**
   * Optional. A field mask used to clear server-side default values for fields
   * within the `instance_properties` oneof. When a field in this mask is
   * cleared, the server will not apply its default logic (like inheriting a
   * value from the source) for that field. The most common current use case is
   * clearing default encryption keys. Examples of field mask paths: - Compute
   * Instance Disks:
   * `compute_instance_restore_properties.disks.*.disk_encryption_key` - Single
   * Disk: `disk_restore_properties.disk_encryption_key`
   *
   * @param string $clearOverridesFieldMask
   */
  public function setClearOverridesFieldMask($clearOverridesFieldMask)
  {
    $this->clearOverridesFieldMask = $clearOverridesFieldMask;
  }
  /**
   * @return string
   */
  public function getClearOverridesFieldMask()
  {
    return $this->clearOverridesFieldMask;
  }
  /**
   * Compute Engine instance properties to be overridden during restore.
   *
   * @param ComputeInstanceRestoreProperties $computeInstanceRestoreProperties
   */
  public function setComputeInstanceRestoreProperties(ComputeInstanceRestoreProperties $computeInstanceRestoreProperties)
  {
    $this->computeInstanceRestoreProperties = $computeInstanceRestoreProperties;
  }
  /**
   * @return ComputeInstanceRestoreProperties
   */
  public function getComputeInstanceRestoreProperties()
  {
    return $this->computeInstanceRestoreProperties;
  }
  /**
   * Compute Engine target environment to be used during restore.
   *
   * @param ComputeInstanceTargetEnvironment $computeInstanceTargetEnvironment
   */
  public function setComputeInstanceTargetEnvironment(ComputeInstanceTargetEnvironment $computeInstanceTargetEnvironment)
  {
    $this->computeInstanceTargetEnvironment = $computeInstanceTargetEnvironment;
  }
  /**
   * @return ComputeInstanceTargetEnvironment
   */
  public function getComputeInstanceTargetEnvironment()
  {
    return $this->computeInstanceTargetEnvironment;
  }
  /**
   * Disk properties to be overridden during restore.
   *
   * @param DiskRestoreProperties $diskRestoreProperties
   */
  public function setDiskRestoreProperties(DiskRestoreProperties $diskRestoreProperties)
  {
    $this->diskRestoreProperties = $diskRestoreProperties;
  }
  /**
   * @return DiskRestoreProperties
   */
  public function getDiskRestoreProperties()
  {
    return $this->diskRestoreProperties;
  }
  /**
   * Disk target environment to be used during restore.
   *
   * @param DiskTargetEnvironment $diskTargetEnvironment
   */
  public function setDiskTargetEnvironment(DiskTargetEnvironment $diskTargetEnvironment)
  {
    $this->diskTargetEnvironment = $diskTargetEnvironment;
  }
  /**
   * @return DiskTargetEnvironment
   */
  public function getDiskTargetEnvironment()
  {
    return $this->diskTargetEnvironment;
  }
  /**
   * Region disk target environment to be used during restore.
   *
   * @param RegionDiskTargetEnvironment $regionDiskTargetEnvironment
   */
  public function setRegionDiskTargetEnvironment(RegionDiskTargetEnvironment $regionDiskTargetEnvironment)
  {
    $this->regionDiskTargetEnvironment = $regionDiskTargetEnvironment;
  }
  /**
   * @return RegionDiskTargetEnvironment
   */
  public function getRegionDiskTargetEnvironment()
  {
    return $this->regionDiskTargetEnvironment;
  }
  /**
   * Optional. An optional request ID to identify requests. Specify a unique
   * request ID so that if you must retry your request, the server will know to
   * ignore the request if it has already been completed. The server will
   * guarantee that for at least 60 minutes after the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RestoreBackupRequest::class, 'Google_Service_Backupdr_RestoreBackupRequest');
