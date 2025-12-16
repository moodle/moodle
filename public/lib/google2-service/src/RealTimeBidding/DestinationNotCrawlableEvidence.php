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

namespace Google\Service\RealTimeBidding;

class DestinationNotCrawlableEvidence extends \Google\Model
{
  /**
   * Default value that should never be used.
   */
  public const REASON_REASON_UNSPECIFIED = 'REASON_UNSPECIFIED';
  /**
   * Site's robots exclusion file (for example, robots.txt) was unreachable.
   */
  public const REASON_UNREACHABLE_ROBOTS = 'UNREACHABLE_ROBOTS';
  /**
   * Timed out reading site's robots exclusion file (for example, robots.txt).
   */
  public const REASON_TIMEOUT_ROBOTS = 'TIMEOUT_ROBOTS';
  /**
   * Crawler was disallowed by the site's robots exclusion file (for example,
   * robots.txt).
   */
  public const REASON_ROBOTED_DENIED = 'ROBOTED_DENIED';
  /**
   * Unknown reason.
   */
  public const REASON_UNKNOWN = 'UNKNOWN';
  /**
   * Approximate time of the crawl.
   *
   * @var string
   */
  public $crawlTime;
  /**
   * Destination URL that was attempted to be crawled.
   *
   * @var string
   */
  public $crawledUrl;
  /**
   * Reason of destination not crawlable.
   *
   * @var string
   */
  public $reason;

  /**
   * Approximate time of the crawl.
   *
   * @param string $crawlTime
   */
  public function setCrawlTime($crawlTime)
  {
    $this->crawlTime = $crawlTime;
  }
  /**
   * @return string
   */
  public function getCrawlTime()
  {
    return $this->crawlTime;
  }
  /**
   * Destination URL that was attempted to be crawled.
   *
   * @param string $crawledUrl
   */
  public function setCrawledUrl($crawledUrl)
  {
    $this->crawledUrl = $crawledUrl;
  }
  /**
   * @return string
   */
  public function getCrawledUrl()
  {
    return $this->crawledUrl;
  }
  /**
   * Reason of destination not crawlable.
   *
   * Accepted values: REASON_UNSPECIFIED, UNREACHABLE_ROBOTS, TIMEOUT_ROBOTS,
   * ROBOTED_DENIED, UNKNOWN
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DestinationNotCrawlableEvidence::class, 'Google_Service_RealTimeBidding_DestinationNotCrawlableEvidence');
