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

namespace Google\Service\Merchant;

class ItemIssueSeverity extends \Google\Collection
{
  protected $collection_key = 'severityPerReportingContext';
  /**
   * @var string
   */
  public $aggregatedSeverity;
  protected $severityPerReportingContextType = IssueSeverityPerReportingContext::class;
  protected $severityPerReportingContextDataType = 'array';

  /**
   * @param string
   */
  public function setAggregatedSeverity($aggregatedSeverity)
  {
    $this->aggregatedSeverity = $aggregatedSeverity;
  }
  /**
   * @return string
   */
  public function getAggregatedSeverity()
  {
    return $this->aggregatedSeverity;
  }
  /**
   * @param IssueSeverityPerReportingContext[]
   */
  public function setSeverityPerReportingContext($severityPerReportingContext)
  {
    $this->severityPerReportingContext = $severityPerReportingContext;
  }
  /**
   * @return IssueSeverityPerReportingContext[]
   */
  public function getSeverityPerReportingContext()
  {
    return $this->severityPerReportingContext;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ItemIssueSeverity::class, 'Google_Service_Merchant_ItemIssueSeverity');
