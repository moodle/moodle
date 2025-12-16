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

namespace Google\Service\ToolResults;

class OverlappingUIElements extends \Google\Collection
{
  protected $collection_key = 'resourceName';
  /**
   * Resource names of the overlapping screen elements
   *
   * @var string[]
   */
  public $resourceName;
  /**
   * The screen id of the elements
   *
   * @var string
   */
  public $screenId;

  /**
   * Resource names of the overlapping screen elements
   *
   * @param string[] $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string[]
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * The screen id of the elements
   *
   * @param string $screenId
   */
  public function setScreenId($screenId)
  {
    $this->screenId = $screenId;
  }
  /**
   * @return string
   */
  public function getScreenId()
  {
    return $this->screenId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OverlappingUIElements::class, 'Google_Service_ToolResults_OverlappingUIElements');
