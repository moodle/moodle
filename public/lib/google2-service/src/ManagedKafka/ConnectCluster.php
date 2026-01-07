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

namespace Google\Service\ManagedKafka;

class ConnectCluster extends \Google\Model
{
  /**
   * A state was not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The cluster is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The cluster is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The cluster is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The cluster is detached.
   */
  public const STATE_DETACHED = 'DETACHED';
  protected $capacityConfigType = CapacityConfig::class;
  protected $capacityConfigDataType = '';
  /**
   * Optional. Configurations for the worker that are overridden from the
   * defaults. The key of the map is a Kafka Connect worker property name, for
   * example: `exactly.once.source.support`.
   *
   * @var string[]
   */
  public $config;
  /**
   * Output only. The time when the cluster was created.
   *
   * @var string
   */
  public $createTime;
  protected $gcpConfigType = ConnectGcpConfig::class;
  protected $gcpConfigDataType = '';
  /**
   * Required. Immutable. The name of the Kafka cluster this Kafka Connect
   * cluster is attached to. Structured like:
   * projects/{project}/locations/{location}/clusters/{cluster}
   *
   * @var string
   */
  public $kafkaCluster;
  /**
   * Optional. Labels as key value pairs.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The name of the Kafka Connect cluster. Structured like: project
   * s/{project_number}/locations/{location}/connectClusters/{connect_cluster_id
   * }
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. The current state of the Kafka Connect cluster.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time when the cluster was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. Capacity configuration for the Kafka Connect cluster.
   *
   * @param CapacityConfig $capacityConfig
   */
  public function setCapacityConfig(CapacityConfig $capacityConfig)
  {
    $this->capacityConfig = $capacityConfig;
  }
  /**
   * @return CapacityConfig
   */
  public function getCapacityConfig()
  {
    return $this->capacityConfig;
  }
  /**
   * Optional. Configurations for the worker that are overridden from the
   * defaults. The key of the map is a Kafka Connect worker property name, for
   * example: `exactly.once.source.support`.
   *
   * @param string[] $config
   */
  public function setConfig($config)
  {
    $this->config = $config;
  }
  /**
   * @return string[]
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Output only. The time when the cluster was created.
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
   * Required. Configuration properties for a Kafka Connect cluster deployed to
   * Google Cloud Platform.
   *
   * @param ConnectGcpConfig $gcpConfig
   */
  public function setGcpConfig(ConnectGcpConfig $gcpConfig)
  {
    $this->gcpConfig = $gcpConfig;
  }
  /**
   * @return ConnectGcpConfig
   */
  public function getGcpConfig()
  {
    return $this->gcpConfig;
  }
  /**
   * Required. Immutable. The name of the Kafka cluster this Kafka Connect
   * cluster is attached to. Structured like:
   * projects/{project}/locations/{location}/clusters/{cluster}
   *
   * @param string $kafkaCluster
   */
  public function setKafkaCluster($kafkaCluster)
  {
    $this->kafkaCluster = $kafkaCluster;
  }
  /**
   * @return string
   */
  public function getKafkaCluster()
  {
    return $this->kafkaCluster;
  }
  /**
   * Optional. Labels as key value pairs.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. The name of the Kafka Connect cluster. Structured like: project
   * s/{project_number}/locations/{location}/connectClusters/{connect_cluster_id
   * }
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
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. The current state of the Kafka Connect cluster.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, DETACHED
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
   * Output only. The time when the cluster was last updated.
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
class_alias(ConnectCluster::class, 'Google_Service_ManagedKafka_ConnectCluster');
