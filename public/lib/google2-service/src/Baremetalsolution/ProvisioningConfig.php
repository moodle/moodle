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

namespace Google\Service\Baremetalsolution;

class ProvisioningConfig extends \Google\Collection
{
  /**
   * State wasn't specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * ProvisioningConfig is a draft and can be freely modified.
   */
  public const STATE_DRAFT = 'DRAFT';
  /**
   * ProvisioningConfig was already submitted and cannot be modified.
   */
  public const STATE_SUBMITTED = 'SUBMITTED';
  /**
   * ProvisioningConfig was in the provisioning state. Initially this state
   * comes from the work order table in big query when SNOW is used. Later this
   * field can be set by the work order API.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * ProvisioningConfig was provisioned, meaning the resources exist.
   */
  public const STATE_PROVISIONED = 'PROVISIONED';
  /**
   * ProvisioningConfig was validated. A validation tool will be run to set this
   * state.
   */
  public const STATE_VALIDATED = 'VALIDATED';
  /**
   * ProvisioningConfig was canceled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The request is submitted for provisioning, with error return.
   */
  public const STATE_FAILED = 'FAILED';
  protected $collection_key = 'volumes';
  /**
   * Output only. URI to Cloud Console UI view of this provisioning config.
   *
   * @var string
   */
  public $cloudConsoleUri;
  /**
   * Optional. The user-defined identifier of the provisioning config.
   *
   * @var string
   */
  public $customId;
  /**
   * Email provided to send a confirmation with provisioning config to.
   * Deprecated in favour of email field in request messages.
   *
   * @deprecated
   * @var string
   */
  public $email;
  /**
   * A service account to enable customers to access instance credentials upon
   * handover.
   *
   * @var string
   */
  public $handoverServiceAccount;
  protected $instancesType = InstanceConfig::class;
  protected $instancesDataType = 'array';
  /**
   * Optional. Location name of this ProvisioningConfig. It is optional only for
   * Intake UI transition period.
   *
   * @var string
   */
  public $location;
  /**
   * Output only. The system-generated name of the provisioning config. This
   * follows the UUID format.
   *
   * @var string
   */
  public $name;
  protected $networksType = NetworkConfig::class;
  protected $networksDataType = 'array';
  /**
   * Optional. Pod name. Pod is an independent part of infrastructure. Instance
   * can be connected to the assets (networks, volumes, nfsshares) allocated in
   * the same pod only.
   *
   * @var string
   */
  public $pod;
  /**
   * Output only. State of ProvisioningConfig.
   *
   * @var string
   */
  public $state;
  /**
   * Optional status messages associated with the FAILED state.
   *
   * @var string
   */
  public $statusMessage;
  /**
   * A generated ticket id to track provisioning request.
   *
   * @var string
   */
  public $ticketId;
  /**
   * Output only. Last update timestamp.
   *
   * @var string
   */
  public $updateTime;
  protected $volumesType = VolumeConfig::class;
  protected $volumesDataType = 'array';
  /**
   * If true, VPC SC is enabled for the cluster.
   *
   * @var bool
   */
  public $vpcScEnabled;

  /**
   * Output only. URI to Cloud Console UI view of this provisioning config.
   *
   * @param string $cloudConsoleUri
   */
  public function setCloudConsoleUri($cloudConsoleUri)
  {
    $this->cloudConsoleUri = $cloudConsoleUri;
  }
  /**
   * @return string
   */
  public function getCloudConsoleUri()
  {
    return $this->cloudConsoleUri;
  }
  /**
   * Optional. The user-defined identifier of the provisioning config.
   *
   * @param string $customId
   */
  public function setCustomId($customId)
  {
    $this->customId = $customId;
  }
  /**
   * @return string
   */
  public function getCustomId()
  {
    return $this->customId;
  }
  /**
   * Email provided to send a confirmation with provisioning config to.
   * Deprecated in favour of email field in request messages.
   *
   * @deprecated
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * A service account to enable customers to access instance credentials upon
   * handover.
   *
   * @param string $handoverServiceAccount
   */
  public function setHandoverServiceAccount($handoverServiceAccount)
  {
    $this->handoverServiceAccount = $handoverServiceAccount;
  }
  /**
   * @return string
   */
  public function getHandoverServiceAccount()
  {
    return $this->handoverServiceAccount;
  }
  /**
   * Instances to be created.
   *
   * @param InstanceConfig[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return InstanceConfig[]
   */
  public function getInstances()
  {
    return $this->instances;
  }
  /**
   * Optional. Location name of this ProvisioningConfig. It is optional only for
   * Intake UI transition period.
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
   * Output only. The system-generated name of the provisioning config. This
   * follows the UUID format.
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
   * Networks to be created.
   *
   * @param NetworkConfig[] $networks
   */
  public function setNetworks($networks)
  {
    $this->networks = $networks;
  }
  /**
   * @return NetworkConfig[]
   */
  public function getNetworks()
  {
    return $this->networks;
  }
  /**
   * Optional. Pod name. Pod is an independent part of infrastructure. Instance
   * can be connected to the assets (networks, volumes, nfsshares) allocated in
   * the same pod only.
   *
   * @param string $pod
   */
  public function setPod($pod)
  {
    $this->pod = $pod;
  }
  /**
   * @return string
   */
  public function getPod()
  {
    return $this->pod;
  }
  /**
   * Output only. State of ProvisioningConfig.
   *
   * Accepted values: STATE_UNSPECIFIED, DRAFT, SUBMITTED, PROVISIONING,
   * PROVISIONED, VALIDATED, CANCELLED, FAILED
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
   * Optional status messages associated with the FAILED state.
   *
   * @param string $statusMessage
   */
  public function setStatusMessage($statusMessage)
  {
    $this->statusMessage = $statusMessage;
  }
  /**
   * @return string
   */
  public function getStatusMessage()
  {
    return $this->statusMessage;
  }
  /**
   * A generated ticket id to track provisioning request.
   *
   * @param string $ticketId
   */
  public function setTicketId($ticketId)
  {
    $this->ticketId = $ticketId;
  }
  /**
   * @return string
   */
  public function getTicketId()
  {
    return $this->ticketId;
  }
  /**
   * Output only. Last update timestamp.
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
   * Volumes to be created.
   *
   * @param VolumeConfig[] $volumes
   */
  public function setVolumes($volumes)
  {
    $this->volumes = $volumes;
  }
  /**
   * @return VolumeConfig[]
   */
  public function getVolumes()
  {
    return $this->volumes;
  }
  /**
   * If true, VPC SC is enabled for the cluster.
   *
   * @param bool $vpcScEnabled
   */
  public function setVpcScEnabled($vpcScEnabled)
  {
    $this->vpcScEnabled = $vpcScEnabled;
  }
  /**
   * @return bool
   */
  public function getVpcScEnabled()
  {
    return $this->vpcScEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProvisioningConfig::class, 'Google_Service_Baremetalsolution_ProvisioningConfig');
