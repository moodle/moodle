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

class OdbNetwork extends \Google\Model
{
  /**
   * Default unspecified value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Indicates that the resource is in provisioning state.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * Indicates that the resource is in available state.
   */
  public const STATE_AVAILABLE = 'AVAILABLE';
  /**
   * Indicates that the resource is in terminating state.
   */
  public const STATE_TERMINATING = 'TERMINATING';
  /**
   * Indicates that the resource is in failed state.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Output only. The date and time that the OdbNetwork was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The ID of the subscription entitlement associated with the
   * OdbNetwork.
   *
   * @var string
   */
  public $entitlementId;
  /**
   * Optional. The GCP Oracle zone where OdbNetwork is hosted. Example: us-
   * east4-b-r2. If not specified, the system will pick a zone based on
   * availability.
   *
   * @var string
   */
  public $gcpOracleZone;
  /**
   * Optional. Labels or tags associated with the resource.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The name of the OdbNetwork resource in the following format:
   * projects/{project}/locations/{region}/odbNetworks/{odb_network}
   *
   * @var string
   */
  public $name;
  /**
   * Required. The name of the VPC network in the following format:
   * projects/{project}/global/networks/{network}
   *
   * @var string
   */
  public $network;
  /**
   * Output only. State of the ODB Network.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The date and time that the OdbNetwork was created.
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
   * Output only. The ID of the subscription entitlement associated with the
   * OdbNetwork.
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
   * Optional. The GCP Oracle zone where OdbNetwork is hosted. Example: us-
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
   * Optional. Labels or tags associated with the resource.
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
   * Identifier. The name of the OdbNetwork resource in the following format:
   * projects/{project}/locations/{region}/odbNetworks/{odb_network}
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
   * Required. The name of the VPC network in the following format:
   * projects/{project}/global/networks/{network}
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Output only. State of the ODB Network.
   *
   * Accepted values: STATE_UNSPECIFIED, PROVISIONING, AVAILABLE, TERMINATING,
   * FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OdbNetwork::class, 'Google_Service_OracleDatabase_OdbNetwork');
