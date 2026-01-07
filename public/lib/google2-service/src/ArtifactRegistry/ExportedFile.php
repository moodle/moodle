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

class ExportedFile extends \Google\Collection
{
  protected $collection_key = 'hashes';
  /**
   * Cloud Storage Object path of the exported file. Examples:
   * `dst_bucket/file1`, `dst_bucket/sub_dir/file1`
   *
   * @var string
   */
  public $gcsObjectPath;
  protected $hashesType = Hash::class;
  protected $hashesDataType = 'array';
  /**
   * Name of the exported artifact file. Format:
   * `projects/p1/locations/us/repositories/repo1/files/file1`
   *
   * @var string
   */
  public $name;

  /**
   * Cloud Storage Object path of the exported file. Examples:
   * `dst_bucket/file1`, `dst_bucket/sub_dir/file1`
   *
   * @param string $gcsObjectPath
   */
  public function setGcsObjectPath($gcsObjectPath)
  {
    $this->gcsObjectPath = $gcsObjectPath;
  }
  /**
   * @return string
   */
  public function getGcsObjectPath()
  {
    return $this->gcsObjectPath;
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
   * Name of the exported artifact file. Format:
   * `projects/p1/locations/us/repositories/repo1/files/file1`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExportedFile::class, 'Google_Service_ArtifactRegistry_ExportedFile');
