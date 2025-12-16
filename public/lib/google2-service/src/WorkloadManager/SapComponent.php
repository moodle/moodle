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

class SapComponent extends \Google\Collection
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
  protected $databasePropertiesType = DatabaseProperties::class;
  protected $databasePropertiesDataType = '';
  /**
   * A list of host URIs that are part of the HA configuration if present. An
   * empty list indicates the component is not configured for HA.
   *
   * @var string[]
   */
  public $haHosts;
  protected $resourcesType = CloudResource::class;
  protected $resourcesDataType = 'array';
  /**
   * Output only. sid is the sap component identificator
   *
   * @var string
   */
  public $sid;
  /**
   * The detected topology of the component.
   *
   * @var string
   */
  public $topologyType;

  /**
   * Output only. All instance properties.
   *
   * @param DatabaseProperties $databaseProperties
   */
  public function setDatabaseProperties(DatabaseProperties $databaseProperties)
  {
    $this->databaseProperties = $databaseProperties;
  }
  /**
   * @return DatabaseProperties
   */
  public function getDatabaseProperties()
  {
    return $this->databaseProperties;
  }
  /**
   * A list of host URIs that are part of the HA configuration if present. An
   * empty list indicates the component is not configured for HA.
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
   * Output only. resources in the component
   *
   * @param CloudResource[] $resources
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return CloudResource[]
   */
  public function getResources()
  {
    return $this->resources;
  }
  /**
   * Output only. sid is the sap component identificator
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
   * The detected topology of the component.
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
class_alias(SapComponent::class, 'Google_Service_WorkloadManager_SapComponent');
