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

namespace Google\Service\OracleDatabase;

class ExascaleDbStorageVault extends \Google\Model
{
  /**
   * Output only. The date and time when the ExascaleDbStorageVault was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The display name for the ExascaleDbStorageVault. The name does
   * not have to be unique within your project. The name must be 1-255
   * characters long and can only contain alphanumeric characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The ID of the subscription entitlement associated with the
   * ExascaleDbStorageVault.
   *
   * @var string
   */
  public $entitlementId;
  /**
   * Optional. The GCP Oracle zone where Oracle ExascaleDbStorageVault is
   * hosted. Example: us-east4-b-r2. If not specified, the system will pick a
   * zone based on availability.
   *
   * @var string
   */
  public $gcpOracleZone;
  /**
   * Optional. The labels or tags associated with the ExascaleDbStorageVault.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the ExascaleDbStorageVault. Format: projec
   * ts/{project}/locations/{location}/exascaleDbStorageVaults/{exascale_db_stor
   * age_vault}
   *
   * @var string
   */
  public $name;
  protected $propertiesType = ExascaleDbStorageVaultProperties::class;
  protected $propertiesDataType = '';

  /**
   * Output only. The date and time when the ExascaleDbStorageVault was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. The display name for the ExascaleDbStorageVault. The name does
   * not have to be unique within your project. The name must be 1-255
   * characters long and can only contain alphanumeric characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The ID of the subscription entitlement associated with the
   * ExascaleDbStorageVault.
   *
   * @param string $entitlementId
   */
  public function setEntitlementId($entitlementId)
  {
    $this->entitlementId = $entitlementId;
  }
  /**
   * @return string
   */
  public function getEntitlementId()
  {
    return $this->entitlementId;
  }
  /**
   * Optional. The GCP Oracle zone where Oracle ExascaleDbStorageVault is
   * hosted. Example: us-east4-b-r2. If not specified, the system will pick a
   * zone based on availability.
   *
   * @param string $gcpOracleZone
   */
  public function setGcpOracleZone($gcpOracleZone)
  {
    $this->gcpOracleZone = $gcpOracleZone;
  }
  /**
   * @return string
   */
  public function getGcpOracleZone()
  {
    return $this->gcpOracleZone;
  }
  /**
   * Optional. The labels or tags associated with the ExascaleDbStorageVault.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. The resource name of the ExascaleDbStorageVault. Format: projec
   * ts/{project}/locations/{location}/exascaleDbStorageVaults/{exascale_db_stor
   * age_vault}
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. The properties of the ExascaleDbStorageVault.
   *
   * @param ExascaleDbStorageVaultProperties $properties
   */
  public function setProperties(ExascaleDbStorageVaultProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return ExascaleDbStorageVaultProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExascaleDbStorageVault::class, 'Google_Service_OracleDatabase_ExascaleDbStorageVault');
