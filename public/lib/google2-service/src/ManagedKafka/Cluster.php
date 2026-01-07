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

class Cluster extends \Google\Model
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
  protected $capacityConfigType = CapacityConfig::class;
  protected $capacityConfigDataType = '';
  /**
   * Output only. The time when the cluster was created.
   *
   * @var string
   */
  public $createTime;
  protected $gcpConfigType = GcpConfig::class;
  protected $gcpConfigDataType = '';
  /**
   * Optional. Labels as key value pairs.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The name of the cluster. Structured like:
   * projects/{project_number}/locations/{location}/clusters/{cluster_id}
   *
   * @var string
   */
  public $name;
  protected $rebalanceConfigType = RebalanceConfig::class;
  protected $rebalanceConfigDataType = '';
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
   * Output only. The current state of the cluster.
   *
   * @var string
   */
  public $state;
  protected $tlsConfigType = TlsConfig::class;
  protected $tlsConfigDataType = '';
  protected $updateOptionsType = UpdateOptions::class;
  protected $updateOptionsDataType = '';
  /**
   * Output only. The time when the cluster was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. Capacity configuration for the Kafka cluster.
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
   * Required. Configuration properties for a Kafka cluster deployed to Google
   * Cloud Platform.
   *
   * @param GcpConfig $gcpConfig
   */
  public function setGcpConfig(GcpConfig $gcpConfig)
  {
    $this->gcpConfig = $gcpConfig;
  }
  /**
   * @return GcpConfig
   */
  public function getGcpConfig()
  {
    return $this->gcpConfig;
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
   * Identifier. The name of the cluster. Structured like:
   * projects/{project_number}/locations/{location}/clusters/{cluster_id}
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
   * Optional. Rebalance configuration for the Kafka cluster.
   *
   * @param RebalanceConfig $rebalanceConfig
   */
  public function setRebalanceConfig(RebalanceConfig $rebalanceConfig)
  {
    $this->rebalanceConfig = $rebalanceConfig;
  }
  /**
   * @return RebalanceConfig
   */
  public function getRebalanceConfig()
  {
    return $this->rebalanceConfig;
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
   * Output only. The current state of the cluster.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING
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
   * Optional. TLS configuration for the Kafka cluster.
   *
   * @param TlsConfig $tlsConfig
   */
  public function setTlsConfig(TlsConfig $tlsConfig)
  {
    $this->tlsConfig = $tlsConfig;
  }
  /**
   * @return TlsConfig
   */
  public function getTlsConfig()
  {
    return $this->tlsConfig;
  }
  /**
   * Optional. UpdateOptions represents options that control how updates to the
   * cluster are applied.
   *
   * @param UpdateOptions $updateOptions
   */
  public function setUpdateOptions(UpdateOptions $updateOptions)
  {
    $this->updateOptions = $updateOptions;
  }
  /**
   * @return UpdateOptions
   */
  public function getUpdateOptions()
  {
    return $this->updateOptions;
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
class_alias(Cluster::class, 'Google_Service_ManagedKafka_Cluster');
