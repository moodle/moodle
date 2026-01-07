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

namespace Google\Service\Sheets;

class TableColumnProperties extends \Google\Model
{
  /**
   * An unspecified column type.
   */
  public const COLUMN_TYPE_COLUMN_TYPE_UNSPECIFIED = 'COLUMN_TYPE_UNSPECIFIED';
  /**
   * The number column type.
   */
  public const COLUMN_TYPE_DOUBLE = 'DOUBLE';
  /**
   * The currency column type.
   */
  public const COLUMN_TYPE_CURRENCY = 'CURRENCY';
  /**
   * The percent column type.
   */
  public const COLUMN_TYPE_PERCENT = 'PERCENT';
  /**
   * The date column type.
   */
  public const COLUMN_TYPE_DATE = 'DATE';
  /**
   * The time column type.
   */
  public const COLUMN_TYPE_TIME = 'TIME';
  /**
   * The date and time column type.
   */
  public const COLUMN_TYPE_DATE_TIME = 'DATE_TIME';
  /**
   * The text column type.
   */
  public const COLUMN_TYPE_TEXT = 'TEXT';
  /**
   * The boolean column type.
   */
  public const COLUMN_TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * The dropdown column type.
   */
  public const COLUMN_TYPE_DROPDOWN = 'DROPDOWN';
  /**
   * The files chip column type
   */
  public const COLUMN_TYPE_FILES_CHIP = 'FILES_CHIP';
  /**
   * The people chip column type
   */
  public const COLUMN_TYPE_PEOPLE_CHIP = 'PEOPLE_CHIP';
  /**
   * The finance chip column type
   */
  public const COLUMN_TYPE_FINANCE_CHIP = 'FINANCE_CHIP';
  /**
   * The place chip column type
   */
  public const COLUMN_TYPE_PLACE_CHIP = 'PLACE_CHIP';
  /**
   * The ratings chip column type
   */
  public const COLUMN_TYPE_RATINGS_CHIP = 'RATINGS_CHIP';
  /**
   * The 0-based column index. This index is relative to its position in the
   * table and is not necessarily the same as the column index in the sheet.
   *
   * @var int
   */
  public $columnIndex;
  /**
   * The column name.
   *
   * @var string
   */
  public $columnName;
  /**
   * The column type.
   *
   * @var string
   */
  public $columnType;
  protected $dataValidationRuleType = TableColumnDataValidationRule::class;
  protected $dataValidationRuleDataType = '';

  /**
   * The 0-based column index. This index is relative to its position in the
   * table and is not necessarily the same as the column index in the sheet.
   *
   * @param int $columnIndex
   */
  public function setColumnIndex($columnIndex)
  {
    $this->columnIndex = $columnIndex;
  }
  /**
   * @return int
   */
  public function getColumnIndex()
  {
    return $this->columnIndex;
  }
  /**
   * The column name.
   *
   * @param string $columnName
   */
  public function setColumnName($columnName)
  {
    $this->columnName = $columnName;
  }
  /**
   * @return string
   */
  public function getColumnName()
  {
    return $this->columnName;
  }
  /**
   * The column type.
   *
   * Accepted values: COLUMN_TYPE_UNSPECIFIED, DOUBLE, CURRENCY, PERCENT, DATE,
   * TIME, DATE_TIME, TEXT, BOOLEAN, DROPDOWN, FILES_CHIP, PEOPLE_CHIP,
   * FINANCE_CHIP, PLACE_CHIP, RATINGS_CHIP
   *
   * @param self::COLUMN_TYPE_* $columnType
   */
  public function setColumnType($columnType)
  {
    $this->columnType = $columnType;
  }
  /**
   * @return self::COLUMN_TYPE_*
   */
  public function getColumnType()
  {
    return $this->columnType;
  }
  /**
   * The column data validation rule. Only set for dropdown column type.
   *
   * @param TableColumnDataValidationRule $dataValidationRule
   */
  public function setDataValidationRule(TableColumnDataValidationRule $dataValidationRule)
  {
    $this->dataValidationRule = $dataValidationRule;
  }
  /**
   * @return TableColumnDataValidationRule
   */
  public function getDataValidationRule()
  {
    return $this->dataValidationRule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableColumnProperties::class, 'Google_Service_Sheets_TableColumnProperties');
