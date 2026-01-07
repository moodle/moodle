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

namespace Google\Service\Testing;

class IntentFilter extends \Google\Collection
{
  protected $collection_key = 'categoryNames';
  /**
   * The android:name value of the tag.
   *
   * @var string[]
   */
  public $actionNames;
  /**
   * The android:name value of the tag.
   *
   * @var string[]
   */
  public $categoryNames;
  /**
   * The android:mimeType value of the tag.
   *
   * @var string
   */
  public $mimeType;

  /**
   * The android:name value of the tag.
   *
   * @param string[] $actionNames
   */
  public function setActionNames($actionNames)
  {
    $this->actionNames = $actionNames;
  }
  /**
   * @return string[]
   */
  public function getActionNames()
  {
    return $this->actionNames;
  }
  /**
   * The android:name value of the tag.
   *
   * @param string[] $categoryNames
   */
  public function setCategoryNames($categoryNames)
  {
    $this->categoryNames = $categoryNames;
  }
  /**
   * @return string[]
   */
  public function getCategoryNames()
  {
    return $this->categoryNames;
  }
  /**
   * The android:mimeType value of the tag.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IntentFilter::class, 'Google_Service_Testing_IntentFilter');
