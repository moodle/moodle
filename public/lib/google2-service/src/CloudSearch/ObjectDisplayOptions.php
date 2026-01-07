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

namespace Google\Service\CloudSearch;

class ObjectDisplayOptions extends \Google\Collection
{
  protected $collection_key = 'metalines';
  protected $metalinesType = Metaline::class;
  protected $metalinesDataType = 'array';
  /**
   * The user friendly label to display in the search result to indicate the
   * type of the item. This is OPTIONAL; if not provided, an object label isn't
   * displayed on the context line of the search results. The maximum length is
   * 64 characters.
   *
   * @var string
   */
  public $objectDisplayLabel;

  /**
   * Defines the properties that are displayed in the metalines of the search
   * results. The property values are displayed in the order given here. If a
   * property holds multiple values, all of the values are displayed before the
   * next properties. For this reason, it is a good practice to specify singular
   * properties before repeated properties in this list. All of the properties
   * must set is_returnable to true. The maximum number of metalines is 3.
   *
   * @param Metaline[] $metalines
   */
  public function setMetalines($metalines)
  {
    $this->metalines = $metalines;
  }
  /**
   * @return Metaline[]
   */
  public function getMetalines()
  {
    return $this->metalines;
  }
  /**
   * The user friendly label to display in the search result to indicate the
   * type of the item. This is OPTIONAL; if not provided, an object label isn't
   * displayed on the context line of the search results. The maximum length is
   * 64 characters.
   *
   * @param string $objectDisplayLabel
   */
  public function setObjectDisplayLabel($objectDisplayLabel)
  {
    $this->objectDisplayLabel = $objectDisplayLabel;
  }
  /**
   * @return string
   */
  public function getObjectDisplayLabel()
  {
    return $this->objectDisplayLabel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ObjectDisplayOptions::class, 'Google_Service_CloudSearch_ObjectDisplayOptions');
