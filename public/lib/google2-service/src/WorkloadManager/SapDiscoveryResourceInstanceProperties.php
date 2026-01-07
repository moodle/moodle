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

namespace Google\Service\WorkloadManager;

class SapDiscoveryResourceInstanceProperties extends \Google\Collection
{
  /**
   * Unspecified instance role.
   */
  public const INSTANCE_ROLE_INSTANCE_ROLE_UNSPECIFIED = 'INSTANCE_ROLE_UNSPECIFIED';
  /**
   * Application central services.
   */
  public const INSTANCE_ROLE_INSTANCE_ROLE_ASCS = 'INSTANCE_ROLE_ASCS';
  /**
   * Enqueue replication server.
   */
  public const INSTANCE_ROLE_INSTANCE_ROLE_ERS = 'INSTANCE_ROLE_ERS';
  /**
   * Application server.
   */
  public const INSTANCE_ROLE_INSTANCE_ROLE_APP_SERVER = 'INSTANCE_ROLE_APP_SERVER';
  /**
   * Database node.
   */
  public const INSTANCE_ROLE_INSTANCE_ROLE_DATABASE = 'INSTANCE_ROLE_DATABASE';
  /**
   * Combinations of roles. Application central services and enqueue replication
   * server.
   */
  public const INSTANCE_ROLE_INSTANCE_ROLE_ASCS_ERS = 'INSTANCE_ROLE_ASCS_ERS';
  /**
   * Application central services and application server.
   */
  public const INSTANCE_ROLE_INSTANCE_ROLE_ASCS_APP_SERVER = 'INSTANCE_ROLE_ASCS_APP_SERVER';
  /**
   * Application central services and database.
   */
  public const INSTANCE_ROLE_INSTANCE_ROLE_ASCS_DATABASE = 'INSTANCE_ROLE_ASCS_DATABASE';
  /**
   * Enqueue replication server and application server.
   */
  public const INSTANCE_ROLE_INSTANCE_ROLE_ERS_APP_SERVER = 'INSTANCE_ROLE_ERS_APP_SERVER';
  /**
   * Enqueue replication server and database.
   */
  public const INSTANCE_ROLE_INSTANCE_ROLE_ERS_DATABASE = 'INSTANCE_ROLE_ERS_DATABASE';
  /**
   * Application server and database.
   */
  public const INSTANCE_ROLE_INSTANCE_ROLE_APP_SERVER_DATABASE = 'INSTANCE_ROLE_APP_SERVER_DATABASE';
  /**
   * Application central services, enqueue replication server and application
   * server.
   */
  public const INSTANCE_ROLE_INSTANCE_ROLE_ASCS_ERS_APP_SERVER = 'INSTANCE_ROLE_ASCS_ERS_APP_SERVER';
  /**
   * Application central services, enqueue replication server and database.
   */
  public const INSTANCE_ROLE_INSTANCE_ROLE_ASCS_ERS_DATABASE = 'INSTANCE_ROLE_ASCS_ERS_DATABASE';
  /**
   * Application central services, application server and database.
   */
  public const INSTANCE_ROLE_INSTANCE_ROLE_ASCS_APP_SERVER_DATABASE = 'INSTANCE_ROLE_ASCS_APP_SERVER_DATABASE';
  /**
   * Enqueue replication server, application server and database.
   */
  public const INSTANCE_ROLE_INSTANCE_ROLE_ERS_APP_SERVER_DATABASE = 'INSTANCE_ROLE_ERS_APP_SERVER_DATABASE';
  /**
   * Application central services, enqueue replication server, application
   * server and database.
   */
  public const INSTANCE_ROLE_INSTANCE_ROLE_ASCS_ERS_APP_SERVER_DATABASE = 'INSTANCE_ROLE_ASCS_ERS_APP_SERVER_DATABASE';
  protected $collection_key = 'diskMounts';
  protected $appInstancesType = SapDiscoveryResourceInstancePropertiesAppInstance::class;
  protected $appInstancesDataType = 'array';
  /**
   * Optional. A list of instance URIs that are part of a cluster with this one.
   *
   * @var string[]
   */
  public $clusterInstances;
  protected $diskMountsType = SapDiscoveryResourceInstancePropertiesDiskMount::class;
  protected $diskMountsDataType = 'array';
  /**
   * Optional. The VM's instance number.
   *
   * @deprecated
   * @var string
   */
  public $instanceNumber;
  /**
   * Optional. Bitmask of instance role, a resource may have multiple roles at
   * once.
   *
   * @var string
   */
  public $instanceRole;
  /**
   * Optional. Instance is part of a DR site.
   *
   * @var bool
   */
  public $isDrSite;
  protected $osKernelVersionType = SapDiscoveryResourceInstancePropertiesKernelVersion::class;
  protected $osKernelVersionDataType = '';
  /**
   * Optional. A virtual hostname of the instance if it has one.
   *
   * @var string
   */
  public $virtualHostname;

  /**
   * Optional. App server instances on the host
   *
   * @param SapDiscoveryResourceInstancePropertiesAppInstance[] $appInstances
   */
  public function setAppInstances($appInstances)
  {
    $this->appInstances = $appInstances;
  }
  /**
   * @return SapDiscoveryResourceInstancePropertiesAppInstance[]
   */
  public function getAppInstances()
  {
    return $this->appInstances;
  }
  /**
   * Optional. A list of instance URIs that are part of a cluster with this one.
   *
   * @param string[] $clusterInstances
   */
  public function setClusterInstances($clusterInstances)
  {
    $this->clusterInstances = $clusterInstances;
  }
  /**
   * @return string[]
   */
  public function getClusterInstances()
  {
    return $this->clusterInstances;
  }
  /**
   * Optional. Disk mounts on the instance.
   *
   * @param SapDiscoveryResourceInstancePropertiesDiskMount[] $diskMounts
   */
  public function setDiskMounts($diskMounts)
  {
    $this->diskMounts = $diskMounts;
  }
  /**
   * @return SapDiscoveryResourceInstancePropertiesDiskMount[]
   */
  public function getDiskMounts()
  {
    return $this->diskMounts;
  }
  /**
   * Optional. The VM's instance number.
   *
   * @deprecated
   * @param string $instanceNumber
   */
  public function setInstanceNumber($instanceNumber)
  {
    $this->instanceNumber = $instanceNumber;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getInstanceNumber()
  {
    return $this->instanceNumber;
  }
  /**
   * Optional. Bitmask of instance role, a resource may have multiple roles at
   * once.
   *
   * Accepted values: INSTANCE_ROLE_UNSPECIFIED, INSTANCE_ROLE_ASCS,
   * INSTANCE_ROLE_ERS, INSTANCE_ROLE_APP_SERVER, INSTANCE_ROLE_DATABASE,
   * INSTANCE_ROLE_ASCS_ERS, INSTANCE_ROLE_ASCS_APP_SERVER,
   * INSTANCE_ROLE_ASCS_DATABASE, INSTANCE_ROLE_ERS_APP_SERVER,
   * INSTANCE_ROLE_ERS_DATABASE, INSTANCE_ROLE_APP_SERVER_DATABASE,
   * INSTANCE_ROLE_ASCS_ERS_APP_SERVER, INSTANCE_ROLE_ASCS_ERS_DATABASE,
   * INSTANCE_ROLE_ASCS_APP_SERVER_DATABASE,
   * INSTANCE_ROLE_ERS_APP_SERVER_DATABASE,
   * INSTANCE_ROLE_ASCS_ERS_APP_SERVER_DATABASE
   *
   * @param self::INSTANCE_ROLE_* $instanceRole
   */
  public function setInstanceRole($instanceRole)
  {
    $this->instanceRole = $instanceRole;
  }
  /**
   * @return self::INSTANCE_ROLE_*
   */
  public function getInstanceRole()
  {
    return $this->instanceRole;
  }
  /**
   * Optional. Instance is part of a DR site.
   *
   * @param bool $isDrSite
   */
  public function setIsDrSite($isDrSite)
  {
    $this->isDrSite = $isDrSite;
  }
  /**
   * @return bool
   */
  public function getIsDrSite()
  {
    return $this->isDrSite;
  }
  /**
   * Optional. The kernel version of the instance.
   *
   * @param SapDiscoveryResourceInstancePropertiesKernelVersion $osKernelVersion
   */
  public function setOsKernelVersion(SapDiscoveryResourceInstancePropertiesKernelVersion $osKernelVersion)
  {
    $this->osKernelVersion = $osKernelVersion;
  }
  /**
   * @return SapDiscoveryResourceInstancePropertiesKernelVersion
   */
  public function getOsKernelVersion()
  {
    return $this->osKernelVersion;
  }
  /**
   * Optional. A virtual hostname of the instance if it has one.
   *
   * @param string $virtualHostname
   */
  public function setVirtualHostname($virtualHostname)
  {
    $this->virtualHostname = $virtualHostname;
  }
  /**
   * @return string
   */
  public function getVirtualHostname()
  {
    return $this->virtualHostname;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SapDiscoveryResourceInstanceProperties::class, 'Google_Service_WorkloadManager_SapDiscoveryResourceInstanceProperties');
