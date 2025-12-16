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

class FileDiff extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const ACTION_ACTION_UNSPECIFIED = 'ACTION_UNSPECIFIED';
  /**
   * The file was added.
   */
  public const ACTION_ADDED = 'ADDED';
  /**
   * The file was modified.
   */
  public const ACTION_MODIFIED = 'MODIFIED';
  /**
   * The file was deleted.
   */
  public const ACTION_DELETED = 'DELETED';
  /**
   * Output only. The action taken on the file (eg. added, modified, deleted).
   *
   * @var string
   */
  public $action;
  /**
   * Output only. The name of the file.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The git patch containing the file changes.
   *
   * @var string
   */
  public $patch;
  /**
   * Output only. The commit pointing to the file changes.
   *
   * @var string
   */
  public $sha;

  /**
   * Output only. The action taken on the file (eg. added, modified, deleted).
   *
   * Accepted values: ACTION_UNSPECIFIED, ADDED, MODIFIED, DELETED
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Output only. The name of the file.
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
   * Output only. The git patch containing the file changes.
   *
   * @param string $patch
   */
  public function setPatch($patch)
  {
    $this->patch = $patch;
  }
  /**
   * @return string
   */
  public function getPatch()
  {
    return $this->patch;
  }
  /**
   * Output only. The commit pointing to the file changes.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FileDiff::class, 'Google_Service_SecureSourceManager_FileDiff');
