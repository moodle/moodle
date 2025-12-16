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

class EntityIssue extends \Google\Model
{
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
  /**
   * Unspecified issue severity
   */
  public const SEVERITY_ISSUE_SEVERITY_UNSPECIFIED = 'ISSUE_SEVERITY_UNSPECIFIED';
  /**
   * Info
   */
  public const SEVERITY_ISSUE_SEVERITY_INFO = 'ISSUE_SEVERITY_INFO';
  /**
   * Warning
   */
  public const SEVERITY_ISSUE_SEVERITY_WARNING = 'ISSUE_SEVERITY_WARNING';
  /**
   * Error
   */
  public const SEVERITY_ISSUE_SEVERITY_ERROR = 'ISSUE_SEVERITY_ERROR';
  /**
   * Unspecified issue type.
   */
  public const TYPE_ISSUE_TYPE_UNSPECIFIED = 'ISSUE_TYPE_UNSPECIFIED';
  /**
   * Issue originated from the DDL
   */
  public const TYPE_ISSUE_TYPE_DDL = 'ISSUE_TYPE_DDL';
  /**
   * Issue originated during the apply process
   */
  public const TYPE_ISSUE_TYPE_APPLY = 'ISSUE_TYPE_APPLY';
  /**
   * Issue originated during the convert process
   */
  public const TYPE_ISSUE_TYPE_CONVERT = 'ISSUE_TYPE_CONVERT';
  /**
   * Error/Warning code
   *
   * @var string
   */
  public $code;
  /**
   * The ddl which caused the issue, if relevant.
   *
   * @var string
   */
  public $ddl;
  /**
   * The entity type (if the DDL is for a sub entity).
   *
   * @var string
   */
  public $entityType;
  /**
   * Unique Issue ID.
   *
   * @var string
   */
  public $id;
  /**
   * Issue detailed message
   *
   * @var string
   */
  public $message;
  protected $positionType = Position::class;
  protected $positionDataType = '';
  /**
   * Severity of the issue
   *
   * @var string
   */
  public $severity;
  /**
   * The type of the issue.
   *
   * @var string
   */
  public $type;

  /**
   * Error/Warning code
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * The ddl which caused the issue, if relevant.
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
   * Unique Issue ID.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Issue detailed message
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * The position of the issue found, if relevant.
   *
   * @param Position $position
   */
  public function setPosition(Position $position)
  {
    $this->position = $position;
  }
  /**
   * @return Position
   */
  public function getPosition()
  {
    return $this->position;
  }
  /**
   * Severity of the issue
   *
   * Accepted values: ISSUE_SEVERITY_UNSPECIFIED, ISSUE_SEVERITY_INFO,
   * ISSUE_SEVERITY_WARNING, ISSUE_SEVERITY_ERROR
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * The type of the issue.
   *
   * Accepted values: ISSUE_TYPE_UNSPECIFIED, ISSUE_TYPE_DDL, ISSUE_TYPE_APPLY,
   * ISSUE_TYPE_CONVERT
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
class_alias(EntityIssue::class, 'Google_Service_DatabaseMigrationService_EntityIssue');
