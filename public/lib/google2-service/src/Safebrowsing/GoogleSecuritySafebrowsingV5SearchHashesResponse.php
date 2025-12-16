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

namespace Google\Service\Safebrowsing;

class GoogleSecuritySafebrowsingV5SearchHashesResponse extends \Google\Collection
{
  protected $collection_key = 'fullHashes';
  /**
   * The client-side cache duration. The client MUST add this duration to the
   * current time to determine the expiration time. The expiration time then
   * applies to every hash prefix queried by the client in the request,
   * regardless of how many full hashes are returned in the response. Even if
   * the server returns no full hashes for a particular hash prefix, this fact
   * MUST also be cached by the client. If and only if the field `full_hashes`
   * is empty, the client MAY increase the `cache_duration` to determine a new
   * expiration that is later than that specified by the server. In any case,
   * the increased cache duration must not be longer than 24 hours. Important:
   * the client MUST NOT assume that the server will return the same cache
   * duration for all responses. The server MAY choose different cache durations
   * for different responses depending on the situation.
   *
   * @var string
   */
  public $cacheDuration;
  protected $fullHashesType = GoogleSecuritySafebrowsingV5FullHash::class;
  protected $fullHashesDataType = 'array';

  /**
   * The client-side cache duration. The client MUST add this duration to the
   * current time to determine the expiration time. The expiration time then
   * applies to every hash prefix queried by the client in the request,
   * regardless of how many full hashes are returned in the response. Even if
   * the server returns no full hashes for a particular hash prefix, this fact
   * MUST also be cached by the client. If and only if the field `full_hashes`
   * is empty, the client MAY increase the `cache_duration` to determine a new
   * expiration that is later than that specified by the server. In any case,
   * the increased cache duration must not be longer than 24 hours. Important:
   * the client MUST NOT assume that the server will return the same cache
   * duration for all responses. The server MAY choose different cache durations
   * for different responses depending on the situation.
   *
   * @param string $cacheDuration
   */
  public function setCacheDuration($cacheDuration)
  {
    $this->cacheDuration = $cacheDuration;
  }
  /**
   * @return string
   */
  public function getCacheDuration()
  {
    return $this->cacheDuration;
  }
  /**
   * Unordered list. The unordered list of full hashes found.
   *
   * @param GoogleSecuritySafebrowsingV5FullHash[] $fullHashes
   */
  public function setFullHashes($fullHashes)
  {
    $this->fullHashes = $fullHashes;
  }
  /**
   * @return GoogleSecuritySafebrowsingV5FullHash[]
   */
  public function getFullHashes()
  {
    return $this->fullHashes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleSecuritySafebrowsingV5SearchHashesResponse::class, 'Google_Service_Safebrowsing_GoogleSecuritySafebrowsingV5SearchHashesResponse');
