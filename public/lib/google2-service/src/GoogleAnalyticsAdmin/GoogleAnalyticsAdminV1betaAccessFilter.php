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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaAccessFilter extends \Google\Model
{
  protected $betweenFilterType = GoogleAnalyticsAdminV1betaAccessBetweenFilter::class;
  protected $betweenFilterDataType = '';
  /**
   * The dimension name or metric name.
   *
   * @var string
   */
  public $fieldName;
  protected $inListFilterType = GoogleAnalyticsAdminV1betaAccessInListFilter::class;
  protected $inListFilterDataType = '';
  protected $numericFilterType = GoogleAnalyticsAdminV1betaAccessNumericFilter::class;
  protected $numericFilterDataType = '';
  protected $stringFilterType = GoogleAnalyticsAdminV1betaAccessStringFilter::class;
  protected $stringFilterDataType = '';

  /**
   * A filter for two values.
   *
   * @param GoogleAnalyticsAdminV1betaAccessBetweenFilter $betweenFilter
   */
  public function setBetweenFilter(GoogleAnalyticsAdminV1betaAccessBetweenFilter $betweenFilter)
  {
    $this->betweenFilter = $betweenFilter;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessBetweenFilter
   */
  public function getBetweenFilter()
  {
    return $this->betweenFilter;
  }
  /**
   * The dimension name or metric name.
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
   * @param GoogleAnalyticsAdminV1betaAccessInListFilter $inListFilter
   */
  public function setInListFilter(GoogleAnalyticsAdminV1betaAccessInListFilter $inListFilter)
  {
    $this->inListFilter = $inListFilter;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessInListFilter
   */
  public function getInListFilter()
  {
    return $this->inListFilter;
  }
  /**
   * A filter for numeric or date values.
   *
   * @param GoogleAnalyticsAdminV1betaAccessNumericFilter $numericFilter
   */
  public function setNumericFilter(GoogleAnalyticsAdminV1betaAccessNumericFilter $numericFilter)
  {
    $this->numericFilter = $numericFilter;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessNumericFilter
   */
  public function getNumericFilter()
  {
    return $this->numericFilter;
  }
  /**
   * Strings related filter.
   *
   * @param GoogleAnalyticsAdminV1betaAccessStringFilter $stringFilter
   */
  public function setStringFilter(GoogleAnalyticsAdminV1betaAccessStringFilter $stringFilter)
  {
    $this->stringFilter = $stringFilter;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessStringFilter
   */
  public function getStringFilter()
  {
    return $this->stringFilter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaAccessFilter::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaAccessFilter');
