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

class GoogleSecuritySafebrowsingV5HashList extends \Google\Model
{
  protected $additionsEightBytesType = GoogleSecuritySafebrowsingV5RiceDeltaEncoded64Bit::class;
  protected $additionsEightBytesDataType = '';
  protected $additionsFourBytesType = GoogleSecuritySafebrowsingV5RiceDeltaEncoded32Bit::class;
  protected $additionsFourBytesDataType = '';
  protected $additionsSixteenBytesType = GoogleSecuritySafebrowsingV5RiceDeltaEncoded128Bit::class;
  protected $additionsSixteenBytesDataType = '';
  protected $additionsThirtyTwoBytesType = GoogleSecuritySafebrowsingV5RiceDeltaEncoded256Bit::class;
  protected $additionsThirtyTwoBytesDataType = '';
  protected $compressedRemovalsType = GoogleSecuritySafebrowsingV5RiceDeltaEncoded32Bit::class;
  protected $compressedRemovalsDataType = '';
  protected $metadataType = GoogleSecuritySafebrowsingV5HashListMetadata::class;
  protected $metadataDataType = '';
  /**
   * Clients should wait at least this long to get the hash list again. If
   * omitted or zero, clients SHOULD fetch immediately because it indicates that
   * the server has an additional update to be sent to the client, but could not
   * due to the client-specified constraints.
   *
   * @var string
   */
  public $minimumWaitDuration;
  /**
   * The name of the hash list. Note that the Global Cache is also just a hash
   * list and can be referred to here.
   *
   * @var string
   */
  public $name;
  /**
   * When true, this is a partial diff containing additions and removals based
   * on what the client already has. When false, this is the complete hash list.
   * When false, the client MUST delete any locally stored version for this hash
   * list. This means that either the version possessed by the client is
   * seriously out-of-date or the client data is believed to be corrupt. The
   * `compressed_removals` field will be empty. When true, the client MUST apply
   * an incremental update by applying removals and then additions.
   *
   * @var bool
   */
  public $partialUpdate;
  /**
   * The sorted list of all hashes, hashed again with SHA256. This is the
   * checksum for the sorted list of all hashes present in the database after
   * applying the provided update. In the case that no updates were provided,
   * the server will omit this field to indicate that the client should use the
   * existing checksum.
   *
   * @var string
   */
  public $sha256Checksum;
  /**
   * The version of the hash list. The client MUST NOT manipulate those bytes.
   *
   * @var string
   */
  public $version;

  /**
   * The 8-byte additions.
   *
   * @param GoogleSecuritySafebrowsingV5RiceDeltaEncoded64Bit $additionsEightBytes
   */
  public function setAdditionsEightBytes(GoogleSecuritySafebrowsingV5RiceDeltaEncoded64Bit $additionsEightBytes)
  {
    $this->additionsEightBytes = $additionsEightBytes;
  }
  /**
   * @return GoogleSecuritySafebrowsingV5RiceDeltaEncoded64Bit
   */
  public function getAdditionsEightBytes()
  {
    return $this->additionsEightBytes;
  }
  /**
   * The 4-byte additions.
   *
   * @param GoogleSecuritySafebrowsingV5RiceDeltaEncoded32Bit $additionsFourBytes
   */
  public function setAdditionsFourBytes(GoogleSecuritySafebrowsingV5RiceDeltaEncoded32Bit $additionsFourBytes)
  {
    $this->additionsFourBytes = $additionsFourBytes;
  }
  /**
   * @return GoogleSecuritySafebrowsingV5RiceDeltaEncoded32Bit
   */
  public function getAdditionsFourBytes()
  {
    return $this->additionsFourBytes;
  }
  /**
   * The 16-byte additions.
   *
   * @param GoogleSecuritySafebrowsingV5RiceDeltaEncoded128Bit $additionsSixteenBytes
   */
  public function setAdditionsSixteenBytes(GoogleSecuritySafebrowsingV5RiceDeltaEncoded128Bit $additionsSixteenBytes)
  {
    $this->additionsSixteenBytes = $additionsSixteenBytes;
  }
  /**
   * @return GoogleSecuritySafebrowsingV5RiceDeltaEncoded128Bit
   */
  public function getAdditionsSixteenBytes()
  {
    return $this->additionsSixteenBytes;
  }
  /**
   * The 32-byte additions.
   *
   * @param GoogleSecuritySafebrowsingV5RiceDeltaEncoded256Bit $additionsThirtyTwoBytes
   */
  public function setAdditionsThirtyTwoBytes(GoogleSecuritySafebrowsingV5RiceDeltaEncoded256Bit $additionsThirtyTwoBytes)
  {
    $this->additionsThirtyTwoBytes = $additionsThirtyTwoBytes;
  }
  /**
   * @return GoogleSecuritySafebrowsingV5RiceDeltaEncoded256Bit
   */
  public function getAdditionsThirtyTwoBytes()
  {
    return $this->additionsThirtyTwoBytes;
  }
  /**
   * The Rice-delta encoded version of removal indices. Since each hash list
   * definitely has less than 2^32 entries, the indices are treated as 32-bit
   * integers and encoded.
   *
   * @param GoogleSecuritySafebrowsingV5RiceDeltaEncoded32Bit $compressedRemovals
   */
  public function setCompressedRemovals(GoogleSecuritySafebrowsingV5RiceDeltaEncoded32Bit $compressedRemovals)
  {
    $this->compressedRemovals = $compressedRemovals;
  }
  /**
   * @return GoogleSecuritySafebrowsingV5RiceDeltaEncoded32Bit
   */
  public function getCompressedRemovals()
  {
    return $this->compressedRemovals;
  }
  /**
   * Metadata about the hash list. This is not populated by the `GetHashList`
   * method, but this is populated by the `ListHashLists` method.
   *
   * @param GoogleSecuritySafebrowsingV5HashListMetadata $metadata
   */
  public function setMetadata(GoogleSecuritySafebrowsingV5HashListMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return GoogleSecuritySafebrowsingV5HashListMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Clients should wait at least this long to get the hash list again. If
   * omitted or zero, clients SHOULD fetch immediately because it indicates that
   * the server has an additional update to be sent to the client, but could not
   * due to the client-specified constraints.
   *
   * @param string $minimumWaitDuration
   */
  public function setMinimumWaitDuration($minimumWaitDuration)
  {
    $this->minimumWaitDuration = $minimumWaitDuration;
  }
  /**
   * @return string
   */
  public function getMinimumWaitDuration()
  {
    return $this->minimumWaitDuration;
  }
  /**
   * The name of the hash list. Note that the Global Cache is also just a hash
   * list and can be referred to here.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * When true, this is a partial diff containing additions and removals based
   * on what the client already has. When false, this is the complete hash list.
   * When false, the client MUST delete any locally stored version for this hash
   * list. This means that either the version possessed by the client is
   * seriously out-of-date or the client data is believed to be corrupt. The
   * `compressed_removals` field will be empty. When true, the client MUST apply
   * an incremental update by applying removals and then additions.
   *
   * @param bool $partialUpdate
   */
  public function setPartialUpdate($partialUpdate)
  {
    $this->partialUpdate = $partialUpdate;
  }
  /**
   * @return bool
   */
  public function getPartialUpdate()
  {
    return $this->partialUpdate;
  }
  /**
   * The sorted list of all hashes, hashed again with SHA256. This is the
   * checksum for the sorted list of all hashes present in the database after
   * applying the provided update. In the case that no updates were provided,
   * the server will omit this field to indicate that the client should use the
   * existing checksum.
   *
   * @param string $sha256Checksum
   */
  public function setSha256Checksum($sha256Checksum)
  {
    $this->sha256Checksum = $sha256Checksum;
  }
  /**
   * @return string
   */
  public function getSha256Checksum()
  {
    return $this->sha256Checksum;
  }
  /**
   * The version of the hash list. The client MUST NOT manipulate those bytes.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleSecuritySafebrowsingV5HashList::class, 'Google_Service_Safebrowsing_GoogleSecuritySafebrowsingV5HashList');
