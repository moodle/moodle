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

class PivotGroup extends \Google\Collection
{
  /**
   * Default value, do not use this.
   */
  public const SORT_ORDER_SORT_ORDER_UNSPECIFIED = 'SORT_ORDER_UNSPECIFIED';
  /**
   * Sort ascending.
   */
  public const SORT_ORDER_ASCENDING = 'ASCENDING';
  /**
   * Sort descending.
   */
  public const SORT_ORDER_DESCENDING = 'DESCENDING';
  protected $collection_key = 'valueMetadata';
  protected $dataSourceColumnReferenceType = DataSourceColumnReference::class;
  protected $dataSourceColumnReferenceDataType = '';
  protected $groupLimitType = PivotGroupLimit::class;
  protected $groupLimitDataType = '';
  protected $groupRuleType = PivotGroupRule::class;
  protected $groupRuleDataType = '';
  /**
   * The labels to use for the row/column groups which can be customized. For
   * example, in the following pivot table, the row label is `Region` (which
   * could be renamed to `State`) and the column label is `Product` (which could
   * be renamed `Item`). Pivot tables created before December 2017 do not have
   * header labels. If you'd like to add header labels to an existing pivot
   * table, please delete the existing pivot table and then create a new pivot
   * table with same parameters. +--------------+---------+-------+ | SUM of
   * Units | Product | | | Region | Pen | Paper |
   * +--------------+---------+-------+ | New York | 345 | 98 | | Oregon | 234 |
   * 123 | | Tennessee | 531 | 415 | +--------------+---------+-------+ | Grand
   * Total | 1110 | 636 | +--------------+---------+-------+
   *
   * @var string
   */
  public $label;
  /**
   * True if the headings in this pivot group should be repeated. This is only
   * valid for row groupings and is ignored by columns. By default, we minimize
   * repetition of headings by not showing higher level headings where they are
   * the same. For example, even though the third row below corresponds to "Q1
   * Mar", "Q1" is not shown because it is redundant with previous rows. Setting
   * repeat_headings to true would cause "Q1" to be repeated for "Feb" and
   * "Mar". +--------------+ | Q1 | Jan | | | Feb | | | Mar | +--------+-----+ |
   * Q1 Total | +--------------+
   *
   * @var bool
   */
  public $repeatHeadings;
  /**
   * True if the pivot table should include the totals for this grouping.
   *
   * @var bool
   */
  public $showTotals;
  /**
   * The order the values in this group should be sorted.
   *
   * @var string
   */
  public $sortOrder;
  /**
   * The column offset of the source range that this grouping is based on. For
   * example, if the source was `C10:E15`, a `sourceColumnOffset` of `0` means
   * this group refers to column `C`, whereas the offset `1` would refer to
   * column `D`.
   *
   * @var int
   */
  public $sourceColumnOffset;
  protected $valueBucketType = PivotGroupSortValueBucket::class;
  protected $valueBucketDataType = '';
  protected $valueMetadataType = PivotGroupValueMetadata::class;
  protected $valueMetadataDataType = 'array';

  /**
   * The reference to the data source column this grouping is based on.
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
   * The count limit on rows or columns to apply to this pivot group.
   *
   * @param PivotGroupLimit $groupLimit
   */
  public function setGroupLimit(PivotGroupLimit $groupLimit)
  {
    $this->groupLimit = $groupLimit;
  }
  /**
   * @return PivotGroupLimit
   */
  public function getGroupLimit()
  {
    return $this->groupLimit;
  }
  /**
   * The group rule to apply to this row/column group.
   *
   * @param PivotGroupRule $groupRule
   */
  public function setGroupRule(PivotGroupRule $groupRule)
  {
    $this->groupRule = $groupRule;
  }
  /**
   * @return PivotGroupRule
   */
  public function getGroupRule()
  {
    return $this->groupRule;
  }
  /**
   * The labels to use for the row/column groups which can be customized. For
   * example, in the following pivot table, the row label is `Region` (which
   * could be renamed to `State`) and the column label is `Product` (which could
   * be renamed `Item`). Pivot tables created before December 2017 do not have
   * header labels. If you'd like to add header labels to an existing pivot
   * table, please delete the existing pivot table and then create a new pivot
   * table with same parameters. +--------------+---------+-------+ | SUM of
   * Units | Product | | | Region | Pen | Paper |
   * +--------------+---------+-------+ | New York | 345 | 98 | | Oregon | 234 |
   * 123 | | Tennessee | 531 | 415 | +--------------+---------+-------+ | Grand
   * Total | 1110 | 636 | +--------------+---------+-------+
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * True if the headings in this pivot group should be repeated. This is only
   * valid for row groupings and is ignored by columns. By default, we minimize
   * repetition of headings by not showing higher level headings where they are
   * the same. For example, even though the third row below corresponds to "Q1
   * Mar", "Q1" is not shown because it is redundant with previous rows. Setting
   * repeat_headings to true would cause "Q1" to be repeated for "Feb" and
   * "Mar". +--------------+ | Q1 | Jan | | | Feb | | | Mar | +--------+-----+ |
   * Q1 Total | +--------------+
   *
   * @param bool $repeatHeadings
   */
  public function setRepeatHeadings($repeatHeadings)
  {
    $this->repeatHeadings = $repeatHeadings;
  }
  /**
   * @return bool
   */
  public function getRepeatHeadings()
  {
    return $this->repeatHeadings;
  }
  /**
   * True if the pivot table should include the totals for this grouping.
   *
   * @param bool $showTotals
   */
  public function setShowTotals($showTotals)
  {
    $this->showTotals = $showTotals;
  }
  /**
   * @return bool
   */
  public function getShowTotals()
  {
    return $this->showTotals;
  }
  /**
   * The order the values in this group should be sorted.
   *
   * Accepted values: SORT_ORDER_UNSPECIFIED, ASCENDING, DESCENDING
   *
   * @param self::SORT_ORDER_* $sortOrder
   */
  public function setSortOrder($sortOrder)
  {
    $this->sortOrder = $sortOrder;
  }
  /**
   * @return self::SORT_ORDER_*
   */
  public function getSortOrder()
  {
    return $this->sortOrder;
  }
  /**
   * The column offset of the source range that this grouping is based on. For
   * example, if the source was `C10:E15`, a `sourceColumnOffset` of `0` means
   * this group refers to column `C`, whereas the offset `1` would refer to
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
   * The bucket of the opposite pivot group to sort by. If not specified,
   * sorting is alphabetical by this group's values.
   *
   * @param PivotGroupSortValueBucket $valueBucket
   */
  public function setValueBucket(PivotGroupSortValueBucket $valueBucket)
  {
    $this->valueBucket = $valueBucket;
  }
  /**
   * @return PivotGroupSortValueBucket
   */
  public function getValueBucket()
  {
    return $this->valueBucket;
  }
  /**
   * Metadata about values in the grouping.
   *
   * @param PivotGroupValueMetadata[] $valueMetadata
   */
  public function setValueMetadata($valueMetadata)
  {
    $this->valueMetadata = $valueMetadata;
  }
  /**
   * @return PivotGroupValueMetadata[]
   */
  public function getValueMetadata()
  {
    return $this->valueMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PivotGroup::class, 'Google_Service_Sheets_PivotGroup');
