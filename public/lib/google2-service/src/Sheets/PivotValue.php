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

class PivotValue extends \Google\Model
{
  /**
   * Default value, do not use.
   */
  public const CALCULATED_DISPLAY_TYPE_PIVOT_VALUE_CALCULATED_DISPLAY_TYPE_UNSPECIFIED = 'PIVOT_VALUE_CALCULATED_DISPLAY_TYPE_UNSPECIFIED';
  /**
   * Shows the pivot values as percentage of the row total values.
   */
  public const CALCULATED_DISPLAY_TYPE_PERCENT_OF_ROW_TOTAL = 'PERCENT_OF_ROW_TOTAL';
  /**
   * Shows the pivot values as percentage of the column total values.
   */
  public const CALCULATED_DISPLAY_TYPE_PERCENT_OF_COLUMN_TOTAL = 'PERCENT_OF_COLUMN_TOTAL';
  /**
   * Shows the pivot values as percentage of the grand total values.
   */
  public const CALCULATED_DISPLAY_TYPE_PERCENT_OF_GRAND_TOTAL = 'PERCENT_OF_GRAND_TOTAL';
  /**
   * The default, do not use.
   */
  public const SUMMARIZE_FUNCTION_PIVOT_STANDARD_VALUE_FUNCTION_UNSPECIFIED = 'PIVOT_STANDARD_VALUE_FUNCTION_UNSPECIFIED';
  /**
   * Corresponds to the `SUM` function.
   */
  public const SUMMARIZE_FUNCTION_SUM = 'SUM';
  /**
   * Corresponds to the `COUNTA` function.
   */
  public const SUMMARIZE_FUNCTION_COUNTA = 'COUNTA';
  /**
   * Corresponds to the `COUNT` function.
   */
  public const SUMMARIZE_FUNCTION_COUNT = 'COUNT';
  /**
   * Corresponds to the `COUNTUNIQUE` function.
   */
  public const SUMMARIZE_FUNCTION_COUNTUNIQUE = 'COUNTUNIQUE';
  /**
   * Corresponds to the `AVERAGE` function.
   */
  public const SUMMARIZE_FUNCTION_AVERAGE = 'AVERAGE';
  /**
   * Corresponds to the `MAX` function.
   */
  public const SUMMARIZE_FUNCTION_MAX = 'MAX';
  /**
   * Corresponds to the `MIN` function.
   */
  public const SUMMARIZE_FUNCTION_MIN = 'MIN';
  /**
   * Corresponds to the `MEDIAN` function.
   */
  public const SUMMARIZE_FUNCTION_MEDIAN = 'MEDIAN';
  /**
   * Corresponds to the `PRODUCT` function.
   */
  public const SUMMARIZE_FUNCTION_PRODUCT = 'PRODUCT';
  /**
   * Corresponds to the `STDEV` function.
   */
  public const SUMMARIZE_FUNCTION_STDEV = 'STDEV';
  /**
   * Corresponds to the `STDEVP` function.
   */
  public const SUMMARIZE_FUNCTION_STDEVP = 'STDEVP';
  /**
   * Corresponds to the `VAR` function.
   */
  public const SUMMARIZE_FUNCTION_VAR = 'VAR';
  /**
   * Corresponds to the `VARP` function.
   */
  public const SUMMARIZE_FUNCTION_VARP = 'VARP';
  /**
   * Indicates the formula should be used as-is. Only valid if
   * PivotValue.formula was set.
   */
  public const SUMMARIZE_FUNCTION_CUSTOM = 'CUSTOM';
  /**
   * Indicates that the value is already summarized, and the summarization
   * function is not explicitly specified. Used for Looker data source pivot
   * tables where the value is already summarized.
   */
  public const SUMMARIZE_FUNCTION_NONE = 'NONE';
  /**
   * If specified, indicates that pivot values should be displayed as the result
   * of a calculation with another pivot value. For example, if
   * calculated_display_type is specified as PERCENT_OF_GRAND_TOTAL, all the
   * pivot values are displayed as the percentage of the grand total. In the
   * Sheets editor, this is referred to as "Show As" in the value section of a
   * pivot table.
   *
   * @var string
   */
  public $calculatedDisplayType;
  protected $dataSourceColumnReferenceType = DataSourceColumnReference::class;
  protected $dataSourceColumnReferenceDataType = '';
  /**
   * A custom formula to calculate the value. The formula must start with an `=`
   * character.
   *
   * @var string
   */
  public $formula;
  /**
   * A name to use for the value.
   *
   * @var string
   */
  public $name;
  /**
   * The column offset of the source range that this value reads from. For
   * example, if the source was `C10:E15`, a `sourceColumnOffset` of `0` means
   * this value refers to column `C`, whereas the offset `1` would refer to
   * column `D`.
   *
   * @var int
   */
  public $sourceColumnOffset;
  /**
   * A function to summarize the value. If formula is set, the only supported
   * values are SUM and CUSTOM. If sourceColumnOffset is set, then `CUSTOM` is
   * not supported.
   *
   * @var string
   */
  public $summarizeFunction;

  /**
   * If specified, indicates that pivot values should be displayed as the result
   * of a calculation with another pivot value. For example, if
   * calculated_display_type is specified as PERCENT_OF_GRAND_TOTAL, all the
   * pivot values are displayed as the percentage of the grand total. In the
   * Sheets editor, this is referred to as "Show As" in the value section of a
   * pivot table.
   *
   * Accepted values: PIVOT_VALUE_CALCULATED_DISPLAY_TYPE_UNSPECIFIED,
   * PERCENT_OF_ROW_TOTAL, PERCENT_OF_COLUMN_TOTAL, PERCENT_OF_GRAND_TOTAL
   *
   * @param self::CALCULATED_DISPLAY_TYPE_* $calculatedDisplayType
   */
  public function setCalculatedDisplayType($calculatedDisplayType)
  {
    $this->calculatedDisplayType = $calculatedDisplayType;
  }
  /**
   * @return self::CALCULATED_DISPLAY_TYPE_*
   */
  public function getCalculatedDisplayType()
  {
    return $this->calculatedDisplayType;
  }
  /**
   * The reference to the data source column that this value reads from.
   *
   * @param DataSourceColumnReference $dataSourceColumnReference
   */
  public function setDataSourceColumnReference(DataSourceColumnReference $dataSourceColumnReference)
  {
    $this->dataSourceColumnReference = $dataSourceColumnReference;
  }
  /**
   * @return DataSourceColumnReference
   */
  public function getDataSourceColumnReference()
  {
    return $this->dataSourceColumnReference;
  }
  /**
   * A custom formula to calculate the value. The formula must start with an `=`
   * character.
   *
   * @param string $formula
   */
  public function setFormula($formula)
  {
    $this->formula = $formula;
  }
  /**
   * @return string
   */
  public function getFormula()
  {
    return $this->formula;
  }
  /**
   * A name to use for the value.
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
   * The column offset of the source range that this value reads from. For
   * example, if the source was `C10:E15`, a `sourceColumnOffset` of `0` means
   * this value refers to column `C`, whereas the offset `1` would refer to
   * column `D`.
   *
   * @param int $sourceColumnOffset
   */
  public function setSourceColumnOffset($sourceColumnOffset)
  {
    $this->sourceColumnOffset = $sourceColumnOffset;
  }
  /**
   * @return int
   */
  public function getSourceColumnOffset()
  {
    return $this->sourceColumnOffset;
  }
  /**
   * A function to summarize the value. If formula is set, the only supported
   * values are SUM and CUSTOM. If sourceColumnOffset is set, then `CUSTOM` is
   * not supported.
   *
   * Accepted values: PIVOT_STANDARD_VALUE_FUNCTION_UNSPECIFIED, SUM, COUNTA,
   * COUNT, COUNTUNIQUE, AVERAGE, MAX, MIN, MEDIAN, PRODUCT, STDEV, STDEVP, VAR,
   * VARP, CUSTOM, NONE
   *
   * @param self::SUMMARIZE_FUNCTION_* $summarizeFunction
   */
  public function setSummarizeFunction($summarizeFunction)
  {
    $this->summarizeFunction = $summarizeFunction;
  }
  /**
   * @return self::SUMMARIZE_FUNCTION_*
   */
  public function getSummarizeFunction()
  {
    return $this->summarizeFunction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PivotValue::class, 'Google_Service_Sheets_PivotValue');
