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

class AmpInspectionResult extends \Google\Collection
{
  /**
   * Unknown verdict.
   */
  public const AMP_INDEX_STATUS_VERDICT_VERDICT_UNSPECIFIED = 'VERDICT_UNSPECIFIED';
  /**
   * Equivalent to "Valid" for the page or item in Search Console.
   */
  public const AMP_INDEX_STATUS_VERDICT_PASS = 'PASS';
  /**
   * Reserved, no longer in use.
   */
  public const AMP_INDEX_STATUS_VERDICT_PARTIAL = 'PARTIAL';
  /**
   * Equivalent to "Error" or "Invalid" for the page or item in Search Console.
   */
  public const AMP_INDEX_STATUS_VERDICT_FAIL = 'FAIL';
  /**
   * Equivalent to "Excluded" for the page or item in Search Console.
   */
  public const AMP_INDEX_STATUS_VERDICT_NEUTRAL = 'NEUTRAL';
  /**
   * Unknown indexing status.
   */
  public const INDEXING_STATE_AMP_INDEXING_STATE_UNSPECIFIED = 'AMP_INDEXING_STATE_UNSPECIFIED';
  /**
   * Indexing allowed.
   */
  public const INDEXING_STATE_AMP_INDEXING_ALLOWED = 'AMP_INDEXING_ALLOWED';
  /**
   * Indexing not allowed, 'noindex' detected.
   */
  public const INDEXING_STATE_BLOCKED_DUE_TO_NOINDEX = 'BLOCKED_DUE_TO_NOINDEX';
  /**
   * Indexing not allowed, 'unavailable_after' date expired.
   */
  public const INDEXING_STATE_BLOCKED_DUE_TO_EXPIRED_UNAVAILABLE_AFTER = 'BLOCKED_DUE_TO_EXPIRED_UNAVAILABLE_AFTER';
  /**
   * Unknown fetch state.
   */
  public const PAGE_FETCH_STATE_PAGE_FETCH_STATE_UNSPECIFIED = 'PAGE_FETCH_STATE_UNSPECIFIED';
  /**
   * Successful fetch.
   */
  public const PAGE_FETCH_STATE_SUCCESSFUL = 'SUCCESSFUL';
  /**
   * Soft 404.
   */
  public const PAGE_FETCH_STATE_SOFT_404 = 'SOFT_404';
  /**
   * Blocked by robots.txt.
   */
  public const PAGE_FETCH_STATE_BLOCKED_ROBOTS_TXT = 'BLOCKED_ROBOTS_TXT';
  /**
   * Not found (404).
   */
  public const PAGE_FETCH_STATE_NOT_FOUND = 'NOT_FOUND';
  /**
   * Blocked due to unauthorized request (401).
   */
  public const PAGE_FETCH_STATE_ACCESS_DENIED = 'ACCESS_DENIED';
  /**
   * Server error (5xx).
   */
  public const PAGE_FETCH_STATE_SERVER_ERROR = 'SERVER_ERROR';
  /**
   * Redirection error.
   */
  public const PAGE_FETCH_STATE_REDIRECT_ERROR = 'REDIRECT_ERROR';
  /**
   * Blocked due to access forbidden (403).
   */
  public const PAGE_FETCH_STATE_ACCESS_FORBIDDEN = 'ACCESS_FORBIDDEN';
  /**
   * Blocked due to other 4xx issue (not 403, 404).
   */
  public const PAGE_FETCH_STATE_BLOCKED_4XX = 'BLOCKED_4XX';
  /**
   * Internal error.
   */
  public const PAGE_FETCH_STATE_INTERNAL_CRAWL_ERROR = 'INTERNAL_CRAWL_ERROR';
  /**
   * Invalid URL.
   */
  public const PAGE_FETCH_STATE_INVALID_URL = 'INVALID_URL';
  /**
   * Unknown robots.txt state, typically because the page wasn't fetched or
   * found, or because robots.txt itself couldn't be reached.
   */
  public const ROBOTS_TXT_STATE_ROBOTS_TXT_STATE_UNSPECIFIED = 'ROBOTS_TXT_STATE_UNSPECIFIED';
  /**
   * Crawl allowed by robots.txt.
   */
  public const ROBOTS_TXT_STATE_ALLOWED = 'ALLOWED';
  /**
   * Crawl blocked by robots.txt.
   */
  public const ROBOTS_TXT_STATE_DISALLOWED = 'DISALLOWED';
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
  /**
   * Index status of the AMP URL.
   *
   * @var string
   */
  public $ampIndexStatusVerdict;
  /**
   * URL of the AMP that was inspected. If the submitted URL is a desktop page
   * that refers to an AMP version, the AMP version will be inspected.
   *
   * @var string
   */
  public $ampUrl;
  /**
   * Whether or not the page blocks indexing through a noindex rule.
   *
   * @var string
   */
  public $indexingState;
  protected $issuesType = AmpIssue::class;
  protected $issuesDataType = 'array';
  /**
   * Last time this AMP version was crawled by Google. Absent if the URL was
   * never crawled successfully.
   *
   * @var string
   */
  public $lastCrawlTime;
  /**
   * Whether or not Google could fetch the AMP.
   *
   * @var string
   */
  public $pageFetchState;
  /**
   * Whether or not the page is blocked to Google by a robots.txt rule.
   *
   * @var string
   */
  public $robotsTxtState;
  /**
   * The status of the most severe error on the page. If a page has both
   * warnings and errors, the page status is error. Error status means the page
   * cannot be shown in Search results.
   *
   * @var string
   */
  public $verdict;

  /**
   * Index status of the AMP URL.
   *
   * Accepted values: VERDICT_UNSPECIFIED, PASS, PARTIAL, FAIL, NEUTRAL
   *
   * @param self::AMP_INDEX_STATUS_VERDICT_* $ampIndexStatusVerdict
   */
  public function setAmpIndexStatusVerdict($ampIndexStatusVerdict)
  {
    $this->ampIndexStatusVerdict = $ampIndexStatusVerdict;
  }
  /**
   * @return self::AMP_INDEX_STATUS_VERDICT_*
   */
  public function getAmpIndexStatusVerdict()
  {
    return $this->ampIndexStatusVerdict;
  }
  /**
   * URL of the AMP that was inspected. If the submitted URL is a desktop page
   * that refers to an AMP version, the AMP version will be inspected.
   *
   * @param string $ampUrl
   */
  public function setAmpUrl($ampUrl)
  {
    $this->ampUrl = $ampUrl;
  }
  /**
   * @return string
   */
  public function getAmpUrl()
  {
    return $this->ampUrl;
  }
  /**
   * Whether or not the page blocks indexing through a noindex rule.
   *
   * Accepted values: AMP_INDEXING_STATE_UNSPECIFIED, AMP_INDEXING_ALLOWED,
   * BLOCKED_DUE_TO_NOINDEX, BLOCKED_DUE_TO_EXPIRED_UNAVAILABLE_AFTER
   *
   * @param self::INDEXING_STATE_* $indexingState
   */
  public function setIndexingState($indexingState)
  {
    $this->indexingState = $indexingState;
  }
  /**
   * @return self::INDEXING_STATE_*
   */
  public function getIndexingState()
  {
    return $this->indexingState;
  }
  /**
   * A list of zero or more AMP issues found for the inspected URL.
   *
   * @param AmpIssue[] $issues
   */
  public function setIssues($issues)
  {
    $this->issues = $issues;
  }
  /**
   * @return AmpIssue[]
   */
  public function getIssues()
  {
    return $this->issues;
  }
  /**
   * Last time this AMP version was crawled by Google. Absent if the URL was
   * never crawled successfully.
   *
   * @param string $lastCrawlTime
   */
  public function setLastCrawlTime($lastCrawlTime)
  {
    $this->lastCrawlTime = $lastCrawlTime;
  }
  /**
   * @return string
   */
  public function getLastCrawlTime()
  {
    return $this->lastCrawlTime;
  }
  /**
   * Whether or not Google could fetch the AMP.
   *
   * Accepted values: PAGE_FETCH_STATE_UNSPECIFIED, SUCCESSFUL, SOFT_404,
   * BLOCKED_ROBOTS_TXT, NOT_FOUND, ACCESS_DENIED, SERVER_ERROR, REDIRECT_ERROR,
   * ACCESS_FORBIDDEN, BLOCKED_4XX, INTERNAL_CRAWL_ERROR, INVALID_URL
   *
   * @param self::PAGE_FETCH_STATE_* $pageFetchState
   */
  public function setPageFetchState($pageFetchState)
  {
    $this->pageFetchState = $pageFetchState;
  }
  /**
   * @return self::PAGE_FETCH_STATE_*
   */
  public function getPageFetchState()
  {
    return $this->pageFetchState;
  }
  /**
   * Whether or not the page is blocked to Google by a robots.txt rule.
   *
   * Accepted values: ROBOTS_TXT_STATE_UNSPECIFIED, ALLOWED, DISALLOWED
   *
   * @param self::ROBOTS_TXT_STATE_* $robotsTxtState
   */
  public function setRobotsTxtState($robotsTxtState)
  {
    $this->robotsTxtState = $robotsTxtState;
  }
  /**
   * @return self::ROBOTS_TXT_STATE_*
   */
  public function getRobotsTxtState()
  {
    return $this->robotsTxtState;
  }
  /**
   * The status of the most severe error on the page. If a page has both
   * warnings and errors, the page status is error. Error status means the page
   * cannot be shown in Search results.
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
class_alias(AmpInspectionResult::class, 'Google_Service_SearchConsole_AmpInspectionResult');
