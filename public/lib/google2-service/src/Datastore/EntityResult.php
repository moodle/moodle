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

class EntityResult extends \Google\Model
{
  /**
   * The time at which the entity was created. This field is set for `FULL`
   * entity results. If this entity is missing, this field will not be set.
   *
   * @var string
   */
  public $createTime;
  /**
   * A cursor that points to the position after the result entity. Set only when
   * the `EntityResult` is part of a `QueryResultBatch` message.
   *
   * @var string
   */
  public $cursor;
  protected $entityType = Entity::class;
  protected $entityDataType = '';
  /**
   * The time at which the entity was last changed. This field is set for `FULL`
   * entity results. If this entity is missing, this field will not be set.
   *
   * @var string
   */
  public $updateTime;
  /**
   * The version of the entity, a strictly positive number that monotonically
   * increases with changes to the entity. This field is set for `FULL` entity
   * results. For missing entities in `LookupResponse`, this is the version of
   * the snapshot that was used to look up the entity, and it is always set
   * except for eventually consistent reads.
   *
   * @var string
   */
  public $version;

  /**
   * The time at which the entity was created. This field is set for `FULL`
   * entity results. If this entity is missing, this field will not be set.
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
   * A cursor that points to the position after the result entity. Set only when
   * the `EntityResult` is part of a `QueryResultBatch` message.
   *
   * @param string $cursor
   */
  public function setCursor($cursor)
  {
    $this->cursor = $cursor;
  }
  /**
   * @return string
   */
  public function getCursor()
  {
    return $this->cursor;
  }
  /**
   * The resulting entity.
   *
   * @param Entity $entity
   */
  public function setEntity(Entity $entity)
  {
    $this->entity = $entity;
  }
  /**
   * @return Entity
   */
  public function getEntity()
  {
    return $this->entity;
  }
  /**
   * The time at which the entity was last changed. This field is set for `FULL`
   * entity results. If this entity is missing, this field will not be set.
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
   * The version of the entity, a strictly positive number that monotonically
   * increases with changes to the entity. This field is set for `FULL` entity
   * results. For missing entities in `LookupResponse`, this is the version of
   * the snapshot that was used to look up the entity, and it is always set
   * except for eventually consistent reads.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EntityResult::class, 'Google_Service_Datastore_EntityResult');
