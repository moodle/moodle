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

class GoogleCloudWebriskV1RawHashes extends \Google\Model
{
  /**
   * The number of bytes for each prefix encoded below. This field can be
   * anywhere from 4 (shortest prefix) to 32 (full SHA256 hash). In practice
   * this is almost always 4, except in exceptional circumstances.
   *
   * @var int
   */
  public $prefixSize;
  /**
   * The hashes, in binary format, concatenated into one long string. Hashes are
   * sorted in lexicographic order. For JSON API users, hashes are
   * base64-encoded.
   *
   * @var string
   */
  public $rawHashes;

  /**
   * The number of bytes for each prefix encoded below. This field can be
   * anywhere from 4 (shortest prefix) to 32 (full SHA256 hash). In practice
   * this is almost always 4, except in exceptional circumstances.
   *
   * @param int $prefixSize
   */
  public function setPrefixSize($prefixSize)
  {
    $this->prefixSize = $prefixSize;
  }
  /**
   * @return int
   */
  public function getPrefixSize()
  {
    return $this->prefixSize;
  }
  /**
   * The hashes, in binary format, concatenated into one long string. Hashes are
   * sorted in lexicographic order. For JSON API users, hashes are
   * base64-encoded.
   *
   * @param string $rawHashes
   */
  public function setRawHashes($rawHashes)
  {
    $this->rawHashes = $rawHashes;
  }
  /**
   * @return string
   */
  public function getRawHashes()
  {
    return $this->rawHashes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudWebriskV1RawHashes::class, 'Google_Service_WebRisk_GoogleCloudWebriskV1RawHashes');
