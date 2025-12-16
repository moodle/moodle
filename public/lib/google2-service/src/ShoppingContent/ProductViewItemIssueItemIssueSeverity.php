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

namespace Google\Service\ShoppingContent;

class ProductViewItemIssueItemIssueSeverity extends \Google\Collection
{
  /**
   * Undefined Issue severity.
   */
  public const AGGREGATED_SEVERITY_AGGREGATED_ISSUE_SEVERITY_UNSPECIFIED = 'AGGREGATED_ISSUE_SEVERITY_UNSPECIFIED';
  /**
   * Issue disapproves the product in at least one of the selected destinations.
   */
  public const AGGREGATED_SEVERITY_DISAPPROVED = 'DISAPPROVED';
  /**
   * Issue demotes the product in all selected destinations it affects.
   */
  public const AGGREGATED_SEVERITY_DEMOTED = 'DEMOTED';
  /**
   * Issue resolution is `PENDING_PROCESSING`.
   */
  public const AGGREGATED_SEVERITY_PENDING = 'PENDING';
  protected $collection_key = 'severityPerDestination';
  /**
   * Severity of an issue aggregated for destination.
   *
   * @var string
   */
  public $aggregatedSeverity;
  protected $severityPerDestinationType = ProductViewItemIssueIssueSeverityPerDestination::class;
  protected $severityPerDestinationDataType = 'array';

  /**
   * Severity of an issue aggregated for destination.
   *
   * Accepted values: AGGREGATED_ISSUE_SEVERITY_UNSPECIFIED, DISAPPROVED,
   * DEMOTED, PENDING
   *
   * @param self::AGGREGATED_SEVERITY_* $aggregatedSeverity
   */
  public function setAggregatedSeverity($aggregatedSeverity)
  {
    $this->aggregatedSeverity = $aggregatedSeverity;
  }
  /**
   * @return self::AGGREGATED_SEVERITY_*
   */
  public function getAggregatedSeverity()
  {
    return $this->aggregatedSeverity;
  }
  /**
   * Item issue severity for every destination.
   *
   * @param ProductViewItemIssueIssueSeverityPerDestination[] $severityPerDestination
   */
  public function setSeverityPerDestination($severityPerDestination)
  {
    $this->severityPerDestination = $severityPerDestination;
  }
  /**
   * @return ProductViewItemIssueIssueSeverityPerDestination[]
   */
  public function getSeverityPerDestination()
  {
    return $this->severityPerDestination;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductViewItemIssueItemIssueSeverity::class, 'Google_Service_ShoppingContent_ProductViewItemIssueItemIssueSeverity');
