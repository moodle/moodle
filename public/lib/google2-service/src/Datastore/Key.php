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

namespace Google\Service\Datastore;

class Key extends \Google\Collection
{
  protected $collection_key = 'path';
  protected $partitionIdType = PartitionId::class;
  protected $partitionIdDataType = '';
  protected $pathType = PathElement::class;
  protected $pathDataType = 'array';

  /**
   * Entities are partitioned into subsets, currently identified by a project ID
   * and namespace ID. Queries are scoped to a single partition.
   *
   * @param PartitionId $partitionId
   */
  public function setPartitionId(PartitionId $partitionId)
  {
    $this->partitionId = $partitionId;
  }
  /**
   * @return PartitionId
   */
  public function getPartitionId()
  {
    return $this->partitionId;
  }
  /**
   * The entity path. An entity path consists of one or more elements composed
   * of a kind and a string or numerical identifier, which identify entities.
   * The first element identifies a _root entity_, the second element identifies
   * a _child_ of the root entity, the third element identifies a child of the
   * second entity, and so forth. The entities identified by all prefixes of the
   * path are called the element's _ancestors_. An entity path is always fully
   * complete: *all* of the entity's ancestors are required to be in the path
   * along with the entity identifier itself. The only exception is that in some
   * documented cases, the identifier in the last path element (for the entity)
   * itself may be omitted. For example, the last path element of the key of
   * `Mutation.insert` may have no identifier. A path can never be empty, and a
   * path can have at most 100 elements.
   *
   * @param PathElement[] $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return PathElement[]
   */
  public function getPath()
  {
    return $this->path;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Key::class, 'Google_Service_Datastore_Key');
