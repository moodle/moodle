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

class GoogleSecuritySafebrowsingV5HashListMetadata extends \Google\Collection
{
  /**
   * Unspecified length.
   */
  public const HASH_LENGTH_HASH_LENGTH_UNSPECIFIED = 'HASH_LENGTH_UNSPECIFIED';
  /**
   * Each hash is a four-byte prefix.
   */
  public const HASH_LENGTH_FOUR_BYTES = 'FOUR_BYTES';
  /**
   * Each hash is an eight-byte prefix.
   */
  public const HASH_LENGTH_EIGHT_BYTES = 'EIGHT_BYTES';
  /**
   * Each hash is a sixteen-byte prefix.
   */
  public const HASH_LENGTH_SIXTEEN_BYTES = 'SIXTEEN_BYTES';
  /**
   * Each hash is a thirty-two-byte full hash.
   */
  public const HASH_LENGTH_THIRTY_TWO_BYTES = 'THIRTY_TWO_BYTES';
  protected $collection_key = 'threatTypes';
  /**
   * A human-readable description about this list. Written in English.
   *
   * @var string
   */
  public $description;
  /**
   * The supported hash length for this hash list. Each hash list will support
   * exactly one length. If a different hash length is introduced for the same
   * set of threat types or safe types, it will be introduced as a separate list
   * with a distinct name and respective hash length set.
   *
   * @var string
   */
  public $hashLength;
  /**
   * Unordered list. If not empty, this specifies that the hash list represents
   * a list of likely safe hashes, and this enumerates the ways they are
   * considered likely safe. This field is mutually exclusive with the
   * threat_types field.
   *
   * @var string[]
   */
  public $likelySafeTypes;
  /**
   * Unordered list. If not empty, this specifies that the hash list is a kind
   * of threat list, and this enumerates the kind of threats associated with
   * hashes or hash prefixes in this hash list. May be empty if the entry does
   * not represent a threat, i.e. in the case that it represents a likely safe
   * type.
   *
   * @var string[]
   */
  public $threatTypes;

  /**
   * A human-readable description about this list. Written in English.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The supported hash length for this hash list. Each hash list will support
   * exactly one length. If a different hash length is introduced for the same
   * set of threat types or safe types, it will be introduced as a separate list
   * with a distinct name and respective hash length set.
   *
   * Accepted values: HASH_LENGTH_UNSPECIFIED, FOUR_BYTES, EIGHT_BYTES,
   * SIXTEEN_BYTES, THIRTY_TWO_BYTES
   *
   * @param self::HASH_LENGTH_* $hashLength
   */
  public function setHashLength($hashLength)
  {
    $this->hashLength = $hashLength;
  }
  /**
   * @return self::HASH_LENGTH_*
   */
  public function getHashLength()
  {
    return $this->hashLength;
  }
  /**
   * Unordered list. If not empty, this specifies that the hash list represents
   * a list of likely safe hashes, and this enumerates the ways they are
   * considered likely safe. This field is mutually exclusive with the
   * threat_types field.
   *
   * @param string[] $likelySafeTypes
   */
  public function setLikelySafeTypes($likelySafeTypes)
  {
    $this->likelySafeTypes = $likelySafeTypes;
  }
  /**
   * @return string[]
   */
  public function getLikelySafeTypes()
  {
    return $this->likelySafeTypes;
  }
  /**
   * Unordered list. If not empty, this specifies that the hash list is a kind
   * of threat list, and this enumerates the kind of threats associated with
   * hashes or hash prefixes in this hash list. May be empty if the entry does
   * not represent a threat, i.e. in the case that it represents a likely safe
   * type.
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
class_alias(GoogleSecuritySafebrowsingV5HashListMetadata::class, 'Google_Service_Safebrowsing_GoogleSecuritySafebrowsingV5HashListMetadata');
