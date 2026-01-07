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

class RichResultsInspectionResult extends \Google\Collection
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
  protected $collection_key = 'detectedItems';
  protected $detectedItemsType = DetectedItems::class;
  protected $detectedItemsDataType = 'array';
  /**
   * High-level rich results inspection result for this URL.
   *
   * @var string
   */
  public $verdict;

  /**
   * A list of zero or more rich results detected on this page. Rich results
   * that cannot even be parsed due to syntactic issues will not be listed here.
   *
   * @param DetectedItems[] $detectedItems
   */
  public function setDetectedItems($detectedItems)
  {
    $this->detectedItems = $detectedItems;
  }
  /**
   * @return DetectedItems[]
   */
  public function getDetectedItems()
  {
    return $this->detectedItems;
  }
  /**
   * High-level rich results inspection result for this URL.
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
class_alias(RichResultsInspectionResult::class, 'Google_Service_SearchConsole_RichResultsInspectionResult');
