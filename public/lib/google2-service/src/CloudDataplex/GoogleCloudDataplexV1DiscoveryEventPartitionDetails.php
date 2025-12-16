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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DiscoveryEventPartitionDetails extends \Google\Collection
{
  /**
   * An unspecified event type.
   */
  public const TYPE_ENTITY_TYPE_UNSPECIFIED = 'ENTITY_TYPE_UNSPECIFIED';
  /**
   * Entities representing structured data.
   */
  public const TYPE_TABLE = 'TABLE';
  /**
   * Entities representing unstructured data.
   */
  public const TYPE_FILESET = 'FILESET';
  protected $collection_key = 'sampledDataLocations';
  /**
   * The name to the containing entity resource. The name is the fully-qualified
   * resource name.
   *
   * @var string
   */
  public $entity;
  /**
   * The name to the partition resource. The name is the fully-qualified
   * resource name.
   *
   * @var string
   */
  public $partition;
  /**
   * The locations of the data items (e.g., a Cloud Storage objects) sampled for
   * metadata inference.
   *
   * @var string[]
   */
  public $sampledDataLocations;
  /**
   * The type of the containing entity resource.
   *
   * @var string
   */
  public $type;

  /**
   * The name to the containing entity resource. The name is the fully-qualified
   * resource name.
   *
   * @param string $entity
   */
  public function setEntity($entity)
  {
    $this->entity = $entity;
  }
  /**
   * @return string
   */
  public function getEntity()
  {
    return $this->entity;
  }
  /**
   * The name to the partition resource. The name is the fully-qualified
   * resource name.
   *
   * @param string $partition
   */
  public function setPartition($partition)
  {
    $this->partition = $partition;
  }
  /**
   * @return string
   */
  public function getPartition()
  {
    return $this->partition;
  }
  /**
   * The locations of the data items (e.g., a Cloud Storage objects) sampled for
   * metadata inference.
   *
   * @param string[] $sampledDataLocations
   */
  public function setSampledDataLocations($sampledDataLocations)
  {
    $this->sampledDataLocations = $sampledDataLocations;
  }
  /**
   * @return string[]
   */
  public function getSampledDataLocations()
  {
    return $this->sampledDataLocations;
  }
  /**
   * The type of the containing entity resource.
   *
   * Accepted values: ENTITY_TYPE_UNSPECIFIED, TABLE, FILESET
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DiscoveryEventPartitionDetails::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DiscoveryEventPartitionDetails');
