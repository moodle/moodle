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

class EntityDdl extends \Google\Collection
{
  /**
   * The kind of the DDL is unknown.
   */
  public const DDL_KIND_DDL_KIND_UNSPECIFIED = 'DDL_KIND_UNSPECIFIED';
  /**
   * DDL of the source entity
   */
  public const DDL_KIND_SOURCE = 'SOURCE';
  /**
   * Deterministic converted DDL
   */
  public const DDL_KIND_DETERMINISTIC = 'DETERMINISTIC';
  /**
   * Gemini AI converted DDL
   */
  public const DDL_KIND_AI = 'AI';
  /**
   * User edited DDL
   */
  public const DDL_KIND_USER_EDIT = 'USER_EDIT';
  /**
   * The kind of the DDL is unknown.
   */
  public const EDITED_DDL_KIND_DDL_KIND_UNSPECIFIED = 'DDL_KIND_UNSPECIFIED';
  /**
   * DDL of the source entity
   */
  public const EDITED_DDL_KIND_SOURCE = 'SOURCE';
  /**
   * Deterministic converted DDL
   */
  public const EDITED_DDL_KIND_DETERMINISTIC = 'DETERMINISTIC';
  /**
   * Gemini AI converted DDL
   */
  public const EDITED_DDL_KIND_AI = 'AI';
  /**
   * User edited DDL
   */
  public const EDITED_DDL_KIND_USER_EDIT = 'USER_EDIT';
  /**
   * Unspecified database entity type.
   */
  public const ENTITY_TYPE_DATABASE_ENTITY_TYPE_UNSPECIFIED = 'DATABASE_ENTITY_TYPE_UNSPECIFIED';
  /**
   * Schema.
   */
  public const ENTITY_TYPE_DATABASE_ENTITY_TYPE_SCHEMA = 'DATABASE_ENTITY_TYPE_SCHEMA';
  /**
   * Table.
   */
  public const ENTITY_TYPE_DATABASE_ENTITY_TYPE_TABLE = 'DATABASE_ENTITY_TYPE_TABLE';
  /**
   * Column.
   */
  public const ENTITY_TYPE_DATABASE_ENTITY_TYPE_COLUMN = 'DATABASE_ENTITY_TYPE_COLUMN';
  /**
   * Constraint.
   */
  public const ENTITY_TYPE_DATABASE_ENTITY_TYPE_CONSTRAINT = 'DATABASE_ENTITY_TYPE_CONSTRAINT';
  /**
   * Index.
   */
  public const ENTITY_TYPE_DATABASE_ENTITY_TYPE_INDEX = 'DATABASE_ENTITY_TYPE_INDEX';
  /**
   * Trigger.
   */
  public const ENTITY_TYPE_DATABASE_ENTITY_TYPE_TRIGGER = 'DATABASE_ENTITY_TYPE_TRIGGER';
  /**
   * View.
   */
  public const ENTITY_TYPE_DATABASE_ENTITY_TYPE_VIEW = 'DATABASE_ENTITY_TYPE_VIEW';
  /**
   * Sequence.
   */
  public const ENTITY_TYPE_DATABASE_ENTITY_TYPE_SEQUENCE = 'DATABASE_ENTITY_TYPE_SEQUENCE';
  /**
   * Stored Procedure.
   */
  public const ENTITY_TYPE_DATABASE_ENTITY_TYPE_STORED_PROCEDURE = 'DATABASE_ENTITY_TYPE_STORED_PROCEDURE';
  /**
   * Function.
   */
  public const ENTITY_TYPE_DATABASE_ENTITY_TYPE_FUNCTION = 'DATABASE_ENTITY_TYPE_FUNCTION';
  /**
   * Synonym.
   */
  public const ENTITY_TYPE_DATABASE_ENTITY_TYPE_SYNONYM = 'DATABASE_ENTITY_TYPE_SYNONYM';
  /**
   * Package.
   */
  public const ENTITY_TYPE_DATABASE_ENTITY_TYPE_DATABASE_PACKAGE = 'DATABASE_ENTITY_TYPE_DATABASE_PACKAGE';
  /**
   * UDT.
   */
  public const ENTITY_TYPE_DATABASE_ENTITY_TYPE_UDT = 'DATABASE_ENTITY_TYPE_UDT';
  /**
   * Materialized View.
   */
  public const ENTITY_TYPE_DATABASE_ENTITY_TYPE_MATERIALIZED_VIEW = 'DATABASE_ENTITY_TYPE_MATERIALIZED_VIEW';
  /**
   * Database.
   */
  public const ENTITY_TYPE_DATABASE_ENTITY_TYPE_DATABASE = 'DATABASE_ENTITY_TYPE_DATABASE';
  protected $collection_key = 'issueId';
  /**
   * The actual ddl code.
   *
   * @var string
   */
  public $ddl;
  /**
   * The DDL Kind selected for apply, or UNSPECIFIED if the entity wasn't
   * converted yet.
   *
   * @var string
   */
  public $ddlKind;
  /**
   * Type of DDL (Create, Alter).
   *
   * @var string
   */
  public $ddlType;
  /**
   * If ddl_kind is USER_EDIT, this holds the DDL kind of the original content -
   * DETERMINISTIC or AI. Otherwise, this is DDL_KIND_UNSPECIFIED.
   *
   * @var string
   */
  public $editedDdlKind;
  /**
   * The name of the database entity the ddl refers to.
   *
   * @var string
   */
  public $entity;
  /**
   * The entity type (if the DDL is for a sub entity).
   *
   * @var string
   */
  public $entityType;
  /**
   * EntityIssues found for this ddl.
   *
   * @var string[]
   */
  public $issueId;

  /**
   * The actual ddl code.
   *
   * @param string $ddl
   */
  public function setDdl($ddl)
  {
    $this->ddl = $ddl;
  }
  /**
   * @return string
   */
  public function getDdl()
  {
    return $this->ddl;
  }
  /**
   * The DDL Kind selected for apply, or UNSPECIFIED if the entity wasn't
   * converted yet.
   *
   * Accepted values: DDL_KIND_UNSPECIFIED, SOURCE, DETERMINISTIC, AI, USER_EDIT
   *
   * @param self::DDL_KIND_* $ddlKind
   */
  public function setDdlKind($ddlKind)
  {
    $this->ddlKind = $ddlKind;
  }
  /**
   * @return self::DDL_KIND_*
   */
  public function getDdlKind()
  {
    return $this->ddlKind;
  }
  /**
   * Type of DDL (Create, Alter).
   *
   * @param string $ddlType
   */
  public function setDdlType($ddlType)
  {
    $this->ddlType = $ddlType;
  }
  /**
   * @return string
   */
  public function getDdlType()
  {
    return $this->ddlType;
  }
  /**
   * If ddl_kind is USER_EDIT, this holds the DDL kind of the original content -
   * DETERMINISTIC or AI. Otherwise, this is DDL_KIND_UNSPECIFIED.
   *
   * Accepted values: DDL_KIND_UNSPECIFIED, SOURCE, DETERMINISTIC, AI, USER_EDIT
   *
   * @param self::EDITED_DDL_KIND_* $editedDdlKind
   */
  public function setEditedDdlKind($editedDdlKind)
  {
    $this->editedDdlKind = $editedDdlKind;
  }
  /**
   * @return self::EDITED_DDL_KIND_*
   */
  public function getEditedDdlKind()
  {
    return $this->editedDdlKind;
  }
  /**
   * The name of the database entity the ddl refers to.
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
   * The entity type (if the DDL is for a sub entity).
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
   * @param self::ENTITY_TYPE_* $entityType
   */
  public function setEntityType($entityType)
  {
    $this->entityType = $entityType;
  }
  /**
   * @return self::ENTITY_TYPE_*
   */
  public function getEntityType()
  {
    return $this->entityType;
  }
  /**
   * EntityIssues found for this ddl.
   *
   * @param string[] $issueId
   */
  public function setIssueId($issueId)
  {
    $this->issueId = $issueId;
  }
  /**
   * @return string[]
   */
  public function getIssueId()
  {
    return $this->issueId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EntityDdl::class, 'Google_Service_DatabaseMigrationService_EntityDdl');
