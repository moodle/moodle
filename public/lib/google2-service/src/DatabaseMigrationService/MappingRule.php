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

class MappingRule extends \Google\Model
{
  /**
   * Unspecified database entity type.
   */
  public const RULE_SCOPE_DATABASE_ENTITY_TYPE_UNSPECIFIED = 'DATABASE_ENTITY_TYPE_UNSPECIFIED';
  /**
   * Schema.
   */
  public const RULE_SCOPE_DATABASE_ENTITY_TYPE_SCHEMA = 'DATABASE_ENTITY_TYPE_SCHEMA';
  /**
   * Table.
   */
  public const RULE_SCOPE_DATABASE_ENTITY_TYPE_TABLE = 'DATABASE_ENTITY_TYPE_TABLE';
  /**
   * Column.
   */
  public const RULE_SCOPE_DATABASE_ENTITY_TYPE_COLUMN = 'DATABASE_ENTITY_TYPE_COLUMN';
  /**
   * Constraint.
   */
  public const RULE_SCOPE_DATABASE_ENTITY_TYPE_CONSTRAINT = 'DATABASE_ENTITY_TYPE_CONSTRAINT';
  /**
   * Index.
   */
  public const RULE_SCOPE_DATABASE_ENTITY_TYPE_INDEX = 'DATABASE_ENTITY_TYPE_INDEX';
  /**
   * Trigger.
   */
  public const RULE_SCOPE_DATABASE_ENTITY_TYPE_TRIGGER = 'DATABASE_ENTITY_TYPE_TRIGGER';
  /**
   * View.
   */
  public const RULE_SCOPE_DATABASE_ENTITY_TYPE_VIEW = 'DATABASE_ENTITY_TYPE_VIEW';
  /**
   * Sequence.
   */
  public const RULE_SCOPE_DATABASE_ENTITY_TYPE_SEQUENCE = 'DATABASE_ENTITY_TYPE_SEQUENCE';
  /**
   * Stored Procedure.
   */
  public const RULE_SCOPE_DATABASE_ENTITY_TYPE_STORED_PROCEDURE = 'DATABASE_ENTITY_TYPE_STORED_PROCEDURE';
  /**
   * Function.
   */
  public const RULE_SCOPE_DATABASE_ENTITY_TYPE_FUNCTION = 'DATABASE_ENTITY_TYPE_FUNCTION';
  /**
   * Synonym.
   */
  public const RULE_SCOPE_DATABASE_ENTITY_TYPE_SYNONYM = 'DATABASE_ENTITY_TYPE_SYNONYM';
  /**
   * Package.
   */
  public const RULE_SCOPE_DATABASE_ENTITY_TYPE_DATABASE_PACKAGE = 'DATABASE_ENTITY_TYPE_DATABASE_PACKAGE';
  /**
   * UDT.
   */
  public const RULE_SCOPE_DATABASE_ENTITY_TYPE_UDT = 'DATABASE_ENTITY_TYPE_UDT';
  /**
   * Materialized View.
   */
  public const RULE_SCOPE_DATABASE_ENTITY_TYPE_MATERIALIZED_VIEW = 'DATABASE_ENTITY_TYPE_MATERIALIZED_VIEW';
  /**
   * Database.
   */
  public const RULE_SCOPE_DATABASE_ENTITY_TYPE_DATABASE = 'DATABASE_ENTITY_TYPE_DATABASE';
  /**
   * The state of the mapping rule is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The rule is enabled.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * The rule is disabled.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * The rule is logically deleted.
   */
  public const STATE_DELETED = 'DELETED';
  protected $conditionalColumnSetValueType = ConditionalColumnSetValue::class;
  protected $conditionalColumnSetValueDataType = '';
  protected $convertRowidColumnType = ConvertRowIdToColumn::class;
  protected $convertRowidColumnDataType = '';
  /**
   * Optional. A human readable name
   *
   * @var string
   */
  public $displayName;
  protected $entityMoveType = EntityMove::class;
  protected $entityMoveDataType = '';
  protected $filterType = MappingRuleFilter::class;
  protected $filterDataType = '';
  protected $filterTableColumnsType = FilterTableColumns::class;
  protected $filterTableColumnsDataType = '';
  protected $multiColumnDataTypeChangeType = MultiColumnDatatypeChange::class;
  protected $multiColumnDataTypeChangeDataType = '';
  protected $multiEntityRenameType = MultiEntityRename::class;
  protected $multiEntityRenameDataType = '';
  /**
   * Full name of the mapping rule resource, in the form of: projects/{project}/
   * locations/{location}/conversionWorkspaces/{set}/mappingRule/{rule}.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The timestamp that the revision was created.
   *
   * @var string
   */
  public $revisionCreateTime;
  /**
   * Output only. The revision ID of the mapping rule. A new revision is
   * committed whenever the mapping rule is changed in any way. The format is an
   * 8-character hexadecimal string.
   *
   * @var string
   */
  public $revisionId;
  /**
   * Required. The order in which the rule is applied. Lower order rules are
   * applied before higher value rules so they may end up being overridden.
   *
   * @var string
   */
  public $ruleOrder;
  /**
   * Required. The rule scope
   *
   * @var string
   */
  public $ruleScope;
  protected $setTablePrimaryKeyType = SetTablePrimaryKey::class;
  protected $setTablePrimaryKeyDataType = '';
  protected $singleColumnChangeType = SingleColumnChange::class;
  protected $singleColumnChangeDataType = '';
  protected $singleEntityRenameType = SingleEntityRename::class;
  protected $singleEntityRenameDataType = '';
  protected $singlePackageChangeType = SinglePackageChange::class;
  protected $singlePackageChangeDataType = '';
  protected $sourceSqlChangeType = SourceSqlChange::class;
  protected $sourceSqlChangeDataType = '';
  /**
   * Optional. The mapping rule state
   *
   * @var string
   */
  public $state;

  /**
   * Optional. Rule to specify how the data contained in a column should be
   * transformed (such as trimmed, rounded, etc) provided that the data meets
   * certain criteria.
   *
   * @param ConditionalColumnSetValue $conditionalColumnSetValue
   */
  public function setConditionalColumnSetValue(ConditionalColumnSetValue $conditionalColumnSetValue)
  {
    $this->conditionalColumnSetValue = $conditionalColumnSetValue;
  }
  /**
   * @return ConditionalColumnSetValue
   */
  public function getConditionalColumnSetValue()
  {
    return $this->conditionalColumnSetValue;
  }
  /**
   * Optional. Rule to specify how multiple tables should be converted with an
   * additional rowid column.
   *
   * @param ConvertRowIdToColumn $convertRowidColumn
   */
  public function setConvertRowidColumn(ConvertRowIdToColumn $convertRowidColumn)
  {
    $this->convertRowidColumn = $convertRowidColumn;
  }
  /**
   * @return ConvertRowIdToColumn
   */
  public function getConvertRowidColumn()
  {
    return $this->convertRowidColumn;
  }
  /**
   * Optional. A human readable name
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. Rule to specify how multiple entities should be relocated into a
   * different schema.
   *
   * @param EntityMove $entityMove
   */
  public function setEntityMove(EntityMove $entityMove)
  {
    $this->entityMove = $entityMove;
  }
  /**
   * @return EntityMove
   */
  public function getEntityMove()
  {
    return $this->entityMove;
  }
  /**
   * Required. The rule filter
   *
   * @param MappingRuleFilter $filter
   */
  public function setFilter(MappingRuleFilter $filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return MappingRuleFilter
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Optional. Rule to specify the list of columns to include or exclude from a
   * table.
   *
   * @param FilterTableColumns $filterTableColumns
   */
  public function setFilterTableColumns(FilterTableColumns $filterTableColumns)
  {
    $this->filterTableColumns = $filterTableColumns;
  }
  /**
   * @return FilterTableColumns
   */
  public function getFilterTableColumns()
  {
    return $this->filterTableColumns;
  }
  /**
   * Optional. Rule to specify how multiple columns should be converted to a
   * different data type.
   *
   * @param MultiColumnDatatypeChange $multiColumnDataTypeChange
   */
  public function setMultiColumnDataTypeChange(MultiColumnDatatypeChange $multiColumnDataTypeChange)
  {
    $this->multiColumnDataTypeChange = $multiColumnDataTypeChange;
  }
  /**
   * @return MultiColumnDatatypeChange
   */
  public function getMultiColumnDataTypeChange()
  {
    return $this->multiColumnDataTypeChange;
  }
  /**
   * Optional. Rule to specify how multiple entities should be renamed.
   *
   * @param MultiEntityRename $multiEntityRename
   */
  public function setMultiEntityRename(MultiEntityRename $multiEntityRename)
  {
    $this->multiEntityRename = $multiEntityRename;
  }
  /**
   * @return MultiEntityRename
   */
  public function getMultiEntityRename()
  {
    return $this->multiEntityRename;
  }
  /**
   * Full name of the mapping rule resource, in the form of: projects/{project}/
   * locations/{location}/conversionWorkspaces/{set}/mappingRule/{rule}.
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
  /**
   * Output only. The timestamp that the revision was created.
   *
   * @param string $revisionCreateTime
   */
  public function setRevisionCreateTime($revisionCreateTime)
  {
    $this->revisionCreateTime = $revisionCreateTime;
  }
  /**
   * @return string
   */
  public function getRevisionCreateTime()
  {
    return $this->revisionCreateTime;
  }
  /**
   * Output only. The revision ID of the mapping rule. A new revision is
   * committed whenever the mapping rule is changed in any way. The format is an
   * 8-character hexadecimal string.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * Required. The order in which the rule is applied. Lower order rules are
   * applied before higher value rules so they may end up being overridden.
   *
   * @param string $ruleOrder
   */
  public function setRuleOrder($ruleOrder)
  {
    $this->ruleOrder = $ruleOrder;
  }
  /**
   * @return string
   */
  public function getRuleOrder()
  {
    return $this->ruleOrder;
  }
  /**
   * Required. The rule scope
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
   * @param self::RULE_SCOPE_* $ruleScope
   */
  public function setRuleScope($ruleScope)
  {
    $this->ruleScope = $ruleScope;
  }
  /**
   * @return self::RULE_SCOPE_*
   */
  public function getRuleScope()
  {
    return $this->ruleScope;
  }
  /**
   * Optional. Rule to specify the primary key for a table
   *
   * @param SetTablePrimaryKey $setTablePrimaryKey
   */
  public function setSetTablePrimaryKey(SetTablePrimaryKey $setTablePrimaryKey)
  {
    $this->setTablePrimaryKey = $setTablePrimaryKey;
  }
  /**
   * @return SetTablePrimaryKey
   */
  public function getSetTablePrimaryKey()
  {
    return $this->setTablePrimaryKey;
  }
  /**
   * Optional. Rule to specify how a single column is converted.
   *
   * @param SingleColumnChange $singleColumnChange
   */
  public function setSingleColumnChange(SingleColumnChange $singleColumnChange)
  {
    $this->singleColumnChange = $singleColumnChange;
  }
  /**
   * @return SingleColumnChange
   */
  public function getSingleColumnChange()
  {
    return $this->singleColumnChange;
  }
  /**
   * Optional. Rule to specify how a single entity should be renamed.
   *
   * @param SingleEntityRename $singleEntityRename
   */
  public function setSingleEntityRename(SingleEntityRename $singleEntityRename)
  {
    $this->singleEntityRename = $singleEntityRename;
  }
  /**
   * @return SingleEntityRename
   */
  public function getSingleEntityRename()
  {
    return $this->singleEntityRename;
  }
  /**
   * Optional. Rule to specify how a single package is converted.
   *
   * @param SinglePackageChange $singlePackageChange
   */
  public function setSinglePackageChange(SinglePackageChange $singlePackageChange)
  {
    $this->singlePackageChange = $singlePackageChange;
  }
  /**
   * @return SinglePackageChange
   */
  public function getSinglePackageChange()
  {
    return $this->singlePackageChange;
  }
  /**
   * Optional. Rule to change the sql code for an entity, for example, function,
   * procedure.
   *
   * @param SourceSqlChange $sourceSqlChange
   */
  public function setSourceSqlChange(SourceSqlChange $sourceSqlChange)
  {
    $this->sourceSqlChange = $sourceSqlChange;
  }
  /**
   * @return SourceSqlChange
   */
  public function getSourceSqlChange()
  {
    return $this->sourceSqlChange;
  }
  /**
   * Optional. The mapping rule state
   *
   * Accepted values: STATE_UNSPECIFIED, ENABLED, DISABLED, DELETED
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
class_alias(MappingRule::class, 'Google_Service_DatabaseMigrationService_MappingRule');
