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

class GoogleSecuritySafebrowsingV5FullHash extends \Google\Collection
{
  protected $collection_key = 'fullHashDetails';
  /**
   * The matching full hash. This is the SHA256 hash. The length will be exactly
   * 32 bytes.
   *
   * @var string
   */
  public $fullHash;
  protected $fullHashDetailsType = GoogleSecuritySafebrowsingV5FullHashFullHashDetail::class;
  protected $fullHashDetailsDataType = 'array';

  /**
   * The matching full hash. This is the SHA256 hash. The length will be exactly
   * 32 bytes.
   *
   * @param string $fullHash
   */
  public function setFullHash($fullHash)
  {
    $this->fullHash = $fullHash;
  }
  /**
   * @return string
   */
  public function getFullHash()
  {
    return $this->fullHash;
  }
  /**
   * Unordered list. A repeated field identifying the details relevant to this
   * full hash.
   *
   * @param GoogleSecuritySafebrowsingV5FullHashFullHashDetail[] $fullHashDetails
   */
  public function setFullHashDetails($fullHashDetails)
  {
    $this->fullHashDetails = $fullHashDetails;
  }
  /**
   * @return GoogleSecuritySafebrowsingV5FullHashFullHashDetail[]
   */
  public function getFullHashDetails()
  {
    return $this->fullHashDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleSecuritySafebrowsingV5FullHash::class, 'Google_Service_Safebrowsing_GoogleSecuritySafebrowsingV5FullHash');
