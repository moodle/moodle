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

class ConsumerGroup extends \Google\Model
{
  /**
   * Identifier. The name of the consumer group. The `consumer_group` segment is
   * used when connecting directly to the cluster. Structured like: projects/{pr
   * oject}/locations/{location}/clusters/{cluster}/consumerGroups/{consumer_gro
   * up}
   *
   * @var string
   */
  public $name;
  protected $topicsType = ConsumerTopicMetadata::class;
  protected $topicsDataType = 'map';

  /**
   * Identifier. The name of the consumer group. The `consumer_group` segment is
   * used when connecting directly to the cluster. Structured like: projects/{pr
   * oject}/locations/{location}/clusters/{cluster}/consumerGroups/{consumer_gro
   * up}
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
   * Optional. Metadata for this consumer group for all topics it has metadata
   * for. The key of the map is a topic name, structured like:
   * projects/{project}/locations/{location}/clusters/{cluster}/topics/{topic}
   *
   * @param ConsumerTopicMetadata[] $topics
   */
  public function setTopics($topics)
  {
    $this->topics = $topics;
  }
  /**
   * @return ConsumerTopicMetadata[]
   */
  public function getTopics()
  {
    return $this->topics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConsumerGroup::class, 'Google_Service_ManagedKafka_ConsumerGroup');
