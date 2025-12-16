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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2File extends \Google\Collection
{
  protected $collection_key = 'operations';
  /**
   * Prefix of the file contents as a JSON-encoded string.
   *
   * @var string
   */
  public $contents;
  protected $diskPathType = GoogleCloudSecuritycenterV2DiskPath::class;
  protected $diskPathDataType = '';
  /**
   * The length in bytes of the file prefix that was hashed. If hashed_size ==
   * size, any hashes reported represent the entire file.
   *
   * @var string
   */
  public $hashedSize;
  protected $operationsType = GoogleCloudSecuritycenterV2FileOperation::class;
  protected $operationsDataType = 'array';
  /**
   * True when the hash covers only a prefix of the file.
   *
   * @var bool
   */
  public $partiallyHashed;
  /**
   * Absolute path of the file as a JSON encoded string.
   *
   * @var string
   */
  public $path;
  /**
   * SHA256 hash of the first hashed_size bytes of the file encoded as a hex
   * string. If hashed_size == size, sha256 represents the SHA256 hash of the
   * entire file.
   *
   * @var string
   */
  public $sha256;
  /**
   * Size of the file in bytes.
   *
   * @var string
   */
  public $size;

  /**
   * Prefix of the file contents as a JSON-encoded string.
   *
   * @param string $contents
   */
  public function setContents($contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return string
   */
  public function getContents()
  {
    return $this->contents;
  }
  /**
   * Path of the file in terms of underlying disk/partition identifiers.
   *
   * @param GoogleCloudSecuritycenterV2DiskPath $diskPath
   */
  public function setDiskPath(GoogleCloudSecuritycenterV2DiskPath $diskPath)
  {
    $this->diskPath = $diskPath;
  }
  /**
   * @return GoogleCloudSecuritycenterV2DiskPath
   */
  public function getDiskPath()
  {
    return $this->diskPath;
  }
  /**
   * The length in bytes of the file prefix that was hashed. If hashed_size ==
   * size, any hashes reported represent the entire file.
   *
   * @param string $hashedSize
   */
  public function setHashedSize($hashedSize)
  {
    $this->hashedSize = $hashedSize;
  }
  /**
   * @return string
   */
  public function getHashedSize()
  {
    return $this->hashedSize;
  }
  /**
   * Operation(s) performed on a file.
   *
   * @param GoogleCloudSecuritycenterV2FileOperation[] $operations
   */
  public function setOperations($operations)
  {
    $this->operations = $operations;
  }
  /**
   * @return GoogleCloudSecuritycenterV2FileOperation[]
   */
  public function getOperations()
  {
    return $this->operations;
  }
  /**
   * True when the hash covers only a prefix of the file.
   *
   * @param bool $partiallyHashed
   */
  public function setPartiallyHashed($partiallyHashed)
  {
    $this->partiallyHashed = $partiallyHashed;
  }
  /**
   * @return bool
   */
  public function getPartiallyHashed()
  {
    return $this->partiallyHashed;
  }
  /**
   * Absolute path of the file as a JSON encoded string.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * SHA256 hash of the first hashed_size bytes of the file encoded as a hex
   * string. If hashed_size == size, sha256 represents the SHA256 hash of the
   * entire file.
   *
   * @param string $sha256
   */
  public function setSha256($sha256)
  {
    $this->sha256 = $sha256;
  }
  /**
   * @return string
   */
  public function getSha256()
  {
    return $this->sha256;
  }
  /**
   * Size of the file in bytes.
   *
   * @param string $size
   */
  public function setSize($size)
  {
    $this->size = $size;
  }
  /**
   * @return string
   */
  public function getSize()
  {
    return $this->size;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2File::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2File');
