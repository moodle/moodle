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

namespace Google\Service\Dataform;

class UncommittedFileChange extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The file has been newly added.
   */
  public const STATE_ADDED = 'ADDED';
  /**
   * The file has been deleted.
   */
  public const STATE_DELETED = 'DELETED';
  /**
   * The file has been modified.
   */
  public const STATE_MODIFIED = 'MODIFIED';
  /**
   * The file contains merge conflicts.
   */
  public const STATE_HAS_CONFLICTS = 'HAS_CONFLICTS';
  /**
   * The file's full path including filename, relative to the workspace root.
   *
   * @var string
   */
  public $path;
  /**
   * Output only. Indicates the status of the file.
   *
   * @var string
   */
  public $state;

  /**
   * The file's full path including filename, relative to the workspace root.
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
   * Output only. Indicates the status of the file.
   *
   * Accepted values: STATE_UNSPECIFIED, ADDED, DELETED, MODIFIED, HAS_CONFLICTS
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UncommittedFileChange::class, 'Google_Service_Dataform_UncommittedFileChange');
