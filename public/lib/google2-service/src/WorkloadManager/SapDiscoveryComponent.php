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

class SapDiscoveryComponent extends \Google\Collection
{
  /**
   * Unspecified topology.
   */
  public const TOPOLOGY_TYPE_TOPOLOGY_TYPE_UNSPECIFIED = 'TOPOLOGY_TYPE_UNSPECIFIED';
  /**
   * A scale-up single node system.
   */
  public const TOPOLOGY_TYPE_TOPOLOGY_SCALE_UP = 'TOPOLOGY_SCALE_UP';
  /**
   * A scale-out multi-node system.
   */
  public const TOPOLOGY_TYPE_TOPOLOGY_SCALE_OUT = 'TOPOLOGY_SCALE_OUT';
  protected $collection_key = 'resources';
  protected $applicationPropertiesType = SapDiscoveryComponentApplicationProperties::class;
  protected $applicationPropertiesDataType = '';
  protected $databasePropertiesType = SapDiscoveryComponentDatabaseProperties::class;
  protected $databasePropertiesDataType = '';
  /**
   * Optional. A list of host URIs that are part of the HA configuration if
   * present. An empty list indicates the component is not configured for HA.
   *
   * @var string[]
   */
  public $haHosts;
  /**
   * Required. Pantheon Project in which the resources reside.
   *
   * @var string
   */
  public $hostProject;
  /**
   * Optional. The region this component's resources are primarily located in.
   *
   * @var string
   */
  public $region;
  protected $replicationSitesType = SapDiscoveryComponentReplicationSite::class;
  protected $replicationSitesDataType = 'array';
  protected $resourcesType = SapDiscoveryResource::class;
  protected $resourcesDataType = 'array';
  /**
   * Optional. The SAP identifier, used by the SAP software and helps
   * differentiate systems for customers.
   *
   * @var string
   */
  public $sid;
  /**
   * Optional. The detected topology of the component.
   *
   * @var string
   */
  public $topologyType;

  /**
   * Optional. The component is a SAP application.
   *
   * @param SapDiscoveryComponentApplicationProperties $applicationProperties
   */
  public function setApplicationProperties(SapDiscoveryComponentApplicationProperties $applicationProperties)
  {
    $this->applicationProperties = $applicationProperties;
  }
  /**
   * @return SapDiscoveryComponentApplicationProperties
   */
  public function getApplicationProperties()
  {
    return $this->applicationProperties;
  }
  /**
   * Optional. The component is a SAP database.
   *
   * @param SapDiscoveryComponentDatabaseProperties $databaseProperties
   */
  public function setDatabaseProperties(SapDiscoveryComponentDatabaseProperties $databaseProperties)
  {
    $this->databaseProperties = $databaseProperties;
  }
  /**
   * @return SapDiscoveryComponentDatabaseProperties
   */
  public function getDatabaseProperties()
  {
    return $this->databaseProperties;
  }
  /**
   * Optional. A list of host URIs that are part of the HA configuration if
   * present. An empty list indicates the component is not configured for HA.
   *
   * @param string[] $haHosts
   */
  public function setHaHosts($haHosts)
  {
    $this->haHosts = $haHosts;
  }
  /**
   * @return string[]
   */
  public function getHaHosts()
  {
    return $this->haHosts;
  }
  /**
   * Required. Pantheon Project in which the resources reside.
   *
   * @param string $hostProject
   */
  public function setHostProject($hostProject)
  {
    $this->hostProject = $hostProject;
  }
  /**
   * @return string
   */
  public function getHostProject()
  {
    return $this->hostProject;
  }
  /**
   * Optional. The region this component's resources are primarily located in.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Optional. A list of replication sites used in Disaster Recovery (DR)
   * configurations.
   *
   * @param SapDiscoveryComponentReplicationSite[] $replicationSites
   */
  public function setReplicationSites($replicationSites)
  {
    $this->replicationSites = $replicationSites;
  }
  /**
   * @return SapDiscoveryComponentReplicationSite[]
   */
  public function getReplicationSites()
  {
    return $this->replicationSites;
  }
  /**
   * Optional. The resources in a component.
   *
   * @param SapDiscoveryResource[] $resources
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return SapDiscoveryResource[]
   */
  public function getResources()
  {
    return $this->resources;
  }
  /**
   * Optional. The SAP identifier, used by the SAP software and helps
   * differentiate systems for customers.
   *
   * @param string $sid
   */
  public function setSid($sid)
  {
    $this->sid = $sid;
  }
  /**
   * @return string
   */
  public function getSid()
  {
    return $this->sid;
  }
  /**
   * Optional. The detected topology of the component.
   *
   * Accepted values: TOPOLOGY_TYPE_UNSPECIFIED, TOPOLOGY_SCALE_UP,
   * TOPOLOGY_SCALE_OUT
   *
   * @param self::TOPOLOGY_TYPE_* $topologyType
   */
  public function setTopologyType($topologyType)
  {
    $this->topologyType = $topologyType;
  }
  /**
   * @return self::TOPOLOGY_TYPE_*
   */
  public function getTopologyType()
  {
    return $this->topologyType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SapDiscoveryComponent::class, 'Google_Service_WorkloadManager_SapDiscoveryComponent');
