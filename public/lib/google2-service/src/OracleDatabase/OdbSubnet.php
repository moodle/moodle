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

class OdbSubnet extends \Google\Model
{
  /**
   * Default unspecified value.
   */
  public const PURPOSE_PURPOSE_UNSPECIFIED = 'PURPOSE_UNSPECIFIED';
  /**
   * Subnet to be used for client connections.
   */
  public const PURPOSE_CLIENT_SUBNET = 'CLIENT_SUBNET';
  /**
   * Subnet to be used for backup.
   */
  public const PURPOSE_BACKUP_SUBNET = 'BACKUP_SUBNET';
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
   * Required. The CIDR range of the subnet.
   *
   * @var string
   */
  public $cidrRange;
  /**
   * Output only. The date and time that the OdbNetwork was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Labels or tags associated with the resource.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The name of the OdbSubnet resource in the following format: pro
   * jects/{project}/locations/{location}/odbNetworks/{odb_network}/odbSubnets/{
   * odb_subnet}
   *
   * @var string
   */
  public $name;
  /**
   * Required. Purpose of the subnet.
   *
   * @var string
   */
  public $purpose;
  /**
   * Output only. State of the ODB Subnet.
   *
   * @var string
   */
  public $state;

  /**
   * Required. The CIDR range of the subnet.
   *
   * @param string $cidrRange
   */
  public function setCidrRange($cidrRange)
  {
    $this->cidrRange = $cidrRange;
  }
  /**
   * @return string
   */
  public function getCidrRange()
  {
    return $this->cidrRange;
  }
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
   * Identifier. The name of the OdbSubnet resource in the following format: pro
   * jects/{project}/locations/{location}/odbNetworks/{odb_network}/odbSubnets/{
   * odb_subnet}
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
   * Required. Purpose of the subnet.
   *
   * Accepted values: PURPOSE_UNSPECIFIED, CLIENT_SUBNET, BACKUP_SUBNET
   *
   * @param self::PURPOSE_* $purpose
   */
  public function setPurpose($purpose)
  {
    $this->purpose = $purpose;
  }
  /**
   * @return self::PURPOSE_*
   */
  public function getPurpose()
  {
    return $this->purpose;
  }
  /**
   * Output only. State of the ODB Subnet.
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
class_alias(OdbSubnet::class, 'Google_Service_OracleDatabase_OdbSubnet');
