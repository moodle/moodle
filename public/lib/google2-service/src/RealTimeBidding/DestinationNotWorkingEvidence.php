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

class DestinationNotWorkingEvidence extends \Google\Model
{
  /**
   * Default value that should never be used.
   */
  public const DNS_ERROR_DNS_ERROR_UNSPECIFIED = 'DNS_ERROR_UNSPECIFIED';
  /**
   * DNS name was not found.
   */
  public const DNS_ERROR_ERROR_DNS = 'ERROR_DNS';
  /**
   * An internal issue occurred when Google's crawler tried to resolve the DNS
   * entry. This is a Google-internal issue and may not be the result of an
   * issue with the landing page.
   */
  public const DNS_ERROR_GOOGLE_CRAWLER_DNS_ISSUE = 'GOOGLE_CRAWLER_DNS_ISSUE';
  /**
   * Default value that should never be used.
   */
  public const INVALID_PAGE_INVALID_PAGE_UNSPECIFIED = 'INVALID_PAGE_UNSPECIFIED';
  /**
   * Page was empty or had an error.
   */
  public const INVALID_PAGE_EMPTY_OR_ERROR_PAGE = 'EMPTY_OR_ERROR_PAGE';
  /**
   * Default value that should never be used.
   */
  public const PLATFORM_PLATFORM_UNSPECIFIED = 'PLATFORM_UNSPECIFIED';
  /**
   * The personal computer platform.
   */
  public const PLATFORM_PERSONAL_COMPUTER = 'PERSONAL_COMPUTER';
  /**
   * The Android platform.
   */
  public const PLATFORM_ANDROID = 'ANDROID';
  /**
   * The iOS platform.
   */
  public const PLATFORM_IOS = 'IOS';
  /**
   * Default value that should never be used.
   */
  public const REDIRECTION_ERROR_REDIRECTION_ERROR_UNSPECIFIED = 'REDIRECTION_ERROR_UNSPECIFIED';
  /**
   * Too many redirect hops.
   */
  public const REDIRECTION_ERROR_TOO_MANY_REDIRECTS = 'TOO_MANY_REDIRECTS';
  /**
   * Got a redirect but it was invalid.
   */
  public const REDIRECTION_ERROR_INVALID_REDIRECT = 'INVALID_REDIRECT';
  /**
   * Got a redirect but it was empty.
   */
  public const REDIRECTION_ERROR_EMPTY_REDIRECT = 'EMPTY_REDIRECT';
  /**
   * Unknown redirect error.
   */
  public const REDIRECTION_ERROR_REDIRECT_ERROR_UNKNOWN = 'REDIRECT_ERROR_UNKNOWN';
  /**
   * Default value that should never be used.
   */
  public const URL_REJECTED_URL_REJECTED_UNSPECIFIED = 'URL_REJECTED_UNSPECIFIED';
  /**
   * URL rejected because of a malformed request.
   */
  public const URL_REJECTED_BAD_REQUEST = 'BAD_REQUEST';
  /**
   * URL rejected because of a malformed URL.
   */
  public const URL_REJECTED_MALFORMED_URL = 'MALFORMED_URL';
  /**
   * URL rejected because of unknown reason.
   */
  public const URL_REJECTED_URL_REJECTED_UNKNOWN = 'URL_REJECTED_UNKNOWN';
  /**
   * DNS lookup errors.
   *
   * @var string
   */
  public $dnsError;
  /**
   * The full non-working URL.
   *
   * @var string
   */
  public $expandedUrl;
  /**
   * HTTP error code (for example, 404 or 5xx)
   *
   * @var int
   */
  public $httpError;
  /**
   * Page was crawled successfully, but was detected as either a page with no
   * content or an error page.
   *
   * @var string
   */
  public $invalidPage;
  /**
   * Approximate time when the ad destination was last checked.
   *
   * @var string
   */
  public $lastCheckTime;
  /**
   * Platform of the non-working URL.
   *
   * @var string
   */
  public $platform;
  /**
   * HTTP redirect chain error.
   *
   * @var string
   */
  public $redirectionError;
  /**
   * Rejected because of malformed URLs or invalid requests.
   *
   * @var string
   */
  public $urlRejected;

  /**
   * DNS lookup errors.
   *
   * Accepted values: DNS_ERROR_UNSPECIFIED, ERROR_DNS, GOOGLE_CRAWLER_DNS_ISSUE
   *
   * @param self::DNS_ERROR_* $dnsError
   */
  public function setDnsError($dnsError)
  {
    $this->dnsError = $dnsError;
  }
  /**
   * @return self::DNS_ERROR_*
   */
  public function getDnsError()
  {
    return $this->dnsError;
  }
  /**
   * The full non-working URL.
   *
   * @param string $expandedUrl
   */
  public function setExpandedUrl($expandedUrl)
  {
    $this->expandedUrl = $expandedUrl;
  }
  /**
   * @return string
   */
  public function getExpandedUrl()
  {
    return $this->expandedUrl;
  }
  /**
   * HTTP error code (for example, 404 or 5xx)
   *
   * @param int $httpError
   */
  public function setHttpError($httpError)
  {
    $this->httpError = $httpError;
  }
  /**
   * @return int
   */
  public function getHttpError()
  {
    return $this->httpError;
  }
  /**
   * Page was crawled successfully, but was detected as either a page with no
   * content or an error page.
   *
   * Accepted values: INVALID_PAGE_UNSPECIFIED, EMPTY_OR_ERROR_PAGE
   *
   * @param self::INVALID_PAGE_* $invalidPage
   */
  public function setInvalidPage($invalidPage)
  {
    $this->invalidPage = $invalidPage;
  }
  /**
   * @return self::INVALID_PAGE_*
   */
  public function getInvalidPage()
  {
    return $this->invalidPage;
  }
  /**
   * Approximate time when the ad destination was last checked.
   *
   * @param string $lastCheckTime
   */
  public function setLastCheckTime($lastCheckTime)
  {
    $this->lastCheckTime = $lastCheckTime;
  }
  /**
   * @return string
   */
  public function getLastCheckTime()
  {
    return $this->lastCheckTime;
  }
  /**
   * Platform of the non-working URL.
   *
   * Accepted values: PLATFORM_UNSPECIFIED, PERSONAL_COMPUTER, ANDROID, IOS
   *
   * @param self::PLATFORM_* $platform
   */
  public function setPlatform($platform)
  {
    $this->platform = $platform;
  }
  /**
   * @return self::PLATFORM_*
   */
  public function getPlatform()
  {
    return $this->platform;
  }
  /**
   * HTTP redirect chain error.
   *
   * Accepted values: REDIRECTION_ERROR_UNSPECIFIED, TOO_MANY_REDIRECTS,
   * INVALID_REDIRECT, EMPTY_REDIRECT, REDIRECT_ERROR_UNKNOWN
   *
   * @param self::REDIRECTION_ERROR_* $redirectionError
   */
  public function setRedirectionError($redirectionError)
  {
    $this->redirectionError = $redirectionError;
  }
  /**
   * @return self::REDIRECTION_ERROR_*
   */
  public function getRedirectionError()
  {
    return $this->redirectionError;
  }
  /**
   * Rejected because of malformed URLs or invalid requests.
   *
   * Accepted values: URL_REJECTED_UNSPECIFIED, BAD_REQUEST, MALFORMED_URL,
   * URL_REJECTED_UNKNOWN
   *
   * @param self::URL_REJECTED_* $urlRejected
   */
  public function setUrlRejected($urlRejected)
  {
    $this->urlRejected = $urlRejected;
  }
  /**
   * @return self::URL_REJECTED_*
   */
  public function getUrlRejected()
  {
    return $this->urlRejected;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DestinationNotWorkingEvidence::class, 'Google_Service_RealTimeBidding_DestinationNotWorkingEvidence');
