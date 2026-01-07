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

class Topic extends \Google\Model
{
  /**
   * Optional. Configurations for the topic that are overridden from the cluster
   * defaults. The key of the map is a Kafka topic property name, for example:
   * `cleanup.policy`, `compression.type`.
   *
   * @var string[]
   */
  public $configs;
  /**
   * Identifier. The name of the topic. The `topic` segment is used when
   * connecting directly to the cluster. Structured like:
   * projects/{project}/locations/{location}/clusters/{cluster}/topics/{topic}
   *
   * @var string
   */
  public $name;
  /**
   * Required. The number of partitions this topic has. The partition count can
   * only be increased, not decreased. Please note that if partitions are
   * increased for a topic that has a key, the partitioning logic or the
   * ordering of the messages will be affected.
   *
   * @var int
   */
  public $partitionCount;
  /**
   * Required. Immutable. The number of replicas of each partition. A
   * replication factor of 3 is recommended for high availability.
   *
   * @var int
   */
  public $replicationFactor;

  /**
   * Optional. Configurations for the topic that are overridden from the cluster
   * defaults. The key of the map is a Kafka topic property name, for example:
   * `cleanup.policy`, `compression.type`.
   *
   * @param string[] $configs
   */
  public function setConfigs($configs)
  {
    $this->configs = $configs;
  }
  /**
   * @return string[]
   */
  public function getConfigs()
  {
    return $this->configs;
  }
  /**
   * Identifier. The name of the topic. The `topic` segment is used when
   * connecting directly to the cluster. Structured like:
   * projects/{project}/locations/{location}/clusters/{cluster}/topics/{topic}
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
   * Required. The number of partitions this topic has. The partition count can
   * only be increased, not decreased. Please note that if partitions are
   * increased for a topic that has a key, the partitioning logic or the
   * ordering of the messages will be affected.
   *
   * @param int $partitionCount
   */
  public function setPartitionCount($partitionCount)
  {
    $this->partitionCount = $partitionCount;
  }
  /**
   * @return int
   */
  public function getPartitionCount()
  {
    return $this->partitionCount;
  }
  /**
   * Required. Immutable. The number of replicas of each partition. A
   * replication factor of 3 is recommended for high availability.
   *
   * @param int $replicationFactor
   */
  public function setReplicationFactor($replicationFactor)
  {
    $this->replicationFactor = $replicationFactor;
  }
  /**
   * @return int
   */
  public function getReplicationFactor()
  {
    return $this->replicationFactor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Topic::class, 'Google_Service_ManagedKafka_Topic');
