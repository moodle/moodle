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

class SynonymEntity extends \Google\Model
{
  /**
   * Unspecified database entity type.
   */
  public const SOURCE_TYPE_DATABASE_ENTITY_TYPE_UNSPECIFIED = 'DATABASE_ENTITY_TYPE_UNSPECIFIED';
  /**
   * Schema.
   */
  public const SOURCE_TYPE_DATABASE_ENTITY_TYPE_SCHEMA = 'DATABASE_ENTITY_TYPE_SCHEMA';
  /**
   * Table.
   */
  public const SOURCE_TYPE_DATABASE_ENTITY_TYPE_TABLE = 'DATABASE_ENTITY_TYPE_TABLE';
  /**
   * Column.
   */
  public const SOURCE_TYPE_DATABASE_ENTITY_TYPE_COLUMN = 'DATABASE_ENTITY_TYPE_COLUMN';
  /**
   * Constraint.
   */
  public const SOURCE_TYPE_DATABASE_ENTITY_TYPE_CONSTRAINT = 'DATABASE_ENTITY_TYPE_CONSTRAINT';
  /**
   * Index.
   */
  public const SOURCE_TYPE_DATABASE_ENTITY_TYPE_INDEX = 'DATABASE_ENTITY_TYPE_INDEX';
  /**
   * Trigger.
   */
  public const SOURCE_TYPE_DATABASE_ENTITY_TYPE_TRIGGER = 'DATABASE_ENTITY_TYPE_TRIGGER';
  /**
   * View.
   */
  public const SOURCE_TYPE_DATABASE_ENTITY_TYPE_VIEW = 'DATABASE_ENTITY_TYPE_VIEW';
  /**
   * Sequence.
   */
  public const SOURCE_TYPE_DATABASE_ENTITY_TYPE_SEQUENCE = 'DATABASE_ENTITY_TYPE_SEQUENCE';
  /**
   * Stored Procedure.
   */
  public const SOURCE_TYPE_DATABASE_ENTITY_TYPE_STORED_PROCEDURE = 'DATABASE_ENTITY_TYPE_STORED_PROCEDURE';
  /**
   * Function.
   */
  public const SOURCE_TYPE_DATABASE_ENTITY_TYPE_FUNCTION = 'DATABASE_ENTITY_TYPE_FUNCTION';
  /**
   * Synonym.
   */
  public const SOURCE_TYPE_DATABASE_ENTITY_TYPE_SYNONYM = 'DATABASE_ENTITY_TYPE_SYNONYM';
  /**
   * Package.
   */
  public const SOURCE_TYPE_DATABASE_ENTITY_TYPE_DATABASE_PACKAGE = 'DATABASE_ENTITY_TYPE_DATABASE_PACKAGE';
  /**
   * UDT.
   */
  public const SOURCE_TYPE_DATABASE_ENTITY_TYPE_UDT = 'DATABASE_ENTITY_TYPE_UDT';
  /**
   * Materialized View.
   */
  public const SOURCE_TYPE_DATABASE_ENTITY_TYPE_MATERIALIZED_VIEW = 'DATABASE_ENTITY_TYPE_MATERIALIZED_VIEW';
  /**
   * Database.
   */
  public const SOURCE_TYPE_DATABASE_ENTITY_TYPE_DATABASE = 'DATABASE_ENTITY_TYPE_DATABASE';
  /**
   * Custom engine specific features.
   *
   * @var array[]
   */
  public $customFeatures;
  /**
   * The name of the entity for which the synonym is being created (the source).
   *
   * @var string
   */
  public $sourceEntity;
  /**
   * The type of the entity for which the synonym is being created (usually a
   * table or a sequence).
   *
   * @var string
   */
  public $sourceType;

  /**
   * Custom engine specific features.
   *
   * @param array[] $customFeatures
   */
  public function setCustomFeatures($customFeatures)
  {
    $this->customFeatures = $customFeatures;
  }
  /**
   * @return array[]
   */
  public function getCustomFeatures()
  {
    return $this->customFeatures;
  }
  /**
   * The name of the entity for which the synonym is being created (the source).
   *
   * @param string $sourceEntity
   */
  public function setSourceEntity($sourceEntity)
  {
    $this->sourceEntity = $sourceEntity;
  }
  /**
   * @return string
   */
  public function getSourceEntity()
  {
    return $this->sourceEntity;
  }
  /**
   * The type of the entity for which the synonym is being created (usually a
   * table or a sequence).
   *
   * Accepted values: DATABASE_ENTITY_TYPE_UNSPECIFIED,
   * DATABASE_ENTITY_TYPE_SCHEMA, DATABASE_ENTITY_TYPE_TABLE,
   * DATABASE_ENTITY_TYPE_COLUMN, DATABASE_ENTITY_TYPE_CONSTRAINT,
   * DATABASE_ENTITY_TYPE_INDEX, DATABASE_ENTITY_TYPE_TRIGGER,
   * DATABASE_ENTITY_TYPE_VIEW, DATABASE_ENTITY_TYPE_SEQUENCE,
   * DATABASE_ENTITY_TYPE_STORED_PROCEDURE, DATABASE_ENTITY_TYPE_FUNCTION,
   * DATABASE_ENTITY_TYPE_SYNONYM, DATABASE_ENTITY_TYPE_DATABASE_PACKAGE,
   * DATABASE_ENTITY_TYPE_UDT, DATABASE_ENTITY_TYPE_MATERIALIZED_VIEW,
   * DATABASE_ENTITY_TYPE_DATABASE
   *
   * @param self::SOURCE_TYPE_* $sourceType
   */
  public function setSourceType($sourceType)
  {
    $this->sourceType = $sourceType;
  }
  /**
   * @return self::SOURCE_TYPE_*
   */
  public function getSourceType()
  {
    return $this->sourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SynonymEntity::class, 'Google_Service_DatabaseMigrationService_SynonymEntity');
