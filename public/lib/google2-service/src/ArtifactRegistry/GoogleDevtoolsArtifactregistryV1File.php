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

namespace Google\Service\ArtifactRegistry;

class GoogleDevtoolsArtifactregistryV1File extends \Google\Collection
{
  protected $collection_key = 'hashes';
  /**
   * Optional. Client specified annotations.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. The time when the File was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time when the last attempt to refresh the file's data was
   * made. Only set when the repository is remote.
   *
   * @var string
   */
  public $fetchTime;
  protected $hashesType = Hash::class;
  protected $hashesDataType = 'array';
  /**
   * The name of the file, for example: `projects/p1/locations/us-
   * central1/repositories/repo1/files/a%2Fb%2Fc.txt`. If the file ID part
   * contains slashes, they are escaped.
   *
   * @var string
   */
  public $name;
  /**
   * The name of the Package or Version that owns this file, if any.
   *
   * @var string
   */
  public $owner;
  /**
   * The size of the File in bytes.
   *
   * @var string
   */
  public $sizeBytes;
  /**
   * Output only. The time when the File was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Client specified annotations.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. The time when the File was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The time when the last attempt to refresh the file's data was
   * made. Only set when the repository is remote.
   *
   * @param string $fetchTime
   */
  public function setFetchTime($fetchTime)
  {
    $this->fetchTime = $fetchTime;
  }
  /**
   * @return string
   */
  public function getFetchTime()
  {
    return $this->fetchTime;
  }
  /**
   * The hashes of the file content.
   *
   * @param Hash[] $hashes
   */
  public function setHashes($hashes)
  {
    $this->hashes = $hashes;
  }
  /**
   * @return Hash[]
   */
  public function getHashes()
  {
    return $this->hashes;
  }
  /**
   * The name of the file, for example: `projects/p1/locations/us-
   * central1/repositories/repo1/files/a%2Fb%2Fc.txt`. If the file ID part
   * contains slashes, they are escaped.
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
   * The name of the Package or Version that owns this file, if any.
   *
   * @param string $owner
   */
  public function setOwner($owner)
  {
    $this->owner = $owner;
  }
  /**
   * @return string
   */
  public function getOwner()
  {
    return $this->owner;
  }
  /**
   * The size of the File in bytes.
   *
   * @param string $sizeBytes
   */
  public function setSizeBytes($sizeBytes)
  {
    $this->sizeBytes = $sizeBytes;
  }
  /**
   * @return string
   */
  public function getSizeBytes()
  {
    return $this->sizeBytes;
  }
  /**
   * Output only. The time when the File was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsArtifactregistryV1File::class, 'Google_Service_ArtifactRegistry_GoogleDevtoolsArtifactregistryV1File');
