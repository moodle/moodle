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

class ProductViewItemIssue extends \Google\Model
{
  /**
   * Unknown resolution type.
   */
  public const RESOLUTION_UNKNOWN = 'UNKNOWN';
  /**
   * The merchant has to fix the issue.
   */
  public const RESOLUTION_MERCHANT_ACTION = 'MERCHANT_ACTION';
  /**
   * The issue will be resolved automatically (for example, image crawl), or
   * Google review. No merchant action is required now. Resolution might lead to
   * another issue (for example, if crawl fails).
   */
  public const RESOLUTION_PENDING_PROCESSING = 'PENDING_PROCESSING';
  protected $issueTypeType = ProductViewItemIssueItemIssueType::class;
  protected $issueTypeDataType = '';
  /**
   * Item issue resolution.
   *
   * @var string
   */
  public $resolution;
  protected $severityType = ProductViewItemIssueItemIssueSeverity::class;
  protected $severityDataType = '';

  /**
   * Item issue type.
   *
   * @param ProductViewItemIssueItemIssueType $issueType
   */
  public function setIssueType(ProductViewItemIssueItemIssueType $issueType)
  {
    $this->issueType = $issueType;
  }
  /**
   * @return ProductViewItemIssueItemIssueType
   */
  public function getIssueType()
  {
    return $this->issueType;
  }
  /**
   * Item issue resolution.
   *
   * Accepted values: UNKNOWN, MERCHANT_ACTION, PENDING_PROCESSING
   *
   * @param self::RESOLUTION_* $resolution
   */
  public function setResolution($resolution)
  {
    $this->resolution = $resolution;
  }
  /**
   * @return self::RESOLUTION_*
   */
  public function getResolution()
  {
    return $this->resolution;
  }
  /**
   * Item issue severity.
   *
   * @param ProductViewItemIssueItemIssueSeverity $severity
   */
  public function setSeverity(ProductViewItemIssueItemIssueSeverity $severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return ProductViewItemIssueItemIssueSeverity
   */
  public function getSeverity()
  {
    return $this->severity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductViewItemIssue::class, 'Google_Service_ShoppingContent_ProductViewItemIssue');
