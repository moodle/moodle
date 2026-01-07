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

namespace Google\Service\Connectors;

class Entity extends \Google\Model
{
  /**
   * Fields of the entity. The key is name of the field and the value contains
   * the applicable `google.protobuf.Value` entry for this field.
   *
   * @var array[]
   */
  public $fields;
  /**
   * Metadata like service latency, etc.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * Output only. Resource name of the Entity. Format: projects/{project}/locati
   * ons/{location}/connections/{connection}/entityTypes/{type}/entities/{id}
   *
   * @var string
   */
  public $name;

  /**
   * Fields of the entity. The key is name of the field and the value contains
   * the applicable `google.protobuf.Value` entry for this field.
   *
   * @param array[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return array[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * Metadata like service latency, etc.
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Output only. Resource name of the Entity. Format: projects/{project}/locati
   * ons/{location}/connections/{connection}/entityTypes/{type}/entities/{id}
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
class_alias(Entity::class, 'Google_Service_Connectors_Entity');
