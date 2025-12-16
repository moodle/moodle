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

class GoogleDatastoreAdminV1Index extends \Google\Collection
{
  /**
   * The ancestor mode is unspecified.
   */
  public const ANCESTOR_ANCESTOR_MODE_UNSPECIFIED = 'ANCESTOR_MODE_UNSPECIFIED';
  /**
   * Do not include the entity's ancestors in the index.
   */
  public const ANCESTOR_NONE = 'NONE';
  /**
   * Include all the entity's ancestors in the index.
   */
  public const ANCESTOR_ALL_ANCESTORS = 'ALL_ANCESTORS';
  /**
   * The state is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The index is being created, and cannot be used by queries. There is an
   * active long-running operation for the index. The index is updated when
   * writing an entity. Some index data may exist.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The index is ready to be used. The index is updated when writing an entity.
   * The index is fully populated from all stored entities it applies to.
   */
  public const STATE_READY = 'READY';
  /**
   * The index is being deleted, and cannot be used by queries. There is an
   * active long-running operation for the index. The index is not updated when
   * writing an entity. Some index data may exist.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The index was being created or deleted, but something went wrong. The index
   * cannot by used by queries. There is no active long-running operation for
   * the index, and the most recently finished long-running operation failed.
   * The index is not updated when writing an entity. Some index data may exist.
   */
  public const STATE_ERROR = 'ERROR';
  protected $collection_key = 'properties';
  /**
   * Required. The index's ancestor mode. Must not be ANCESTOR_MODE_UNSPECIFIED.
   *
   * @var string
   */
  public $ancestor;
  /**
   * Output only. The resource ID of the index.
   *
   * @var string
   */
  public $indexId;
  /**
   * Required. The entity kind to which this index applies.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. Project ID.
   *
   * @var string
   */
  public $projectId;
  protected $propertiesType = GoogleDatastoreAdminV1IndexedProperty::class;
  protected $propertiesDataType = 'array';
  /**
   * Output only. The state of the index.
   *
   * @var string
   */
  public $state;

  /**
   * Required. The index's ancestor mode. Must not be ANCESTOR_MODE_UNSPECIFIED.
   *
   * Accepted values: ANCESTOR_MODE_UNSPECIFIED, NONE, ALL_ANCESTORS
   *
   * @param self::ANCESTOR_* $ancestor
   */
  public function setAncestor($ancestor)
  {
    $this->ancestor = $ancestor;
  }
  /**
   * @return self::ANCESTOR_*
   */
  public function getAncestor()
  {
    return $this->ancestor;
  }
  /**
   * Output only. The resource ID of the index.
   *
   * @param string $indexId
   */
  public function setIndexId($indexId)
  {
    $this->indexId = $indexId;
  }
  /**
   * @return string
   */
  public function getIndexId()
  {
    return $this->indexId;
  }
  /**
   * Required. The entity kind to which this index applies.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Output only. Project ID.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Required. An ordered sequence of property names and their index attributes.
   * Requires: * A maximum of 100 properties.
   *
   * @param GoogleDatastoreAdminV1IndexedProperty[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return GoogleDatastoreAdminV1IndexedProperty[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Output only. The state of the index.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, DELETING, ERROR
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
class_alias(GoogleDatastoreAdminV1Index::class, 'Google_Service_Datastore_GoogleDatastoreAdminV1Index');
