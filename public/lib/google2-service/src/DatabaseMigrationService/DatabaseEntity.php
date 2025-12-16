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

class DatabaseEntity extends \Google\Collection
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
   * Tree type unspecified.
   */
  public const TREE_TREE_TYPE_UNSPECIFIED = 'TREE_TYPE_UNSPECIFIED';
  /**
   * Tree of entities loaded from a source database.
   */
  public const TREE_SOURCE = 'SOURCE';
  /**
   * Tree of entities converted from the source tree using the mapping rules.
   */
  public const TREE_DRAFT = 'DRAFT';
  /**
   * Tree of entities observed on the destination database.
   */
  public const TREE_DESTINATION = 'DESTINATION';
  protected $collection_key = 'mappings';
  protected $databaseType = DatabaseInstanceEntity::class;
  protected $databaseDataType = '';
  protected $databaseFunctionType = FunctionEntity::class;
  protected $databaseFunctionDataType = '';
  protected $databasePackageType = PackageEntity::class;
  protected $databasePackageDataType = '';
  protected $entityDdlType = EntityDdl::class;
  protected $entityDdlDataType = 'array';
  /**
   * The type of the database entity (table, view, index, ...).
   *
   * @var string
   */
  public $entityType;
  protected $issuesType = EntityIssue::class;
  protected $issuesDataType = 'array';
  protected $mappingsType = EntityMapping::class;
  protected $mappingsDataType = 'array';
  protected $materializedViewType = MaterializedViewEntity::class;
  protected $materializedViewDataType = '';
  /**
   * The full name of the parent entity (e.g. schema name).
   *
   * @var string
   */
  public $parentEntity;
  protected $schemaType = SchemaEntity::class;
  protected $schemaDataType = '';
  protected $sequenceType = SequenceEntity::class;
  protected $sequenceDataType = '';
  /**
   * The short name (e.g. table name) of the entity.
   *
   * @var string
   */
  public $shortName;
  protected $storedProcedureType = StoredProcedureEntity::class;
  protected $storedProcedureDataType = '';
  protected $synonymType = SynonymEntity::class;
  protected $synonymDataType = '';
  protected $tableType = TableEntity::class;
  protected $tableDataType = '';
  /**
   * The type of tree the entity belongs to.
   *
   * @var string
   */
  public $tree;
  protected $udtType = UDTEntity::class;
  protected $udtDataType = '';
  protected $viewType = ViewEntity::class;
  protected $viewDataType = '';

  /**
   * Database.
   *
   * @param DatabaseInstanceEntity $database
   */
  public function setDatabase(DatabaseInstanceEntity $database)
  {
    $this->database = $database;
  }
  /**
   * @return DatabaseInstanceEntity
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * Function.
   *
   * @param FunctionEntity $databaseFunction
   */
  public function setDatabaseFunction(FunctionEntity $databaseFunction)
  {
    $this->databaseFunction = $databaseFunction;
  }
  /**
   * @return FunctionEntity
   */
  public function getDatabaseFunction()
  {
    return $this->databaseFunction;
  }
  /**
   * Package.
   *
   * @param PackageEntity $databasePackage
   */
  public function setDatabasePackage(PackageEntity $databasePackage)
  {
    $this->databasePackage = $databasePackage;
  }
  /**
   * @return PackageEntity
   */
  public function getDatabasePackage()
  {
    return $this->databasePackage;
  }
  /**
   * Details about the entity DDL script. Multiple DDL scripts are provided for
   * child entities such as a table entity will have one DDL for the table with
   * additional DDLs for each index, constraint and such.
   *
   * @param EntityDdl[] $entityDdl
   */
  public function setEntityDdl($entityDdl)
  {
    $this->entityDdl = $entityDdl;
  }
  /**
   * @return EntityDdl[]
   */
  public function getEntityDdl()
  {
    return $this->entityDdl;
  }
  /**
   * The type of the database entity (table, view, index, ...).
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
   * Details about the various issues found for the entity.
   *
   * @param EntityIssue[] $issues
   */
  public function setIssues($issues)
  {
    $this->issues = $issues;
  }
  /**
   * @return EntityIssue[]
   */
  public function getIssues()
  {
    return $this->issues;
  }
  /**
   * Details about entity mappings. For source tree entities, this holds the
   * draft entities which were generated by the mapping rules. For draft tree
   * entities, this holds the source entities which were converted to form the
   * draft entity. Destination entities will have no mapping details.
   *
   * @param EntityMapping[] $mappings
   */
  public function setMappings($mappings)
  {
    $this->mappings = $mappings;
  }
  /**
   * @return EntityMapping[]
   */
  public function getMappings()
  {
    return $this->mappings;
  }
  /**
   * Materialized view.
   *
   * @param MaterializedViewEntity $materializedView
   */
  public function setMaterializedView(MaterializedViewEntity $materializedView)
  {
    $this->materializedView = $materializedView;
  }
  /**
   * @return MaterializedViewEntity
   */
  public function getMaterializedView()
  {
    return $this->materializedView;
  }
  /**
   * The full name of the parent entity (e.g. schema name).
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
  /**
   * Schema.
   *
   * @param SchemaEntity $schema
   */
  public function setSchema(SchemaEntity $schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return SchemaEntity
   */
  public function getSchema()
  {
    return $this->schema;
  }
  /**
   * Sequence.
   *
   * @param SequenceEntity $sequence
   */
  public function setSequence(SequenceEntity $sequence)
  {
    $this->sequence = $sequence;
  }
  /**
   * @return SequenceEntity
   */
  public function getSequence()
  {
    return $this->sequence;
  }
  /**
   * The short name (e.g. table name) of the entity.
   *
   * @param string $shortName
   */
  public function setShortName($shortName)
  {
    $this->shortName = $shortName;
  }
  /**
   * @return string
   */
  public function getShortName()
  {
    return $this->shortName;
  }
  /**
   * Stored procedure.
   *
   * @param StoredProcedureEntity $storedProcedure
   */
  public function setStoredProcedure(StoredProcedureEntity $storedProcedure)
  {
    $this->storedProcedure = $storedProcedure;
  }
  /**
   * @return StoredProcedureEntity
   */
  public function getStoredProcedure()
  {
    return $this->storedProcedure;
  }
  /**
   * Synonym.
   *
   * @param SynonymEntity $synonym
   */
  public function setSynonym(SynonymEntity $synonym)
  {
    $this->synonym = $synonym;
  }
  /**
   * @return SynonymEntity
   */
  public function getSynonym()
  {
    return $this->synonym;
  }
  /**
   * Table.
   *
   * @param TableEntity $table
   */
  public function setTable(TableEntity $table)
  {
    $this->table = $table;
  }
  /**
   * @return TableEntity
   */
  public function getTable()
  {
    return $this->table;
  }
  /**
   * The type of tree the entity belongs to.
   *
   * Accepted values: TREE_TYPE_UNSPECIFIED, SOURCE, DRAFT, DESTINATION
   *
   * @param self::TREE_* $tree
   */
  public function setTree($tree)
  {
    $this->tree = $tree;
  }
  /**
   * @return self::TREE_*
   */
  public function getTree()
  {
    return $this->tree;
  }
  /**
   * UDT.
   *
   * @param UDTEntity $udt
   */
  public function setUdt(UDTEntity $udt)
  {
    $this->udt = $udt;
  }
  /**
   * @return UDTEntity
   */
  public function getUdt()
  {
    return $this->udt;
  }
  /**
   * View.
   *
   * @param ViewEntity $view
   */
  public function setView(ViewEntity $view)
  {
    $this->view = $view;
  }
  /**
   * @return ViewEntity
   */
  public function getView()
  {
    return $this->view;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseEntity::class, 'Google_Service_DatabaseMigrationService_DatabaseEntity');
