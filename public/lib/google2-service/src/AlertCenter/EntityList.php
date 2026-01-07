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

namespace Google\Service\AlertCenter;

class EntityList extends \Google\Collection
{
  protected $collection_key = 'headers';
  protected $entitiesType = Entity::class;
  protected $entitiesDataType = 'array';
  /**
   * Headers of the values in entities. If no value is defined in Entity, this
   * field should be empty.
   *
   * @var string[]
   */
  public $headers;
  /**
   * Name of the key detail used to display this entity list.
   *
   * @var string
   */
  public $name;

  /**
   * List of entities affected by the alert.
   *
   * @param Entity[] $entities
   */
  public function setEntities($entities)
  {
    $this->entities = $entities;
  }
  /**
   * @return Entity[]
   */
  public function getEntities()
  {
    return $this->entities;
  }
  /**
   * Headers of the values in entities. If no value is defined in Entity, this
   * field should be empty.
   *
   * @param string[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return string[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  /**
   * Name of the key detail used to display this entity list.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EntityList::class, 'Google_Service_AlertCenter_EntityList');
