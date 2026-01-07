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

namespace Google\Service\Slides;

class SlidesList extends \Google\Model
{
  /**
   * The ID of the list.
   *
   * @var string
   */
  public $listId;
  protected $nestingLevelType = NestingLevel::class;
  protected $nestingLevelDataType = 'map';

  /**
   * The ID of the list.
   *
   * @param string $listId
   */
  public function setListId($listId)
  {
    $this->listId = $listId;
  }
  /**
   * @return string
   */
  public function getListId()
  {
    return $this->listId;
  }
  /**
   * A map of nesting levels to the properties of bullets at the associated
   * level. A list has at most nine levels of nesting, so the possible values
   * for the keys of this map are 0 through 8, inclusive.
   *
   * @param NestingLevel[] $nestingLevel
   */
  public function setNestingLevel($nestingLevel)
  {
    $this->nestingLevel = $nestingLevel;
  }
  /**
   * @return NestingLevel[]
   */
  public function getNestingLevel()
  {
    return $this->nestingLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SlidesList::class, 'Google_Service_Slides_SlidesList');
