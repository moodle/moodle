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

class DbSystem extends \Google\Model
{
  /**
   * Output only. The date and time that the DbSystem was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The display name for the System db. The name does not have to be
   * unique within your project.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The ID of the subscription entitlement associated with the
   * DbSystem
   *
   * @var string
   */
  public $entitlementId;
  /**
   * Optional. The GCP Oracle zone where Oracle DbSystem is hosted. Example: us-
   * east4-b-r2. If not specified, the system will pick a zone based on
   * availability.
   *
   * @var string
   */
  public $gcpOracleZone;
  /**
   * Optional. The labels or tags associated with the DbSystem.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The name of the DbSystem resource in the following format:
   * projects/{project}/locations/{region}/dbSystems/{db_system}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. HTTPS link to OCI resources exposed to Customer via UI
   * Interface.
   *
   * @var string
   */
  public $ociUrl;
  /**
   * Optional. The name of the OdbNetwork associated with the DbSystem. Format:
   * projects/{project}/locations/{location}/odbNetworks/{odb_network} It is
   * optional but if specified, this should match the parent ODBNetwork of the
   * OdbSubnet.
   *
   * @var string
   */
  public $odbNetwork;
  /**
   * Required. The name of the OdbSubnet associated with the DbSystem for IP
   * allocation. Format: projects/{project}/locations/{location}/odbNetworks/{od
   * b_network}/odbSubnets/{odb_subnet}
   *
   * @var string
   */
  public $odbSubnet;
  protected $propertiesType = DbSystemProperties::class;
  protected $propertiesDataType = '';

  /**
   * Output only. The date and time that the DbSystem was created.
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
   * Required. The display name for the System db. The name does not have to be
   * unique within your project.
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
   * DbSystem
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
   * Optional. The GCP Oracle zone where Oracle DbSystem is hosted. Example: us-
   * east4-b-r2. If not specified, the system will pick a zone based on
   * availability.
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
   * Optional. The labels or tags associated with the DbSystem.
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
   * Identifier. The name of the DbSystem resource in the following format:
   * projects/{project}/locations/{region}/dbSystems/{db_system}
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
   * Output only. HTTPS link to OCI resources exposed to Customer via UI
   * Interface.
   *
   * @param string $ociUrl
   */
  public function setOciUrl($ociUrl)
  {
    $this->ociUrl = $ociUrl;
  }
  /**
   * @return string
   */
  public function getOciUrl()
  {
    return $this->ociUrl;
  }
  /**
   * Optional. The name of the OdbNetwork associated with the DbSystem. Format:
   * projects/{project}/locations/{location}/odbNetworks/{odb_network} It is
   * optional but if specified, this should match the parent ODBNetwork of the
   * OdbSubnet.
   *
   * @param string $odbNetwork
   */
  public function setOdbNetwork($odbNetwork)
  {
    $this->odbNetwork = $odbNetwork;
  }
  /**
   * @return string
   */
  public function getOdbNetwork()
  {
    return $this->odbNetwork;
  }
  /**
   * Required. The name of the OdbSubnet associated with the DbSystem for IP
   * allocation. Format: projects/{project}/locations/{location}/odbNetworks/{od
   * b_network}/odbSubnets/{odb_subnet}
   *
   * @param string $odbSubnet
   */
  public function setOdbSubnet($odbSubnet)
  {
    $this->odbSubnet = $odbSubnet;
  }
  /**
   * @return string
   */
  public function getOdbSubnet()
  {
    return $this->odbSubnet;
  }
  /**
   * Optional. The properties of the DbSystem.
   *
   * @param DbSystemProperties $properties
   */
  public function setProperties(DbSystemProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return DbSystemProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DbSystem::class, 'Google_Service_OracleDatabase_DbSystem');
