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

namespace Google\Service\Logging;

class LoggingQuery extends \Google\Collection
{
  protected $collection_key = 'summaryFields';
  /**
   * Required. An advanced query using the Logging Query Language
   * (https://cloud.google.com/logging/docs/view/logging-query-language). The
   * maximum length of the filter is 20000 characters.
   *
   * @var string
   */
  public $filter;
  /**
   * Characters will be counted from the end of the string.
   *
   * @var int
   */
  public $summaryFieldEnd;
  /**
   * Characters will be counted from the start of the string.
   *
   * @var int
   */
  public $summaryFieldStart;
  protected $summaryFieldsType = SummaryField::class;
  protected $summaryFieldsDataType = 'array';

  /**
   * Required. An advanced query using the Logging Query Language
   * (https://cloud.google.com/logging/docs/view/logging-query-language). The
   * maximum length of the filter is 20000 characters.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Characters will be counted from the end of the string.
   *
   * @param int $summaryFieldEnd
   */
  public function setSummaryFieldEnd($summaryFieldEnd)
  {
    $this->summaryFieldEnd = $summaryFieldEnd;
  }
  /**
   * @return int
   */
  public function getSummaryFieldEnd()
  {
    return $this->summaryFieldEnd;
  }
  /**
   * Characters will be counted from the start of the string.
   *
   * @param int $summaryFieldStart
   */
  public function setSummaryFieldStart($summaryFieldStart)
  {
    $this->summaryFieldStart = $summaryFieldStart;
  }
  /**
   * @return int
   */
  public function getSummaryFieldStart()
  {
    return $this->summaryFieldStart;
  }
  /**
   * Optional. The set of summary fields to display for this saved query.
   *
   * @param SummaryField[] $summaryFields
   */
  public function setSummaryFields($summaryFields)
  {
    $this->summaryFields = $summaryFields;
  }
  /**
   * @return SummaryField[]
   */
  public function getSummaryFields()
  {
    return $this->summaryFields;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LoggingQuery::class, 'Google_Service_Logging_LoggingQuery');
