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

class Mutation extends \Google\Collection
{
  /**
   * Unspecified. Defaults to `SERVER_VALUE`.
   */
  public const CONFLICT_RESOLUTION_STRATEGY_STRATEGY_UNSPECIFIED = 'STRATEGY_UNSPECIFIED';
  /**
   * The server entity is kept.
   */
  public const CONFLICT_RESOLUTION_STRATEGY_SERVER_VALUE = 'SERVER_VALUE';
  /**
   * The whole commit request fails.
   */
  public const CONFLICT_RESOLUTION_STRATEGY_FAIL = 'FAIL';
  protected $collection_key = 'propertyTransforms';
  /**
   * The version of the entity that this mutation is being applied to. If this
   * does not match the current version on the server, the mutation conflicts.
   *
   * @var string
   */
  public $baseVersion;
  /**
   * The strategy to use when a conflict is detected. Defaults to
   * `SERVER_VALUE`. If this is set, then `conflict_detection_strategy` must
   * also be set.
   *
   * @var string
   */
  public $conflictResolutionStrategy;
  protected $deleteType = Key::class;
  protected $deleteDataType = '';
  protected $insertType = Entity::class;
  protected $insertDataType = '';
  protected $propertyMaskType = PropertyMask::class;
  protected $propertyMaskDataType = '';
  protected $propertyTransformsType = PropertyTransform::class;
  protected $propertyTransformsDataType = 'array';
  protected $updateType = Entity::class;
  protected $updateDataType = '';
  /**
   * The update time of the entity that this mutation is being applied to. If
   * this does not match the current update time on the server, the mutation
   * conflicts.
   *
   * @var string
   */
  public $updateTime;
  protected $upsertType = Entity::class;
  protected $upsertDataType = '';

  /**
   * The version of the entity that this mutation is being applied to. If this
   * does not match the current version on the server, the mutation conflicts.
   *
   * @param string $baseVersion
   */
  public function setBaseVersion($baseVersion)
  {
    $this->baseVersion = $baseVersion;
  }
  /**
   * @return string
   */
  public function getBaseVersion()
  {
    return $this->baseVersion;
  }
  /**
   * The strategy to use when a conflict is detected. Defaults to
   * `SERVER_VALUE`. If this is set, then `conflict_detection_strategy` must
   * also be set.
   *
   * Accepted values: STRATEGY_UNSPECIFIED, SERVER_VALUE, FAIL
   *
   * @param self::CONFLICT_RESOLUTION_STRATEGY_* $conflictResolutionStrategy
   */
  public function setConflictResolutionStrategy($conflictResolutionStrategy)
  {
    $this->conflictResolutionStrategy = $conflictResolutionStrategy;
  }
  /**
   * @return self::CONFLICT_RESOLUTION_STRATEGY_*
   */
  public function getConflictResolutionStrategy()
  {
    return $this->conflictResolutionStrategy;
  }
  /**
   * The key of the entity to delete. The entity may or may not already exist.
   * Must have a complete key path and must not be reserved/read-only.
   *
   * @param Key $delete
   */
  public function setDelete(Key $delete)
  {
    $this->delete = $delete;
  }
  /**
   * @return Key
   */
  public function getDelete()
  {
    return $this->delete;
  }
  /**
   * The entity to insert. The entity must not already exist. The entity key's
   * final path element may be incomplete.
   *
   * @param Entity $insert
   */
  public function setInsert(Entity $insert)
  {
    $this->insert = $insert;
  }
  /**
   * @return Entity
   */
  public function getInsert()
  {
    return $this->insert;
  }
  /**
   * The properties to write in this mutation. None of the properties in the
   * mask may have a reserved name, except for `__key__`. This field is ignored
   * for `delete`. If the entity already exists, only properties referenced in
   * the mask are updated, others are left untouched. Properties referenced in
   * the mask but not in the entity are deleted.
   *
   * @param PropertyMask $propertyMask
   */
  public function setPropertyMask(PropertyMask $propertyMask)
  {
    $this->propertyMask = $propertyMask;
  }
  /**
   * @return PropertyMask
   */
  public function getPropertyMask()
  {
    return $this->propertyMask;
  }
  /**
   * Optional. The transforms to perform on the entity. This field can be set
   * only when the operation is `insert`, `update`, or `upsert`. If present, the
   * transforms are be applied to the entity regardless of the property mask, in
   * order, after the operation.
   *
   * @param PropertyTransform[] $propertyTransforms
   */
  public function setPropertyTransforms($propertyTransforms)
  {
    $this->propertyTransforms = $propertyTransforms;
  }
  /**
   * @return PropertyTransform[]
   */
  public function getPropertyTransforms()
  {
    return $this->propertyTransforms;
  }
  /**
   * The entity to update. The entity must already exist. Must have a complete
   * key path.
   *
   * @param Entity $update
   */
  public function setUpdate(Entity $update)
  {
    $this->update = $update;
  }
  /**
   * @return Entity
   */
  public function getUpdate()
  {
    return $this->update;
  }
  /**
   * The update time of the entity that this mutation is being applied to. If
   * this does not match the current update time on the server, the mutation
   * conflicts.
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
  /**
   * The entity to upsert. The entity may or may not already exist. The entity
   * key's final path element may be incomplete.
   *
   * @param Entity $upsert
   */
  public function setUpsert(Entity $upsert)
  {
    $this->upsert = $upsert;
  }
  /**
   * @return Entity
   */
  public function getUpsert()
  {
    return $this->upsert;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Mutation::class, 'Google_Service_Datastore_Mutation');
