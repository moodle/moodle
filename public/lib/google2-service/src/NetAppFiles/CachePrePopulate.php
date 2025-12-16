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

namespace Google\Service\NetAppFiles;

class CachePrePopulate extends \Google\Collection
{
  protected $collection_key = 'pathList';
  /**
   * Optional. List of directory-paths to be excluded for pre-population for the
   * FlexCache volume.
   *
   * @var string[]
   */
  public $excludePathList;
  /**
   * Optional. List of directory-paths to be pre-populated for the FlexCache
   * volume.
   *
   * @var string[]
   */
  public $pathList;
  /**
   * Optional. Flag indicating whether the directories listed with the pathList
   * need to be recursively pre-populated.
   *
   * @var bool
   */
  public $recursion;

  /**
   * Optional. List of directory-paths to be excluded for pre-population for the
   * FlexCache volume.
   *
   * @param string[] $excludePathList
   */
  public function setExcludePathList($excludePathList)
  {
    $this->excludePathList = $excludePathList;
  }
  /**
   * @return string[]
   */
  public function getExcludePathList()
  {
    return $this->excludePathList;
  }
  /**
   * Optional. List of directory-paths to be pre-populated for the FlexCache
   * volume.
   *
   * @param string[] $pathList
   */
  public function setPathList($pathList)
  {
    $this->pathList = $pathList;
  }
  /**
   * @return string[]
   */
  public function getPathList()
  {
    return $this->pathList;
  }
  /**
   * Optional. Flag indicating whether the directories listed with the pathList
   * need to be recursively pre-populated.
   *
   * @param bool $recursion
   */
  public function setRecursion($recursion)
  {
    $this->recursion = $recursion;
  }
  /**
   * @return bool
   */
  public function getRecursion()
  {
    return $this->recursion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CachePrePopulate::class, 'Google_Service_NetAppFiles_CachePrePopulate');
