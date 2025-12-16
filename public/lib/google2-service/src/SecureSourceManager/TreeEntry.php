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

namespace Google\Service\SecureSourceManager;

class TreeEntry extends \Google\Model
{
  /**
   * Default value, indicating the object type is unspecified.
   */
  public const TYPE_OBJECT_TYPE_UNSPECIFIED = 'OBJECT_TYPE_UNSPECIFIED';
  /**
   * Represents a directory (folder).
   */
  public const TYPE_TREE = 'TREE';
  /**
   * Represents a file (contains file data).
   */
  public const TYPE_BLOB = 'BLOB';
  /**
   * Represents a pointer to another repository (submodule).
   */
  public const TYPE_COMMIT = 'COMMIT';
  /**
   * Output only. The file mode as a string (e.g., "100644"). Indicates file
   * type. Output-only.
   *
   * @var string
   */
  public $mode;
  /**
   * Output only. The path of the file or directory within the tree (e.g.,
   * "src/main/java/MyClass.java"). Output-only.
   *
   * @var string
   */
  public $path;
  /**
   * Output only. The SHA-1 hash of the object (unique identifier). Output-only.
   *
   * @var string
   */
  public $sha;
  /**
   * Output only. The size of the object in bytes (only for blobs). Output-only.
   *
   * @var string
   */
  public $size;
  /**
   * Output only. The type of the object (TREE, BLOB, COMMIT). Output-only.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The file mode as a string (e.g., "100644"). Indicates file
   * type. Output-only.
   *
   * @param string $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return string
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Output only. The path of the file or directory within the tree (e.g.,
   * "src/main/java/MyClass.java"). Output-only.
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
   * Output only. The SHA-1 hash of the object (unique identifier). Output-only.
   *
   * @param string $sha
   */
  public function setSha($sha)
  {
    $this->sha = $sha;
  }
  /**
   * @return string
   */
  public function getSha()
  {
    return $this->sha;
  }
  /**
   * Output only. The size of the object in bytes (only for blobs). Output-only.
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
  /**
   * Output only. The type of the object (TREE, BLOB, COMMIT). Output-only.
   *
   * Accepted values: OBJECT_TYPE_UNSPECIFIED, TREE, BLOB, COMMIT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TreeEntry::class, 'Google_Service_SecureSourceManager_TreeEntry');
