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

namespace Google\Service\CloudWorkstations;

class GceInstance extends \Google\Collection
{
  protected $collection_key = 'tags';
  protected $acceleratorsType = Accelerator::class;
  protected $acceleratorsDataType = 'array';
  protected $boostConfigsType = BoostConfig::class;
  protected $boostConfigsDataType = 'array';
  /**
   * Optional. The size of the boot disk for the VM in gigabytes (GB). The
   * minimum boot disk size is `30` GB. Defaults to `50` GB.
   *
   * @var int
   */
  public $bootDiskSizeGb;
  protected $confidentialInstanceConfigType = GceConfidentialInstanceConfig::class;
  protected $confidentialInstanceConfigDataType = '';
  /**
   * Optional. When set to true, disables public IP addresses for VMs. If you
   * disable public IP addresses, you must set up Private Google Access or Cloud
   * NAT on your network. If you use Private Google Access and you use
   * `private.googleapis.com` or `restricted.googleapis.com` for Container
   * Registry and Artifact Registry, make sure that you set up DNS records for
   * domains `*.gcr.io` and `*.pkg.dev`. Defaults to false (VMs have public IP
   * addresses).
   *
   * @var bool
   */
  public $disablePublicIpAddresses;
  /**
   * Optional. Whether to disable SSH access to the VM.
   *
   * @var bool
   */
  public $disableSsh;
  /**
   * Optional. Whether to enable nested virtualization on Cloud Workstations VMs
   * created using this workstation configuration. Defaults to false. Nested
   * virtualization lets you run virtual machine (VM) instances inside your
   * workstation. Before enabling nested virtualization, consider the following
   * important considerations. Cloud Workstations instances are subject to the
   * [same restrictions as Compute Engine
   * instances](https://cloud.google.com/compute/docs/instances/nested-
   * virtualization/overview#restrictions): * **Organization policy**: projects,
   * folders, or organizations may be restricted from creating nested VMs if the
   * **Disable VM nested virtualization** constraint is enforced in the
   * organization policy. For more information, see the Compute Engine section,
   * [Checking whether nested virtualization is
   * allowed](https://cloud.google.com/compute/docs/instances/nested-
   * virtualization/managing-
   * constraint#checking_whether_nested_virtualization_is_allowed). *
   * **Performance**: nested VMs might experience a 10% or greater decrease in
   * performance for workloads that are CPU-bound and possibly greater than a
   * 10% decrease for workloads that are input/output bound. * **Machine Type**:
   * nested virtualization can only be enabled on workstation configurations
   * that specify a machine_type in the N1 or N2 machine series.
   *
   * @var bool
   */
  public $enableNestedVirtualization;
  /**
   * Optional. The type of machine to use for VM instances—for example,
   * `"e2-standard-4"`. For more information about machine types that Cloud
   * Workstations supports, see the list of [available machine
   * types](https://cloud.google.com/workstations/docs/available-machine-types).
   *
   * @var string
   */
  public $machineType;
  /**
   * Optional. The number of VMs that the system should keep idle so that new
   * workstations can be started quickly for new users. Defaults to `0` in the
   * API.
   *
   * @var int
   */
  public $poolSize;
  /**
   * Output only. Number of instances currently available in the pool for faster
   * workstation startup.
   *
   * @var int
   */
  public $pooledInstances;
  /**
   * Optional. The email address of the service account for Cloud Workstations
   * VMs created with this configuration. When specified, be sure that the
   * service account has `logging.logEntries.create` and
   * `monitoring.timeSeries.create` permissions on the project so it can write
   * logs out to Cloud Logging. If using a custom container image, the service
   * account must have [Artifact Registry
   * Reader](https://cloud.google.com/artifact-registry/docs/access-
   * control#roles) permission to pull the specified image. If you as the
   * administrator want to be able to `ssh` into the underlying VM, you need to
   * set this value to a service account for which you have the
   * `iam.serviceAccounts.actAs` permission. Conversely, if you don't want
   * anyone to be able to `ssh` into the underlying VM, use a service account
   * where no one has that permission. If not set, VMs run with a service
   * account provided by the Cloud Workstations service, and the image must be
   * publicly accessible.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Optional. Scopes to grant to the service_account. When specified, users of
   * workstations under this configuration must have `iam.serviceAccounts.actAs`
   * on the service account.
   *
   * @var string[]
   */
  public $serviceAccountScopes;
  protected $shieldedInstanceConfigType = GceShieldedInstanceConfig::class;
  protected $shieldedInstanceConfigDataType = '';
  /**
   * Optional. Link to the startup script stored in Cloud Storage. This script
   * will be run on the host workstation VM when the VM is created. The URI must
   * be of the form gs://{bucket-name}/{object-name}. If specifying a startup
   * script, the service account must have [Permission to access the bucket and
   * script file in Cloud Storage](https://cloud.google.com/storage/docs/access-
   * control/iam-permissions). Otherwise, the script must be publicly
   * accessible. Note that the service regularly updates the OS version used,
   * and it is the responsibility of the user to ensure the script stays
   * compatible with the OS version.
   *
   * @var string
   */
  public $startupScriptUri;
  /**
   * Optional. Network tags to add to the Compute Engine VMs backing the
   * workstations. This option applies [network
   * tags](https://cloud.google.com/vpc/docs/add-remove-network-tags) to VMs
   * created with this configuration. These network tags enable the creation of
   * [firewall rules](https://cloud.google.com/workstations/docs/configure-
   * firewall-rules).
   *
   * @var string[]
   */
  public $tags;
  /**
   * Optional. Resource manager tags to be bound to this instance. Tag keys and
   * values have the same definition as [resource manager
   * tags](https://cloud.google.com/resource-manager/docs/tags/tags-overview).
   * Keys must be in the format `tagKeys/{tag_key_id}`, and values are in the
   * format `tagValues/456`.
   *
   * @var string[]
   */
  public $vmTags;

  /**
   * Optional. A list of the type and count of accelerator cards attached to the
   * instance.
   *
   * @param Accelerator[] $accelerators
   */
  public function setAccelerators($accelerators)
  {
    $this->accelerators = $accelerators;
  }
  /**
   * @return Accelerator[]
   */
  public function getAccelerators()
  {
    return $this->accelerators;
  }
  /**
   * Optional. A list of the boost configurations that workstations created
   * using this workstation configuration are allowed to use. If specified,
   * users will have the option to choose from the list of boost configs when
   * starting a workstation.
   *
   * @param BoostConfig[] $boostConfigs
   */
  public function setBoostConfigs($boostConfigs)
  {
    $this->boostConfigs = $boostConfigs;
  }
  /**
   * @return BoostConfig[]
   */
  public function getBoostConfigs()
  {
    return $this->boostConfigs;
  }
  /**
   * Optional. The size of the boot disk for the VM in gigabytes (GB). The
   * minimum boot disk size is `30` GB. Defaults to `50` GB.
   *
   * @param int $bootDiskSizeGb
   */
  public function setBootDiskSizeGb($bootDiskSizeGb)
  {
    $this->bootDiskSizeGb = $bootDiskSizeGb;
  }
  /**
   * @return int
   */
  public function getBootDiskSizeGb()
  {
    return $this->bootDiskSizeGb;
  }
  /**
   * Optional. A set of Compute Engine Confidential VM instance options.
   *
   * @param GceConfidentialInstanceConfig $confidentialInstanceConfig
   */
  public function setConfidentialInstanceConfig(GceConfidentialInstanceConfig $confidentialInstanceConfig)
  {
    $this->confidentialInstanceConfig = $confidentialInstanceConfig;
  }
  /**
   * @return GceConfidentialInstanceConfig
   */
  public function getConfidentialInstanceConfig()
  {
    return $this->confidentialInstanceConfig;
  }
  /**
   * Optional. When set to true, disables public IP addresses for VMs. If you
   * disable public IP addresses, you must set up Private Google Access or Cloud
   * NAT on your network. If you use Private Google Access and you use
   * `private.googleapis.com` or `restricted.googleapis.com` for Container
   * Registry and Artifact Registry, make sure that you set up DNS records for
   * domains `*.gcr.io` and `*.pkg.dev`. Defaults to false (VMs have public IP
   * addresses).
   *
   * @param bool $disablePublicIpAddresses
   */
  public function setDisablePublicIpAddresses($disablePublicIpAddresses)
  {
    $this->disablePublicIpAddresses = $disablePublicIpAddresses;
  }
  /**
   * @return bool
   */
  public function getDisablePublicIpAddresses()
  {
    return $this->disablePublicIpAddresses;
  }
  /**
   * Optional. Whether to disable SSH access to the VM.
   *
   * @param bool $disableSsh
   */
  public function setDisableSsh($disableSsh)
  {
    $this->disableSsh = $disableSsh;
  }
  /**
   * @return bool
   */
  public function getDisableSsh()
  {
    return $this->disableSsh;
  }
  /**
   * Optional. Whether to enable nested virtualization on Cloud Workstations VMs
   * created using this workstation configuration. Defaults to false. Nested
   * virtualization lets you run virtual machine (VM) instances inside your
   * workstation. Before enabling nested virtualization, consider the following
   * important considerations. Cloud Workstations instances are subject to the
   * [same restrictions as Compute Engine
   * instances](https://cloud.google.com/compute/docs/instances/nested-
   * virtualization/overview#restrictions): * **Organization policy**: projects,
   * folders, or organizations may be restricted from creating nested VMs if the
   * **Disable VM nested virtualization** constraint is enforced in the
   * organization policy. For more information, see the Compute Engine section,
   * [Checking whether nested virtualization is
   * allowed](https://cloud.google.com/compute/docs/instances/nested-
   * virtualization/managing-
   * constraint#checking_whether_nested_virtualization_is_allowed). *
   * **Performance**: nested VMs might experience a 10% or greater decrease in
   * performance for workloads that are CPU-bound and possibly greater than a
   * 10% decrease for workloads that are input/output bound. * **Machine Type**:
   * nested virtualization can only be enabled on workstation configurations
   * that specify a machine_type in the N1 or N2 machine series.
   *
   * @param bool $enableNestedVirtualization
   */
  public function setEnableNestedVirtualization($enableNestedVirtualization)
  {
    $this->enableNestedVirtualization = $enableNestedVirtualization;
  }
  /**
   * @return bool
   */
  public function getEnableNestedVirtualization()
  {
    return $this->enableNestedVirtualization;
  }
  /**
   * Optional. The type of machine to use for VM instances—for example,
   * `"e2-standard-4"`. For more information about machine types that Cloud
   * Workstations supports, see the list of [available machine
   * types](https://cloud.google.com/workstations/docs/available-machine-types).
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
   * Optional. The number of VMs that the system should keep idle so that new
   * workstations can be started quickly for new users. Defaults to `0` in the
   * API.
   *
   * @param int $poolSize
   */
  public function setPoolSize($poolSize)
  {
    $this->poolSize = $poolSize;
  }
  /**
   * @return int
   */
  public function getPoolSize()
  {
    return $this->poolSize;
  }
  /**
   * Output only. Number of instances currently available in the pool for faster
   * workstation startup.
   *
   * @param int $pooledInstances
   */
  public function setPooledInstances($pooledInstances)
  {
    $this->pooledInstances = $pooledInstances;
  }
  /**
   * @return int
   */
  public function getPooledInstances()
  {
    return $this->pooledInstances;
  }
  /**
   * Optional. The email address of the service account for Cloud Workstations
   * VMs created with this configuration. When specified, be sure that the
   * service account has `logging.logEntries.create` and
   * `monitoring.timeSeries.create` permissions on the project so it can write
   * logs out to Cloud Logging. If using a custom container image, the service
   * account must have [Artifact Registry
   * Reader](https://cloud.google.com/artifact-registry/docs/access-
   * control#roles) permission to pull the specified image. If you as the
   * administrator want to be able to `ssh` into the underlying VM, you need to
   * set this value to a service account for which you have the
   * `iam.serviceAccounts.actAs` permission. Conversely, if you don't want
   * anyone to be able to `ssh` into the underlying VM, use a service account
   * where no one has that permission. If not set, VMs run with a service
   * account provided by the Cloud Workstations service, and the image must be
   * publicly accessible.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Optional. Scopes to grant to the service_account. When specified, users of
   * workstations under this configuration must have `iam.serviceAccounts.actAs`
   * on the service account.
   *
   * @param string[] $serviceAccountScopes
   */
  public function setServiceAccountScopes($serviceAccountScopes)
  {
    $this->serviceAccountScopes = $serviceAccountScopes;
  }
  /**
   * @return string[]
   */
  public function getServiceAccountScopes()
  {
    return $this->serviceAccountScopes;
  }
  /**
   * Optional. A set of Compute Engine Shielded instance options.
   *
   * @param GceShieldedInstanceConfig $shieldedInstanceConfig
   */
  public function setShieldedInstanceConfig(GceShieldedInstanceConfig $shieldedInstanceConfig)
  {
    $this->shieldedInstanceConfig = $shieldedInstanceConfig;
  }
  /**
   * @return GceShieldedInstanceConfig
   */
  public function getShieldedInstanceConfig()
  {
    return $this->shieldedInstanceConfig;
  }
  /**
   * Optional. Link to the startup script stored in Cloud Storage. This script
   * will be run on the host workstation VM when the VM is created. The URI must
   * be of the form gs://{bucket-name}/{object-name}. If specifying a startup
   * script, the service account must have [Permission to access the bucket and
   * script file in Cloud Storage](https://cloud.google.com/storage/docs/access-
   * control/iam-permissions). Otherwise, the script must be publicly
   * accessible. Note that the service regularly updates the OS version used,
   * and it is the responsibility of the user to ensure the script stays
   * compatible with the OS version.
   *
   * @param string $startupScriptUri
   */
  public function setStartupScriptUri($startupScriptUri)
  {
    $this->startupScriptUri = $startupScriptUri;
  }
  /**
   * @return string
   */
  public function getStartupScriptUri()
  {
    return $this->startupScriptUri;
  }
  /**
   * Optional. Network tags to add to the Compute Engine VMs backing the
   * workstations. This option applies [network
   * tags](https://cloud.google.com/vpc/docs/add-remove-network-tags) to VMs
   * created with this configuration. These network tags enable the creation of
   * [firewall rules](https://cloud.google.com/workstations/docs/configure-
   * firewall-rules).
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
   * Optional. Resource manager tags to be bound to this instance. Tag keys and
   * values have the same definition as [resource manager
   * tags](https://cloud.google.com/resource-manager/docs/tags/tags-overview).
   * Keys must be in the format `tagKeys/{tag_key_id}`, and values are in the
   * format `tagValues/456`.
   *
   * @param string[] $vmTags
   */
  public function setVmTags($vmTags)
  {
    $this->vmTags = $vmTags;
  }
  /**
   * @return string[]
   */
  public function getVmTags()
  {
    return $this->vmTags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GceInstance::class, 'Google_Service_CloudWorkstations_GceInstance');
