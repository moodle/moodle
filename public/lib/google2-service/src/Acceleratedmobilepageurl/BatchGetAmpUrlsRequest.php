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

namespace Google\Service\Acceleratedmobilepageurl;

class BatchGetAmpUrlsRequest extends \Google\Collection
{
  /**
   * FETCH_LIVE_DOC strategy involves live document fetch of URLs not found in
   * the index. Any request URL not found in the index is crawled in realtime to
   * validate if there is a corresponding AMP URL. This strategy has higher
   * coverage but with extra latency introduced by realtime crawling. This is
   * the default strategy. Applications using this strategy should set higher
   * HTTP timeouts of the API calls.
   */
  public const LOOKUP_STRATEGY_FETCH_LIVE_DOC = 'FETCH_LIVE_DOC';
  /**
   * IN_INDEX_DOC strategy skips fetching live documents of URL(s) not found in
   * index. For applications which need low latency use of IN_INDEX_DOC strategy
   * is recommended.
   */
  public const LOOKUP_STRATEGY_IN_INDEX_DOC = 'IN_INDEX_DOC';
  protected $collection_key = 'urls';
  /**
   * The lookup_strategy being requested.
   *
   * @var string
   */
  public $lookupStrategy;
  /**
   * List of URLs to look up for the paired AMP URLs. The URLs are case-
   * sensitive. Up to 50 URLs per lookup (see [Usage
   * Limits](/amp/cache/reference/limits)).
   *
   * @var string[]
   */
  public $urls;

  /**
   * The lookup_strategy being requested.
   *
   * Accepted values: FETCH_LIVE_DOC, IN_INDEX_DOC
   *
   * @param self::LOOKUP_STRATEGY_* $lookupStrategy
   */
  public function setLookupStrategy($lookupStrategy)
  {
    $this->lookupStrategy = $lookupStrategy;
  }
  /**
   * @return self::LOOKUP_STRATEGY_*
   */
  public function getLookupStrategy()
  {
    return $this->lookupStrategy;
  }
  /**
   * List of URLs to look up for the paired AMP URLs. The URLs are case-
   * sensitive. Up to 50 URLs per lookup (see [Usage
   * Limits](/amp/cache/reference/limits)).
   *
   * @param string[] $urls
   */
  public function setUrls($urls)
  {
    $this->urls = $urls;
  }
  /**
   * @return string[]
   */
  public function getUrls()
  {
    return $this->urls;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchGetAmpUrlsRequest::class, 'Google_Service_Acceleratedmobilepageurl_BatchGetAmpUrlsRequest');
