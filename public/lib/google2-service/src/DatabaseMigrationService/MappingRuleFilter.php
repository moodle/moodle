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

namespace Google\Service\DatabaseMigrationService;

class MappingRuleFilter extends \Google\Collection
{
  protected $collection_key = 'entities';
  /**
   * Optional. The rule should be applied to specific entities defined by their
   * fully qualified names.
   *
   * @var string[]
   */
  public $entities;
  /**
   * Optional. The rule should be applied to entities whose non-qualified name
   * contains the given string.
   *
   * @var string
   */
  public $entityNameContains;
  /**
   * Optional. The rule should be applied to entities whose non-qualified name
   * starts with the given prefix.
   *
   * @var string
   */
  public $entityNamePrefix;
  /**
   * Optional. The rule should be applied to entities whose non-qualified name
   * ends with the given suffix.
   *
   * @var string
   */
  public $entityNameSuffix;
  /**
   * Optional. The rule should be applied to entities whose parent entity (fully
   * qualified name) matches the given value. For example, if the rule applies
   * to a table entity, the expected value should be a schema (schema). If the
   * rule applies to a column or index entity, the expected value can be either
   * a schema (schema) or a table (schema.table)
   *
   * @var string
   */
  public $parentEntity;

  /**
   * Optional. The rule should be applied to specific entities defined by their
   * fully qualified names.
   *
   * @param string[] $entities
   */
  public function setEntities($entities)
  {
    $this->entities = $entities;
  }
  /**
   * @return string[]
   */
  public function getEntities()
  {
    return $this->entities;
  }
  /**
   * Optional. The rule should be applied to entities whose non-qualified name
   * contains the given string.
   *
   * @param string $entityNameContains
   */
  public function setEntityNameContains($entityNameContains)
  {
    $this->entityNameContains = $entityNameContains;
  }
  /**
   * @return string
   */
  public function getEntityNameContains()
  {
    return $this->entityNameContains;
  }
  /**
   * Optional. The rule should be applied to entities whose non-qualified name
   * starts with the given prefix.
   *
   * @param string $entityNamePrefix
   */
  public function setEntityNamePrefix($entityNamePrefix)
  {
    $this->entityNamePrefix = $entityNamePrefix;
  }
  /**
   * @return string
   */
  public function getEntityNamePrefix()
  {
    return $this->entityNamePrefix;
  }
  /**
   * Optional. The rule should be applied to entities whose non-qualified name
   * ends with the given suffix.
   *
   * @param string $entityNameSuffix
   */
  public function setEntityNameSuffix($entityNameSuffix)
  {
    $this->entityNameSuffix = $entityNameSuffix;
  }
  /**
   * @return string
   */
  public function getEntityNameSuffix()
  {
    return $this->entityNameSuffix;
  }
  /**
   * Optional. The rule should be applied to entities whose parent entity (fully
   * qualified name) matches the given value. For example, if the rule applies
   * to a table entity, the expected value should be a schema (schema). If the
   * rule applies to a column or index entity, the expected value can be either
   * a schema (schema) or a table (schema.table)
   *
   * @param string $parentEntity
   */
  public function setParentEntity($parentEntity)
  {
    $this->parentEntity = $parentEntity;
  }
  /**
   * @return string
   */
  public function getParentEntity()
  {
    return $this->parentEntity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MappingRuleFilter::class, 'Google_Service_DatabaseMigrationService_MappingRuleFilter');
