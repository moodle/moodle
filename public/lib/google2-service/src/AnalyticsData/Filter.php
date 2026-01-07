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

namespace Google\Service\AnalyticsData;

class Filter extends \Google\Model
{
  protected $betweenFilterType = BetweenFilter::class;
  protected $betweenFilterDataType = '';
  protected $emptyFilterType = EmptyFilter::class;
  protected $emptyFilterDataType = '';
  /**
   * The dimension name or metric name. In most methods, dimensions & metrics
   * can be used for the first time in this field. However in a
   * RunPivotReportRequest, this field must be additionally specified by name in
   * the RunPivotReportRequest's dimensions or metrics.
   *
   * @var string
   */
  public $fieldName;
  protected $inListFilterType = InListFilter::class;
  protected $inListFilterDataType = '';
  protected $numericFilterType = NumericFilter::class;
  protected $numericFilterDataType = '';
  protected $stringFilterType = StringFilter::class;
  protected $stringFilterDataType = '';

  /**
   * A filter for two values.
   *
   * @param BetweenFilter $betweenFilter
   */
  public function setBetweenFilter(BetweenFilter $betweenFilter)
  {
    $this->betweenFilter = $betweenFilter;
  }
  /**
   * @return BetweenFilter
   */
  public function getBetweenFilter()
  {
    return $this->betweenFilter;
  }
  /**
   * A filter for empty values such as "(not set)" and "" values.
   *
   * @param EmptyFilter $emptyFilter
   */
  public function setEmptyFilter(EmptyFilter $emptyFilter)
  {
    $this->emptyFilter = $emptyFilter;
  }
  /**
   * @return EmptyFilter
   */
  public function getEmptyFilter()
  {
    return $this->emptyFilter;
  }
  /**
   * The dimension name or metric name. In most methods, dimensions & metrics
   * can be used for the first time in this field. However in a
   * RunPivotReportRequest, this field must be additionally specified by name in
   * the RunPivotReportRequest's dimensions or metrics.
   *
   * @param string $fieldName
   */
  public function setFieldName($fieldName)
  {
    $this->fieldName = $fieldName;
  }
  /**
   * @return string
   */
  public function getFieldName()
  {
    return $this->fieldName;
  }
  /**
   * A filter for in list values.
   *
   * @param InListFilter $inListFilter
   */
  public function setInListFilter(InListFilter $inListFilter)
  {
    $this->inListFilter = $inListFilter;
  }
  /**
   * @return InListFilter
   */
  public function getInListFilter()
  {
    return $this->inListFilter;
  }
  /**
   * A filter for numeric or date values.
   *
   * @param NumericFilter $numericFilter
   */
  public function setNumericFilter(NumericFilter $numericFilter)
  {
    $this->numericFilter = $numericFilter;
  }
  /**
   * @return NumericFilter
   */
  public function getNumericFilter()
  {
    return $this->numericFilter;
  }
  /**
   * Strings related filter.
   *
   * @param StringFilter $stringFilter
   */
  public function setStringFilter(StringFilter $stringFilter)
  {
    $this->stringFilter = $stringFilter;
  }
  /**
   * @return StringFilter
   */
  public function getStringFilter()
  {
    return $this->stringFilter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Filter::class, 'Google_Service_AnalyticsData_Filter');
