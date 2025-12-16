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

class ProductIssueImpact extends \Google\Collection
{
  /**
   * Default value. Will never be provided by the API.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Causes either an account suspension or an item disapproval. Errors should
   * be resolved as soon as possible to ensure items are eligible to appear in
   * results again.
   */
  public const SEVERITY_ERROR = 'ERROR';
  /**
   * Warnings can negatively impact the performance of ads and can lead to item
   * or account suspensions in the future unless the issue is resolved.
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * Infos are suggested optimizations to increase data quality. Resolving these
   * issues is recommended, but not required.
   */
  public const SEVERITY_INFO = 'INFO';
  protected $collection_key = 'breakdowns';
  protected $breakdownsType = Breakdown::class;
  protected $breakdownsDataType = 'array';
  /**
   * Optional. Message summarizing the overall impact of the issue. If present,
   * it should be rendered to the merchant. For example: "Limits visibility in
   * France"
   *
   * @var string
   */
  public $message;
  /**
   * The severity of the issue.
   *
   * @var string
   */
  public $severity;

  /**
   * Detailed impact breakdown. Explains the types of restriction the issue has
   * in different shopping destinations and territory. If present, it should be
   * rendered to the merchant. Can be shown as a mouse over dropdown or a
   * dialog. Each breakdown item represents a group of regions with the same
   * impact details.
   *
   * @param Breakdown[] $breakdowns
   */
  public function setBreakdowns($breakdowns)
  {
    $this->breakdowns = $breakdowns;
  }
  /**
   * @return Breakdown[]
   */
  public function getBreakdowns()
  {
    return $this->breakdowns;
  }
  /**
   * Optional. Message summarizing the overall impact of the issue. If present,
   * it should be rendered to the merchant. For example: "Limits visibility in
   * France"
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * The severity of the issue.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, ERROR, WARNING, INFO
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductIssueImpact::class, 'Google_Service_ShoppingContent_ProductIssueImpact');
