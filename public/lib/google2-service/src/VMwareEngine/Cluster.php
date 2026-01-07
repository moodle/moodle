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

class Cluster extends \Google\Collection
{
  /**
   * The default value. This value should never be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Cluster is operational and can be used by the user.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The Cluster is being deployed.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Adding or removing of a node to the cluster, any other cluster specific
   * updates.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The Cluster is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The Cluster is undergoing maintenance, for example: a failed node is
   * getting replaced.
   */
  public const STATE_REPAIRING = 'REPAIRING';
  protected $collection_key = 'datastoreMountConfig';
  protected $autoscalingSettingsType = AutoscalingSettings::class;
  protected $autoscalingSettingsDataType = '';
  /**
   * Output only. Creation time of this resource.
   *
   * @var string
   */
  public $createTime;
  protected $datastoreMountConfigType = DatastoreMountConfig::class;
  protected $datastoreMountConfigDataType = 'array';
  /**
   * Output only. True if the cluster is a management cluster; false otherwise.
   * There can only be one management cluster in a private cloud and it has to
   * be the first one.
   *
   * @var bool
   */
  public $management;
  /**
   * Output only. Identifier. The resource name of this cluster. Resource names
   * are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/clusters/my-cluster`
   *
   * @var string
   */
  public $name;
  protected $nodeTypeConfigsType = NodeTypeConfig::class;
  protected $nodeTypeConfigsDataType = 'map';
  /**
   * Output only. State of the resource.
   *
   * @var string
   */
  public $state;
  protected $stretchedClusterConfigType = StretchedClusterConfig::class;
  protected $stretchedClusterConfigDataType = '';
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Last update time of this resource.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Configuration of the autoscaling applied to this cluster.
   *
   * @param AutoscalingSettings $autoscalingSettings
   */
  public function setAutoscalingSettings(AutoscalingSettings $autoscalingSettings)
  {
    $this->autoscalingSettings = $autoscalingSettings;
  }
  /**
   * @return AutoscalingSettings
   */
  public function getAutoscalingSettings()
  {
    return $this->autoscalingSettings;
  }
  /**
   * Output only. Creation time of this resource.
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
   * Output only. Configuration of a mounted datastore.
   *
   * @param DatastoreMountConfig[] $datastoreMountConfig
   */
  public function setDatastoreMountConfig($datastoreMountConfig)
  {
    $this->datastoreMountConfig = $datastoreMountConfig;
  }
  /**
   * @return DatastoreMountConfig[]
   */
  public function getDatastoreMountConfig()
  {
    return $this->datastoreMountConfig;
  }
  /**
   * Output only. True if the cluster is a management cluster; false otherwise.
   * There can only be one management cluster in a private cloud and it has to
   * be the first one.
   *
   * @param bool $management
   */
  public function setManagement($management)
  {
    $this->management = $management;
  }
  /**
   * @return bool
   */
  public function getManagement()
  {
    return $this->management;
  }
  /**
   * Output only. Identifier. The resource name of this cluster. Resource names
   * are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/clusters/my-cluster`
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
   * Output only. State of the resource.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING, UPDATING, DELETING,
   * REPAIRING
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
   * Optional. Configuration of a stretched cluster. Required for clusters that
   * belong to a STRETCHED private cloud.
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
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. Last update time of this resource.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Cluster::class, 'Google_Service_VMwareEngine_Cluster');
