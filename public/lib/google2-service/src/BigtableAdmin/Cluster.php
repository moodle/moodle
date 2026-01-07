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

namespace Google\Service\BigtableAdmin;

class Cluster extends \Google\Model
{
  /**
   * The user did not specify a storage type.
   */
  public const DEFAULT_STORAGE_TYPE_STORAGE_TYPE_UNSPECIFIED = 'STORAGE_TYPE_UNSPECIFIED';
  /**
   * Flash (SSD) storage should be used.
   */
  public const DEFAULT_STORAGE_TYPE_SSD = 'SSD';
  /**
   * Magnetic drive (HDD) storage should be used.
   */
  public const DEFAULT_STORAGE_TYPE_HDD = 'HDD';
  /**
   * No node scaling specified. Defaults to NODE_SCALING_FACTOR_1X.
   */
  public const NODE_SCALING_FACTOR_NODE_SCALING_FACTOR_UNSPECIFIED = 'NODE_SCALING_FACTOR_UNSPECIFIED';
  /**
   * The cluster is running with a scaling factor of 1.
   */
  public const NODE_SCALING_FACTOR_NODE_SCALING_FACTOR_1X = 'NODE_SCALING_FACTOR_1X';
  /**
   * The cluster is running with a scaling factor of 2. All node count values
   * must be in increments of 2 with this scaling factor enabled, otherwise an
   * INVALID_ARGUMENT error will be returned.
   */
  public const NODE_SCALING_FACTOR_NODE_SCALING_FACTOR_2X = 'NODE_SCALING_FACTOR_2X';
  /**
   * The state of the cluster could not be determined.
   */
  public const STATE_STATE_NOT_KNOWN = 'STATE_NOT_KNOWN';
  /**
   * The cluster has been successfully created and is ready to serve requests.
   */
  public const STATE_READY = 'READY';
  /**
   * The cluster is currently being created, and may be destroyed if the
   * creation process encounters an error. A cluster may not be able to serve
   * requests while being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The cluster is currently being resized, and may revert to its previous node
   * count if the process encounters an error. A cluster is still capable of
   * serving requests while being resized, but may exhibit performance as if its
   * number of allocated nodes is between the starting and requested states.
   */
  public const STATE_RESIZING = 'RESIZING';
  /**
   * The cluster has no backing nodes. The data (tables) still exist, but no
   * operations can be performed on the cluster.
   */
  public const STATE_DISABLED = 'DISABLED';
  protected $clusterConfigType = ClusterConfig::class;
  protected $clusterConfigDataType = '';
  /**
   * Immutable. The type of storage used by this cluster to serve its parent
   * instance's tables, unless explicitly overridden.
   *
   * @var string
   */
  public $defaultStorageType;
  protected $encryptionConfigType = EncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  /**
   * Immutable. The location where this cluster's nodes and storage reside. For
   * best performance, clients should be located as close as possible to this
   * cluster. Currently only zones are supported, so values should be of the
   * form `projects/{project}/locations/{zone}`.
   *
   * @var string
   */
  public $location;
  /**
   * The unique name of the cluster. Values are of the form
   * `projects/{project}/instances/{instance}/clusters/a-z*`.
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. The node scaling factor of this cluster.
   *
   * @var string
   */
  public $nodeScalingFactor;
  /**
   * The number of nodes in the cluster. If no value is set, Cloud Bigtable
   * automatically allocates nodes based on your data footprint and optimized
   * for 50% storage utilization.
   *
   * @var int
   */
  public $serveNodes;
  /**
   * Output only. The current state of the cluster.
   *
   * @var string
   */
  public $state;

  /**
   * Configuration for this cluster.
   *
   * @param ClusterConfig $clusterConfig
   */
  public function setClusterConfig(ClusterConfig $clusterConfig)
  {
    $this->clusterConfig = $clusterConfig;
  }
  /**
   * @return ClusterConfig
   */
  public function getClusterConfig()
  {
    return $this->clusterConfig;
  }
  /**
   * Immutable. The type of storage used by this cluster to serve its parent
   * instance's tables, unless explicitly overridden.
   *
   * Accepted values: STORAGE_TYPE_UNSPECIFIED, SSD, HDD
   *
   * @param self::DEFAULT_STORAGE_TYPE_* $defaultStorageType
   */
  public function setDefaultStorageType($defaultStorageType)
  {
    $this->defaultStorageType = $defaultStorageType;
  }
  /**
   * @return self::DEFAULT_STORAGE_TYPE_*
   */
  public function getDefaultStorageType()
  {
    return $this->defaultStorageType;
  }
  /**
   * Immutable. The encryption configuration for CMEK-protected clusters.
   *
   * @param EncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(EncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return EncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
  /**
   * Immutable. The location where this cluster's nodes and storage reside. For
   * best performance, clients should be located as close as possible to this
   * cluster. Currently only zones are supported, so values should be of the
   * form `projects/{project}/locations/{zone}`.
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
   * The unique name of the cluster. Values are of the form
   * `projects/{project}/instances/{instance}/clusters/a-z*`.
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
   * Immutable. The node scaling factor of this cluster.
   *
   * Accepted values: NODE_SCALING_FACTOR_UNSPECIFIED, NODE_SCALING_FACTOR_1X,
   * NODE_SCALING_FACTOR_2X
   *
   * @param self::NODE_SCALING_FACTOR_* $nodeScalingFactor
   */
  public function setNodeScalingFactor($nodeScalingFactor)
  {
    $this->nodeScalingFactor = $nodeScalingFactor;
  }
  /**
   * @return self::NODE_SCALING_FACTOR_*
   */
  public function getNodeScalingFactor()
  {
    return $this->nodeScalingFactor;
  }
  /**
   * The number of nodes in the cluster. If no value is set, Cloud Bigtable
   * automatically allocates nodes based on your data footprint and optimized
   * for 50% storage utilization.
   *
   * @param int $serveNodes
   */
  public function setServeNodes($serveNodes)
  {
    $this->serveNodes = $serveNodes;
  }
  /**
   * @return int
   */
  public function getServeNodes()
  {
    return $this->serveNodes;
  }
  /**
   * Output only. The current state of the cluster.
   *
   * Accepted values: STATE_NOT_KNOWN, READY, CREATING, RESIZING, DISABLED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Cluster::class, 'Google_Service_BigtableAdmin_Cluster');
