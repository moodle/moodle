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

namespace Google\Service\AIPlatformNotebooks;

class GceSetup extends \Google\Collection
{
  protected $collection_key = 'tags';
  protected $acceleratorConfigsType = AcceleratorConfig::class;
  protected $acceleratorConfigsDataType = 'array';
  protected $bootDiskType = BootDisk::class;
  protected $bootDiskDataType = '';
  protected $confidentialInstanceConfigType = ConfidentialInstanceConfig::class;
  protected $confidentialInstanceConfigDataType = '';
  protected $containerImageType = ContainerImage::class;
  protected $containerImageDataType = '';
  protected $dataDisksType = DataDisk::class;
  protected $dataDisksDataType = 'array';
  /**
   * Optional. If true, no external IP will be assigned to this VM instance.
   *
   * @var bool
   */
  public $disablePublicIp;
  /**
   * Optional. Flag to enable ip forwarding or not, default false/off.
   * https://cloud.google.com/vpc/docs/using-routes#canipforward
   *
   * @var bool
   */
  public $enableIpForwarding;
  protected $gpuDriverConfigType = GPUDriverConfig::class;
  protected $gpuDriverConfigDataType = '';
  /**
   * Output only. The unique ID of the Compute Engine instance resource.
   *
   * @var string
   */
  public $instanceId;
  /**
   * Optional. The machine type of the VM instance.
   * https://cloud.google.com/compute/docs/machine-resource
   *
   * @var string
   */
  public $machineType;
  /**
   * Optional. Custom metadata to apply to this instance.
   *
   * @var string[]
   */
  public $metadata;
  /**
   * Optional. The minimum CPU platform to use for this instance. The list of
   * valid values can be found in
   * https://cloud.google.com/compute/docs/instances/specify-min-cpu-
   * platform#availablezones
   *
   * @var string
   */
  public $minCpuPlatform;
  protected $networkInterfacesType = NetworkInterface::class;
  protected $networkInterfacesDataType = 'array';
  protected $reservationAffinityType = ReservationAffinity::class;
  protected $reservationAffinityDataType = '';
  protected $serviceAccountsType = ServiceAccount::class;
  protected $serviceAccountsDataType = 'array';
  protected $shieldedInstanceConfigType = ShieldedInstanceConfig::class;
  protected $shieldedInstanceConfigDataType = '';
  /**
   * Optional. The Compute Engine network tags to add to runtime (see [Add
   * network tags](https://cloud.google.com/vpc/docs/add-remove-network-tags)).
   *
   * @var string[]
   */
  public $tags;
  protected $vmImageType = VmImage::class;
  protected $vmImageDataType = '';

  /**
   * Optional. The hardware accelerators used on this instance. If you use
   * accelerators, make sure that your configuration has [enough vCPUs and
   * memory to support the `machine_type` you have
   * selected](https://cloud.google.com/compute/docs/gpus/#gpus-list). Currently
   * supports only one accelerator configuration.
   *
   * @param AcceleratorConfig[] $acceleratorConfigs
   */
  public function setAcceleratorConfigs($acceleratorConfigs)
  {
    $this->acceleratorConfigs = $acceleratorConfigs;
  }
  /**
   * @return AcceleratorConfig[]
   */
  public function getAcceleratorConfigs()
  {
    return $this->acceleratorConfigs;
  }
  /**
   * Optional. The boot disk for the VM.
   *
   * @param BootDisk $bootDisk
   */
  public function setBootDisk(BootDisk $bootDisk)
  {
    $this->bootDisk = $bootDisk;
  }
  /**
   * @return BootDisk
   */
  public function getBootDisk()
  {
    return $this->bootDisk;
  }
  /**
   * Optional. Confidential instance configuration.
   *
   * @param ConfidentialInstanceConfig $confidentialInstanceConfig
   */
  public function setConfidentialInstanceConfig(ConfidentialInstanceConfig $confidentialInstanceConfig)
  {
    $this->confidentialInstanceConfig = $confidentialInstanceConfig;
  }
  /**
   * @return ConfidentialInstanceConfig
   */
  public function getConfidentialInstanceConfig()
  {
    return $this->confidentialInstanceConfig;
  }
  /**
   * Optional. Use a container image to start the notebook instance.
   *
   * @param ContainerImage $containerImage
   */
  public function setContainerImage(ContainerImage $containerImage)
  {
    $this->containerImage = $containerImage;
  }
  /**
   * @return ContainerImage
   */
  public function getContainerImage()
  {
    return $this->containerImage;
  }
  /**
   * Optional. Data disks attached to the VM instance. Currently supports only
   * one data disk.
   *
   * @param DataDisk[] $dataDisks
   */
  public function setDataDisks($dataDisks)
  {
    $this->dataDisks = $dataDisks;
  }
  /**
   * @return DataDisk[]
   */
  public function getDataDisks()
  {
    return $this->dataDisks;
  }
  /**
   * Optional. If true, no external IP will be assigned to this VM instance.
   *
   * @param bool $disablePublicIp
   */
  public function setDisablePublicIp($disablePublicIp)
  {
    $this->disablePublicIp = $disablePublicIp;
  }
  /**
   * @return bool
   */
  public function getDisablePublicIp()
  {
    return $this->disablePublicIp;
  }
  /**
   * Optional. Flag to enable ip forwarding or not, default false/off.
   * https://cloud.google.com/vpc/docs/using-routes#canipforward
   *
   * @param bool $enableIpForwarding
   */
  public function setEnableIpForwarding($enableIpForwarding)
  {
    $this->enableIpForwarding = $enableIpForwarding;
  }
  /**
   * @return bool
   */
  public function getEnableIpForwarding()
  {
    return $this->enableIpForwarding;
  }
  /**
   * Optional. Configuration for GPU drivers.
   *
   * @param GPUDriverConfig $gpuDriverConfig
   */
  public function setGpuDriverConfig(GPUDriverConfig $gpuDriverConfig)
  {
    $this->gpuDriverConfig = $gpuDriverConfig;
  }
  /**
   * @return GPUDriverConfig
   */
  public function getGpuDriverConfig()
  {
    return $this->gpuDriverConfig;
  }
  /**
   * Output only. The unique ID of the Compute Engine instance resource.
   *
   * @param string $instanceId
   */
  public function setInstanceId($instanceId)
  {
    $this->instanceId = $instanceId;
  }
  /**
   * @return string
   */
  public function getInstanceId()
  {
    return $this->instanceId;
  }
  /**
   * Optional. The machine type of the VM instance.
   * https://cloud.google.com/compute/docs/machine-resource
   *
   * @param string $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return string
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
  /**
   * Optional. Custom metadata to apply to this instance.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Optional. The minimum CPU platform to use for this instance. The list of
   * valid values can be found in
   * https://cloud.google.com/compute/docs/instances/specify-min-cpu-
   * platform#availablezones
   *
   * @param string $minCpuPlatform
   */
  public function setMinCpuPlatform($minCpuPlatform)
  {
    $this->minCpuPlatform = $minCpuPlatform;
  }
  /**
   * @return string
   */
  public function getMinCpuPlatform()
  {
    return $this->minCpuPlatform;
  }
  /**
   * Optional. The network interfaces for the VM. Supports only one interface.
   *
   * @param NetworkInterface[] $networkInterfaces
   */
  public function setNetworkInterfaces($networkInterfaces)
  {
    $this->networkInterfaces = $networkInterfaces;
  }
  /**
   * @return NetworkInterface[]
   */
  public function getNetworkInterfaces()
  {
    return $this->networkInterfaces;
  }
  /**
   * Optional. Specifies the reservations that this instance can consume from.
   *
   * @param ReservationAffinity $reservationAffinity
   */
  public function setReservationAffinity(ReservationAffinity $reservationAffinity)
  {
    $this->reservationAffinity = $reservationAffinity;
  }
  /**
   * @return ReservationAffinity
   */
  public function getReservationAffinity()
  {
    return $this->reservationAffinity;
  }
  /**
   * Optional. The service account that serves as an identity for the VM
   * instance. Currently supports only one service account.
   *
   * @param ServiceAccount[] $serviceAccounts
   */
  public function setServiceAccounts($serviceAccounts)
  {
    $this->serviceAccounts = $serviceAccounts;
  }
  /**
   * @return ServiceAccount[]
   */
  public function getServiceAccounts()
  {
    return $this->serviceAccounts;
  }
  /**
   * Optional. Shielded VM configuration. [Images using supported Shielded VM
   * features](https://cloud.google.com/compute/docs/instances/modifying-
   * shielded-vm).
   *
   * @param ShieldedInstanceConfig $shieldedInstanceConfig
   */
  public function setShieldedInstanceConfig(ShieldedInstanceConfig $shieldedInstanceConfig)
  {
    $this->shieldedInstanceConfig = $shieldedInstanceConfig;
  }
  /**
   * @return ShieldedInstanceConfig
   */
  public function getShieldedInstanceConfig()
  {
    return $this->shieldedInstanceConfig;
  }
  /**
   * Optional. The Compute Engine network tags to add to runtime (see [Add
   * network tags](https://cloud.google.com/vpc/docs/add-remove-network-tags)).
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Optional. Use a Compute Engine VM image to start the notebook instance.
   *
   * @param VmImage $vmImage
   */
  public function setVmImage(VmImage $vmImage)
  {
    $this->vmImage = $vmImage;
  }
  /**
   * @return VmImage
   */
  public function getVmImage()
  {
    return $this->vmImage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GceSetup::class, 'Google_Service_AIPlatformNotebooks_GceSetup');
