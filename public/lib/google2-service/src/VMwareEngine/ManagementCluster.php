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

namespace Google\Service\VMwareEngine;

class ManagementCluster extends \Google\Model
{
  /**
   * Required. The user-provided identifier of the new `Cluster`. The identifier
   * must meet the following requirements: * Only contains 1-63 alphanumeric
   * characters and hyphens * Begins with an alphabetical character * Ends with
   * a non-hyphen character * Not formatted as a UUID * Complies with [RFC
   * 1034](https://datatracker.ietf.org/doc/html/rfc1034) (section 3.5)
   *
   * @var string
   */
  public $clusterId;
  protected $nodeTypeConfigsType = NodeTypeConfig::class;
  protected $nodeTypeConfigsDataType = 'map';
  protected $stretchedClusterConfigType = StretchedClusterConfig::class;
  protected $stretchedClusterConfigDataType = '';

  /**
   * Required. The user-provided identifier of the new `Cluster`. The identifier
   * must meet the following requirements: * Only contains 1-63 alphanumeric
   * characters and hyphens * Begins with an alphabetical character * Ends with
   * a non-hyphen character * Not formatted as a UUID * Complies with [RFC
   * 1034](https://datatracker.ietf.org/doc/html/rfc1034) (section 3.5)
   *
   * @param string $clusterId
   */
  public function setClusterId($clusterId)
  {
    $this->clusterId = $clusterId;
  }
  /**
   * @return string
   */
  public function getClusterId()
  {
    return $this->clusterId;
  }
  /**
   * Required. The map of cluster node types in this cluster, where the key is
   * canonical identifier of the node type (corresponds to the `NodeType`).
   *
   * @param NodeTypeConfig[] $nodeTypeConfigs
   */
  public function setNodeTypeConfigs($nodeTypeConfigs)
  {
    $this->nodeTypeConfigs = $nodeTypeConfigs;
  }
  /**
   * @return NodeTypeConfig[]
   */
  public function getNodeTypeConfigs()
  {
    return $this->nodeTypeConfigs;
  }
  /**
   * Optional. Configuration of a stretched cluster. Required for STRETCHED
   * private clouds.
   *
   * @param StretchedClusterConfig $stretchedClusterConfig
   */
  public function setStretchedClusterConfig(StretchedClusterConfig $stretchedClusterConfig)
  {
    $this->stretchedClusterConfig = $stretchedClusterConfig;
  }
  /**
   * @return StretchedClusterConfig
   */
  public function getStretchedClusterConfig()
  {
    return $this->stretchedClusterConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagementCluster::class, 'Google_Service_VMwareEngine_ManagementCluster');
