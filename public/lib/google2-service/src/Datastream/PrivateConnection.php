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

namespace Google\Service\Datastream;

class PrivateConnection extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The private connection is in creation state - creating resources.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The private connection has been created with all of its resources.
   */
  public const STATE_CREATED = 'CREATED';
  /**
   * The private connection creation has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The private connection is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Delete request has failed, resource is in invalid state.
   */
  public const STATE_FAILED_TO_DELETE = 'FAILED_TO_DELETE';
  /**
   * Output only. The create time of the resource.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Display name.
   *
   * @var string
   */
  public $displayName;
  protected $errorType = Error::class;
  protected $errorDataType = '';
  /**
   * Labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Identifier. The resource's name.
   *
   * @var string
   */
  public $name;
  protected $pscInterfaceConfigType = PscInterfaceConfig::class;
  protected $pscInterfaceConfigDataType = '';
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. The state of the Private Connection.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The update time of the resource.
   *
   * @var string
   */
  public $updateTime;
  protected $vpcPeeringConfigType = VpcPeeringConfig::class;
  protected $vpcPeeringConfigDataType = '';

  /**
   * Output only. The create time of the resource.
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
   * Required. Display name.
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
   * Output only. In case of error, the details of the error in a user-friendly
   * format.
   *
   * @param Error $error
   */
  public function setError(Error $error)
  {
    $this->error = $error;
  }
  /**
   * @return Error
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Labels.
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
   * Output only. Identifier. The resource's name.
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
   * PSC Interface Config.
   *
   * @param PscInterfaceConfig $pscInterfaceConfig
   */
  public function setPscInterfaceConfig(PscInterfaceConfig $pscInterfaceConfig)
  {
    $this->pscInterfaceConfig = $pscInterfaceConfig;
  }
  /**
   * @return PscInterfaceConfig
   */
  public function getPscInterfaceConfig()
  {
    return $this->pscInterfaceConfig;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. The state of the Private Connection.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, CREATED, FAILED, DELETING,
   * FAILED_TO_DELETE
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
  /**
   * Output only. The update time of the resource.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * VPC Peering Config.
   *
   * @param VpcPeeringConfig $vpcPeeringConfig
   */
  public function setVpcPeeringConfig(VpcPeeringConfig $vpcPeeringConfig)
  {
    $this->vpcPeeringConfig = $vpcPeeringConfig;
  }
  /**
   * @return VpcPeeringConfig
   */
  public function getVpcPeeringConfig()
  {
    return $this->vpcPeeringConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrivateConnection::class, 'Google_Service_Datastream_PrivateConnection');
