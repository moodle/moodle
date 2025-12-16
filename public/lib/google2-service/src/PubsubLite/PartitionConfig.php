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

namespace Google\Service\PubsubLite;

class PartitionConfig extends \Google\Model
{
  protected $capacityType = Capacity::class;
  protected $capacityDataType = '';
  /**
   * The number of partitions in the topic. Must be at least 1. Once a topic has
   * been created the number of partitions can be increased but not decreased.
   * Message ordering is not guaranteed across a topic resize. For more
   * information see
   * https://cloud.google.com/pubsub/lite/docs/topics#scaling_capacity
   *
   * @var string
   */
  public $count;
  /**
   * DEPRECATED: Use capacity instead which can express a superset of
   * configurations. Every partition in the topic is allocated throughput
   * equivalent to `scale` times the standard partition throughput (4 MiB/s).
   * This is also reflected in the cost of this topic; a topic with `scale` of 2
   * and count of 10 is charged for 20 partitions. This value must be in the
   * range [1,4].
   *
   * @deprecated
   * @var int
   */
  public $scale;

  /**
   * The capacity configuration.
   *
   * @param Capacity $capacity
   */
  public function setCapacity(Capacity $capacity)
  {
    $this->capacity = $capacity;
  }
  /**
   * @return Capacity
   */
  public function getCapacity()
  {
    return $this->capacity;
  }
  /**
   * The number of partitions in the topic. Must be at least 1. Once a topic has
   * been created the number of partitions can be increased but not decreased.
   * Message ordering is not guaranteed across a topic resize. For more
   * information see
   * https://cloud.google.com/pubsub/lite/docs/topics#scaling_capacity
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * DEPRECATED: Use capacity instead which can express a superset of
   * configurations. Every partition in the topic is allocated throughput
   * equivalent to `scale` times the standard partition throughput (4 MiB/s).
   * This is also reflected in the cost of this topic; a topic with `scale` of 2
   * and count of 10 is charged for 20 partitions. This value must be in the
   * range [1,4].
   *
   * @deprecated
   * @param int $scale
   */
  public function setScale($scale)
  {
    $this->scale = $scale;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getScale()
  {
    return $this->scale;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartitionConfig::class, 'Google_Service_PubsubLite_PartitionConfig');
