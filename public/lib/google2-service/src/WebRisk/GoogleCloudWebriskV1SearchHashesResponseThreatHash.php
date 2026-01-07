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

namespace Google\Service\WebRisk;

class GoogleCloudWebriskV1SearchHashesResponseThreatHash extends \Google\Collection
{
  protected $collection_key = 'threatTypes';
  /**
   * The cache lifetime for the returned match. Clients must not cache this
   * response past this timestamp to avoid false positives.
   *
   * @var string
   */
  public $expireTime;
  /**
   * A 32 byte SHA256 hash. This field is in binary format. For JSON requests,
   * hashes are base64-encoded.
   *
   * @var string
   */
  public $hash;
  /**
   * The ThreatList this threat belongs to. This must contain at least one
   * entry.
   *
   * @var string[]
   */
  public $threatTypes;

  /**
   * The cache lifetime for the returned match. Clients must not cache this
   * response past this timestamp to avoid false positives.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * A 32 byte SHA256 hash. This field is in binary format. For JSON requests,
   * hashes are base64-encoded.
   *
   * @param string $hash
   */
  public function setHash($hash)
  {
    $this->hash = $hash;
  }
  /**
   * @return string
   */
  public function getHash()
  {
    return $this->hash;
  }
  /**
   * The ThreatList this threat belongs to. This must contain at least one
   * entry.
   *
   * @param string[] $threatTypes
   */
  public function setThreatTypes($threatTypes)
  {
    $this->threatTypes = $threatTypes;
  }
  /**
   * @return string[]
   */
  public function getThreatTypes()
  {
    return $this->threatTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudWebriskV1SearchHashesResponseThreatHash::class, 'Google_Service_WebRisk_GoogleCloudWebriskV1SearchHashesResponseThreatHash');
