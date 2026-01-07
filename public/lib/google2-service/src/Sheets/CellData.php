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

class CellData extends \Google\Collection
{
  protected $collection_key = 'textFormatRuns';
  protected $chipRunsType = ChipRun::class;
  protected $chipRunsDataType = 'array';
  protected $dataSourceFormulaType = DataSourceFormula::class;
  protected $dataSourceFormulaDataType = '';
  protected $dataSourceTableType = DataSourceTable::class;
  protected $dataSourceTableDataType = '';
  protected $dataValidationType = DataValidationRule::class;
  protected $dataValidationDataType = '';
  protected $effectiveFormatType = CellFormat::class;
  protected $effectiveFormatDataType = '';
  protected $effectiveValueType = ExtendedValue::class;
  protected $effectiveValueDataType = '';
  /**
   * The formatted value of the cell. This is the value as it's shown to the
   * user. This field is read-only.
   *
   * @var string
   */
  public $formattedValue;
  /**
   * A hyperlink this cell points to, if any. If the cell contains multiple
   * hyperlinks, this field will be empty. This field is read-only. To set it,
   * use a `=HYPERLINK` formula in the userEnteredValue.formulaValue field. A
   * cell-level link can also be set from the userEnteredFormat.textFormat
   * field. Alternatively, set a hyperlink in the textFormatRun.format.link
   * field that spans the entire cell.
   *
   * @var string
   */
  public $hyperlink;
  /**
   * Any note on the cell.
   *
   * @var string
   */
  public $note;
  protected $pivotTableType = PivotTable::class;
  protected $pivotTableDataType = '';
  protected $textFormatRunsType = TextFormatRun::class;
  protected $textFormatRunsDataType = 'array';
  protected $userEnteredFormatType = CellFormat::class;
  protected $userEnteredFormatDataType = '';
  protected $userEnteredValueType = ExtendedValue::class;
  protected $userEnteredValueDataType = '';

  /**
   * Optional. Runs of chips applied to subsections of the cell. Properties of a
   * run start at a specific index in the text and continue until the next run.
   * When reading, all chipped and non-chipped runs are included. Non-chipped
   * runs will have an empty Chip. When writing, only runs with chips are
   * included. Runs containing chips are of length 1 and are represented in the
   * user-entered text by an “@” placeholder symbol. New runs will overwrite any
   * prior runs. Writing a new user_entered_value will erase previous runs.
   *
   * @param ChipRun[] $chipRuns
   */
  public function setChipRuns($chipRuns)
  {
    $this->chipRuns = $chipRuns;
  }
  /**
   * @return ChipRun[]
   */
  public function getChipRuns()
  {
    return $this->chipRuns;
  }
  /**
   * Output only. Information about a data source formula on the cell. The field
   * is set if user_entered_value is a formula referencing some DATA_SOURCE
   * sheet, e.g. `=SUM(DataSheet!Column)`.
   *
   * @param DataSourceFormula $dataSourceFormula
   */
  public function setDataSourceFormula(DataSourceFormula $dataSourceFormula)
  {
    $this->dataSourceFormula = $dataSourceFormula;
  }
  /**
   * @return DataSourceFormula
   */
  public function getDataSourceFormula()
  {
    return $this->dataSourceFormula;
  }
  /**
   * A data source table anchored at this cell. The size of data source table
   * itself is computed dynamically based on its configuration. Only the first
   * cell of the data source table contains the data source table definition.
   * The other cells will contain the display values of the data source table
   * result in their effective_value fields.
   *
   * @param DataSourceTable $dataSourceTable
   */
  public function setDataSourceTable(DataSourceTable $dataSourceTable)
  {
    $this->dataSourceTable = $dataSourceTable;
  }
  /**
   * @return DataSourceTable
   */
  public function getDataSourceTable()
  {
    return $this->dataSourceTable;
  }
  /**
   * A data validation rule on the cell, if any. When writing, the new data
   * validation rule will overwrite any prior rule.
   *
   * @param DataValidationRule $dataValidation
   */
  public function setDataValidation(DataValidationRule $dataValidation)
  {
    $this->dataValidation = $dataValidation;
  }
  /**
   * @return DataValidationRule
   */
  public function getDataValidation()
  {
    return $this->dataValidation;
  }
  /**
   * The effective format being used by the cell. This includes the results of
   * applying any conditional formatting and, if the cell contains a formula,
   * the computed number format. If the effective format is the default format,
   * effective format will not be written. This field is read-only.
   *
   * @param CellFormat $effectiveFormat
   */
  public function setEffectiveFormat(CellFormat $effectiveFormat)
  {
    $this->effectiveFormat = $effectiveFormat;
  }
  /**
   * @return CellFormat
   */
  public function getEffectiveFormat()
  {
    return $this->effectiveFormat;
  }
  /**
   * The effective value of the cell. For cells with formulas, this is the
   * calculated value. For cells with literals, this is the same as the
   * user_entered_value. This field is read-only.
   *
   * @param ExtendedValue $effectiveValue
   */
  public function setEffectiveValue(ExtendedValue $effectiveValue)
  {
    $this->effectiveValue = $effectiveValue;
  }
  /**
   * @return ExtendedValue
   */
  public function getEffectiveValue()
  {
    return $this->effectiveValue;
  }
  /**
   * The formatted value of the cell. This is the value as it's shown to the
   * user. This field is read-only.
   *
   * @param string $formattedValue
   */
  public function setFormattedValue($formattedValue)
  {
    $this->formattedValue = $formattedValue;
  }
  /**
   * @return string
   */
  public function getFormattedValue()
  {
    return $this->formattedValue;
  }
  /**
   * A hyperlink this cell points to, if any. If the cell contains multiple
   * hyperlinks, this field will be empty. This field is read-only. To set it,
   * use a `=HYPERLINK` formula in the userEnteredValue.formulaValue field. A
   * cell-level link can also be set from the userEnteredFormat.textFormat
   * field. Alternatively, set a hyperlink in the textFormatRun.format.link
   * field that spans the entire cell.
   *
   * @param string $hyperlink
   */
  public function setHyperlink($hyperlink)
  {
    $this->hyperlink = $hyperlink;
  }
  /**
   * @return string
   */
  public function getHyperlink()
  {
    return $this->hyperlink;
  }
  /**
   * Any note on the cell.
   *
   * @param string $note
   */
  public function setNote($note)
  {
    $this->note = $note;
  }
  /**
   * @return string
   */
  public function getNote()
  {
    return $this->note;
  }
  /**
   * A pivot table anchored at this cell. The size of pivot table itself is
   * computed dynamically based on its data, grouping, filters, values, etc.
   * Only the top-left cell of the pivot table contains the pivot table
   * definition. The other cells will contain the calculated values of the
   * results of the pivot in their effective_value fields.
   *
   * @param PivotTable $pivotTable
   */
  public function setPivotTable(PivotTable $pivotTable)
  {
    $this->pivotTable = $pivotTable;
  }
  /**
   * @return PivotTable
   */
  public function getPivotTable()
  {
    return $this->pivotTable;
  }
  /**
   * Runs of rich text applied to subsections of the cell. Runs are only valid
   * on user entered strings, not formulas, bools, or numbers. Properties of a
   * run start at a specific index in the text and continue until the next run.
   * Runs will inherit the properties of the cell unless explicitly changed.
   * When writing, the new runs will overwrite any prior runs. When writing a
   * new user_entered_value, previous runs are erased.
   *
   * @param TextFormatRun[] $textFormatRuns
   */
  public function setTextFormatRuns($textFormatRuns)
  {
    $this->textFormatRuns = $textFormatRuns;
  }
  /**
   * @return TextFormatRun[]
   */
  public function getTextFormatRuns()
  {
    return $this->textFormatRuns;
  }
  /**
   * The format the user entered for the cell. When writing, the new format will
   * be merged with the existing format.
   *
   * @param CellFormat $userEnteredFormat
   */
  public function setUserEnteredFormat(CellFormat $userEnteredFormat)
  {
    $this->userEnteredFormat = $userEnteredFormat;
  }
  /**
   * @return CellFormat
   */
  public function getUserEnteredFormat()
  {
    return $this->userEnteredFormat;
  }
  /**
   * The value the user entered in the cell. e.g., `1234`, `'Hello'`, or
   * `=NOW()` Note: Dates, Times and DateTimes are represented as doubles in
   * serial number format.
   *
   * @param ExtendedValue $userEnteredValue
   */
  public function setUserEnteredValue(ExtendedValue $userEnteredValue)
  {
    $this->userEnteredValue = $userEnteredValue;
  }
  /**
   * @return ExtendedValue
   */
  public function getUserEnteredValue()
  {
    return $this->userEnteredValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CellData::class, 'Google_Service_Sheets_CellData');
