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

namespace Google\Service\SearchConsole;

class MobileUsabilityInspectionResult extends \Google\Collection
{
  /**
   * Unknown verdict.
   */
  public const VERDICT_VERDICT_UNSPECIFIED = 'VERDICT_UNSPECIFIED';
  /**
   * Equivalent to "Valid" for the page or item in Search Console.
   */
  public const VERDICT_PASS = 'PASS';
  /**
   * Reserved, no longer in use.
   */
  public const VERDICT_PARTIAL = 'PARTIAL';
  /**
   * Equivalent to "Error" or "Invalid" for the page or item in Search Console.
   */
  public const VERDICT_FAIL = 'FAIL';
  /**
   * Equivalent to "Excluded" for the page or item in Search Console.
   */
  public const VERDICT_NEUTRAL = 'NEUTRAL';
  protected $collection_key = 'issues';
  protected $issuesType = MobileUsabilityIssue::class;
  protected $issuesDataType = 'array';
  /**
   * High-level mobile-usability inspection result for this URL.
   *
   * @var string
   */
  public $verdict;

  /**
   * A list of zero or more mobile-usability issues detected for this URL.
   *
   * @param MobileUsabilityIssue[] $issues
   */
  public function setIssues($issues)
  {
    $this->issues = $issues;
  }
  /**
   * @return MobileUsabilityIssue[]
   */
  public function getIssues()
  {
    return $this->issues;
  }
  /**
   * High-level mobile-usability inspection result for this URL.
   *
   * Accepted values: VERDICT_UNSPECIFIED, PASS, PARTIAL, FAIL, NEUTRAL
   *
   * @param self::VERDICT_* $verdict
   */
  public function setVerdict($verdict)
  {
    $this->verdict = $verdict;
  }
  /**
   * @return self::VERDICT_*
   */
  public function getVerdict()
  {
    return $this->verdict;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MobileUsabilityInspectionResult::class, 'Google_Service_SearchConsole_MobileUsabilityInspectionResult');
