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

class InstanceConfig extends \Google\Collection
{
  /**
   * The unspecified network configuration.
   */
  public const NETWORK_CONFIG_NETWORKCONFIG_UNSPECIFIED = 'NETWORKCONFIG_UNSPECIFIED';
  /**
   * Instance part of single client network and single private network.
   */
  public const NETWORK_CONFIG_SINGLE_VLAN = 'SINGLE_VLAN';
  /**
   * Instance part of multiple (or single) client networks and private networks.
   */
  public const NETWORK_CONFIG_MULTI_VLAN = 'MULTI_VLAN';
  protected $collection_key = 'sshKeyNames';
  /**
   * If true networks can be from different projects of the same vendor account.
   *
   * @var bool
   */
  public $accountNetworksEnabled;
  protected $clientNetworkType = NetworkAddress::class;
  protected $clientNetworkDataType = '';
  /**
   * Whether the instance should be provisioned with Hyperthreading enabled.
   *
   * @var bool
   */
  public $hyperthreading;
  /**
   * A transient unique identifier to identify an instance within an
   * ProvisioningConfig request.
   *
   * @deprecated
   * @var string
   */
  public $id;
  /**
   * Instance type. [Available types](https://cloud.google.com/bare-
   * metal/docs/bms-planning#server_configurations)
   *
   * @var string
   */
  public $instanceType;
  /**
   * Name of the KMS crypto key version used to encrypt the initial passwords.
   * The key has to have ASYMMETRIC_DECRYPT purpose.
   *
   * @var string
   */
  public $kmsKeyVersion;
  protected $logicalInterfacesType = GoogleCloudBaremetalsolutionV2LogicalInterface::class;
  protected $logicalInterfacesDataType = 'array';
  /**
   * The name of the instance config.
   *
   * @var string
   */
  public $name;
  /**
   * The type of network configuration on the instance.
   *
   * @var string
   */
  public $networkConfig;
  /**
   * Server network template name. Filled if InstanceConfig.multivlan_config is
   * true.
   *
   * @var string
   */
  public $networkTemplate;
  /**
   * OS image to initialize the instance. [Available
   * images](https://cloud.google.com/bare-metal/docs/bms-
   * planning#server_configurations)
   *
   * @var string
   */
  public $osImage;
  protected $privateNetworkType = NetworkAddress::class;
  protected $privateNetworkDataType = '';
  /**
   * Optional. List of names of ssh keys used to provision the instance.
   *
   * @var string[]
   */
  public $sshKeyNames;
  /**
   * User note field, it can be used by customers to add additional information
   * for the BMS Ops team .
   *
   * @var string
   */
  public $userNote;

  /**
   * If true networks can be from different projects of the same vendor account.
   *
   * @param bool $accountNetworksEnabled
   */
  public function setAccountNetworksEnabled($accountNetworksEnabled)
  {
    $this->accountNetworksEnabled = $accountNetworksEnabled;
  }
  /**
   * @return bool
   */
  public function getAccountNetworksEnabled()
  {
    return $this->accountNetworksEnabled;
  }
  /**
   * Client network address. Filled if InstanceConfig.multivlan_config is false.
   *
   * @deprecated
   * @param NetworkAddress $clientNetwork
   */
  public function setClientNetwork(NetworkAddress $clientNetwork)
  {
    $this->clientNetwork = $clientNetwork;
  }
  /**
   * @deprecated
   * @return NetworkAddress
   */
  public function getClientNetwork()
  {
    return $this->clientNetwork;
  }
  /**
   * Whether the instance should be provisioned with Hyperthreading enabled.
   *
   * @param bool $hyperthreading
   */
  public function setHyperthreading($hyperthreading)
  {
    $this->hyperthreading = $hyperthreading;
  }
  /**
   * @return bool
   */
  public function getHyperthreading()
  {
    return $this->hyperthreading;
  }
  /**
   * A transient unique identifier to identify an instance within an
   * ProvisioningConfig request.
   *
   * @deprecated
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Instance type. [Available types](https://cloud.google.com/bare-
   * metal/docs/bms-planning#server_configurations)
   *
   * @param string $instanceType
   */
  public function setInstanceType($instanceType)
  {
    $this->instanceType = $instanceType;
  }
  /**
   * @return string
   */
  public function getInstanceType()
  {
    return $this->instanceType;
  }
  /**
   * Name of the KMS crypto key version used to encrypt the initial passwords.
   * The key has to have ASYMMETRIC_DECRYPT purpose.
   *
   * @param string $kmsKeyVersion
   */
  public function setKmsKeyVersion($kmsKeyVersion)
  {
    $this->kmsKeyVersion = $kmsKeyVersion;
  }
  /**
   * @return string
   */
  public function getKmsKeyVersion()
  {
    return $this->kmsKeyVersion;
  }
  /**
   * List of logical interfaces for the instance. The number of logical
   * interfaces will be the same as number of hardware bond/nic on the chosen
   * network template. Filled if InstanceConfig.multivlan_config is true.
   *
   * @param GoogleCloudBaremetalsolutionV2LogicalInterface[] $logicalInterfaces
   */
  public function setLogicalInterfaces($logicalInterfaces)
  {
    $this->logicalInterfaces = $logicalInterfaces;
  }
  /**
   * @return GoogleCloudBaremetalsolutionV2LogicalInterface[]
   */
  public function getLogicalInterfaces()
  {
    return $this->logicalInterfaces;
  }
  /**
   * The name of the instance config.
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
   * The type of network configuration on the instance.
   *
   * Accepted values: NETWORKCONFIG_UNSPECIFIED, SINGLE_VLAN, MULTI_VLAN
   *
   * @param self::NETWORK_CONFIG_* $networkConfig
   */
  public function setNetworkConfig($networkConfig)
  {
    $this->networkConfig = $networkConfig;
  }
  /**
   * @return self::NETWORK_CONFIG_*
   */
  public function getNetworkConfig()
  {
    return $this->networkConfig;
  }
  /**
   * Server network template name. Filled if InstanceConfig.multivlan_config is
   * true.
   *
   * @param string $networkTemplate
   */
  public function setNetworkTemplate($networkTemplate)
  {
    $this->networkTemplate = $networkTemplate;
  }
  /**
   * @return string
   */
  public function getNetworkTemplate()
  {
    return $this->networkTemplate;
  }
  /**
   * OS image to initialize the instance. [Available
   * images](https://cloud.google.com/bare-metal/docs/bms-
   * planning#server_configurations)
   *
   * @param string $osImage
   */
  public function setOsImage($osImage)
  {
    $this->osImage = $osImage;
  }
  /**
   * @return string
   */
  public function getOsImage()
  {
    return $this->osImage;
  }
  /**
   * Private network address, if any. Filled if InstanceConfig.multivlan_config
   * is false.
   *
   * @deprecated
   * @param NetworkAddress $privateNetwork
   */
  public function setPrivateNetwork(NetworkAddress $privateNetwork)
  {
    $this->privateNetwork = $privateNetwork;
  }
  /**
   * @deprecated
   * @return NetworkAddress
   */
  public function getPrivateNetwork()
  {
    return $this->privateNetwork;
  }
  /**
   * Optional. List of names of ssh keys used to provision the instance.
   *
   * @param string[] $sshKeyNames
   */
  public function setSshKeyNames($sshKeyNames)
  {
    $this->sshKeyNames = $sshKeyNames;
  }
  /**
   * @return string[]
   */
  public function getSshKeyNames()
  {
    return $this->sshKeyNames;
  }
  /**
   * User note field, it can be used by customers to add additional information
   * for the BMS Ops team .
   *
   * @param string $userNote
   */
  public function setUserNote($userNote)
  {
    $this->userNote = $userNote;
  }
  /**
   * @return string
   */
  public function getUserNote()
  {
    return $this->userNote;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceConfig::class, 'Google_Service_Baremetalsolution_InstanceConfig');
