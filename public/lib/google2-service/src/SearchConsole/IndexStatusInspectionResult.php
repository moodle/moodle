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

class IndexStatusInspectionResult extends \Google\Collection
{
  /**
   * Unknown user agent.
   */
  public const CRAWLED_AS_CRAWLING_USER_AGENT_UNSPECIFIED = 'CRAWLING_USER_AGENT_UNSPECIFIED';
  /**
   * Desktop user agent.
   */
  public const CRAWLED_AS_DESKTOP = 'DESKTOP';
  /**
   * Mobile user agent.
   */
  public const CRAWLED_AS_MOBILE = 'MOBILE';
  /**
   * Unknown indexing status.
   */
  public const INDEXING_STATE_INDEXING_STATE_UNSPECIFIED = 'INDEXING_STATE_UNSPECIFIED';
  /**
   * Indexing allowed.
   */
  public const INDEXING_STATE_INDEXING_ALLOWED = 'INDEXING_ALLOWED';
  /**
   * Indexing not allowed, 'noindex' detected in 'robots' meta tag.
   */
  public const INDEXING_STATE_BLOCKED_BY_META_TAG = 'BLOCKED_BY_META_TAG';
  /**
   * Indexing not allowed, 'noindex' detected in 'X-Robots-Tag' http header.
   */
  public const INDEXING_STATE_BLOCKED_BY_HTTP_HEADER = 'BLOCKED_BY_HTTP_HEADER';
  /**
   * Reserved, no longer in use.
   */
  public const INDEXING_STATE_BLOCKED_BY_ROBOTS_TXT = 'BLOCKED_BY_ROBOTS_TXT';
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
  protected $collection_key = 'sitemap';
  /**
   * Could Google find and index the page. More details about page indexing
   * appear in 'indexing_state'.
   *
   * @var string
   */
  public $coverageState;
  /**
   * Primary crawler that was used by Google to crawl your site.
   *
   * @var string
   */
  public $crawledAs;
  /**
   * The URL of the page that Google selected as canonical. If the page was not
   * indexed, this field is absent.
   *
   * @var string
   */
  public $googleCanonical;
  /**
   * Whether or not the page blocks indexing through a noindex rule.
   *
   * @var string
   */
  public $indexingState;
  /**
   * Last time this URL was crawled by Google using the [primary crawler](https:
   * //support.google.com/webmasters/answer/7440203#primary_crawler). Absent if
   * the URL was never crawled successfully.
   *
   * @var string
   */
  public $lastCrawlTime;
  /**
   * Whether or not Google could retrieve the page from your server. Equivalent
   * to ["page fetch"](https://support.google.com/webmasters/answer/9012289#inde
   * x_coverage) in the URL inspection report.
   *
   * @var string
   */
  public $pageFetchState;
  /**
   * URLs that link to the inspected URL, directly and indirectly.
   *
   * @var string[]
   */
  public $referringUrls;
  /**
   * Whether or not the page is blocked to Google by a robots.txt rule.
   *
   * @var string
   */
  public $robotsTxtState;
  /**
   * Any sitemaps that this URL was listed in, as known by Google. Not
   * guaranteed to be an exhaustive list, especially if Google did not discover
   * this URL through a sitemap. Absent if no sitemaps were found.
   *
   * @var string[]
   */
  public $sitemap;
  /**
   * The URL that your page or site [declares as canonical](https://developers.g
   * oogle.com/search/docs/advanced/crawling/consolidate-duplicate-urls?#define-
   * canonical). If you did not declare a canonical URL, this field is absent.
   *
   * @var string
   */
  public $userCanonical;
  /**
   * High level verdict about whether the URL *is* indexed (indexed status), or
   * *can be* indexed (live inspection).
   *
   * @var string
   */
  public $verdict;

  /**
   * Could Google find and index the page. More details about page indexing
   * appear in 'indexing_state'.
   *
   * @param string $coverageState
   */
  public function setCoverageState($coverageState)
  {
    $this->coverageState = $coverageState;
  }
  /**
   * @return string
   */
  public function getCoverageState()
  {
    return $this->coverageState;
  }
  /**
   * Primary crawler that was used by Google to crawl your site.
   *
   * Accepted values: CRAWLING_USER_AGENT_UNSPECIFIED, DESKTOP, MOBILE
   *
   * @param self::CRAWLED_AS_* $crawledAs
   */
  public function setCrawledAs($crawledAs)
  {
    $this->crawledAs = $crawledAs;
  }
  /**
   * @return self::CRAWLED_AS_*
   */
  public function getCrawledAs()
  {
    return $this->crawledAs;
  }
  /**
   * The URL of the page that Google selected as canonical. If the page was not
   * indexed, this field is absent.
   *
   * @param string $googleCanonical
   */
  public function setGoogleCanonical($googleCanonical)
  {
    $this->googleCanonical = $googleCanonical;
  }
  /**
   * @return string
   */
  public function getGoogleCanonical()
  {
    return $this->googleCanonical;
  }
  /**
   * Whether or not the page blocks indexing through a noindex rule.
   *
   * Accepted values: INDEXING_STATE_UNSPECIFIED, INDEXING_ALLOWED,
   * BLOCKED_BY_META_TAG, BLOCKED_BY_HTTP_HEADER, BLOCKED_BY_ROBOTS_TXT
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
   * Last time this URL was crawled by Google using the [primary crawler](https:
   * //support.google.com/webmasters/answer/7440203#primary_crawler). Absent if
   * the URL was never crawled successfully.
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
   * Whether or not Google could retrieve the page from your server. Equivalent
   * to ["page fetch"](https://support.google.com/webmasters/answer/9012289#inde
   * x_coverage) in the URL inspection report.
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
   * URLs that link to the inspected URL, directly and indirectly.
   *
   * @param string[] $referringUrls
   */
  public function setReferringUrls($referringUrls)
  {
    $this->referringUrls = $referringUrls;
  }
  /**
   * @return string[]
   */
  public function getReferringUrls()
  {
    return $this->referringUrls;
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
   * Any sitemaps that this URL was listed in, as known by Google. Not
   * guaranteed to be an exhaustive list, especially if Google did not discover
   * this URL through a sitemap. Absent if no sitemaps were found.
   *
   * @param string[] $sitemap
   */
  public function setSitemap($sitemap)
  {
    $this->sitemap = $sitemap;
  }
  /**
   * @return string[]
   */
  public function getSitemap()
  {
    return $this->sitemap;
  }
  /**
   * The URL that your page or site [declares as canonical](https://developers.g
   * oogle.com/search/docs/advanced/crawling/consolidate-duplicate-urls?#define-
   * canonical). If you did not declare a canonical URL, this field is absent.
   *
   * @param string $userCanonical
   */
  public function setUserCanonical($userCanonical)
  {
    $this->userCanonical = $userCanonical;
  }
  /**
   * @return string
   */
  public function getUserCanonical()
  {
    return $this->userCanonical;
  }
  /**
   * High level verdict about whether the URL *is* indexed (indexed status), or
   * *can be* indexed (live inspection).
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
class_alias(IndexStatusInspectionResult::class, 'Google_Service_SearchConsole_IndexStatusInspectionResult');
