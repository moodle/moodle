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

namespace Google\Service\Drive;

class AppList extends \Google\Collection
{
  protected $collection_key = 'items';
  /**
   * The list of app IDs that the user has specified to use by default. The list
   * is in reverse-priority order (lowest to highest).
   *
   * @var string[]
   */
  public $defaultAppIds;
  protected $itemsType = App::class;
  protected $itemsDataType = 'array';
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string "drive#appList".
   *
   * @var string
   */
  public $kind;
  /**
   * A link back to this list.
   *
   * @var string
   */
  public $selfLink;

  /**
   * The list of app IDs that the user has specified to use by default. The list
   * is in reverse-priority order (lowest to highest).
   *
   * @param string[] $defaultAppIds
   */
  public function setDefaultAppIds($defaultAppIds)
  {
    $this->defaultAppIds = $defaultAppIds;
  }
  /**
   * @return string[]
   */
  public function getDefaultAppIds()
  {
    return $this->defaultAppIds;
  }
  /**
   * The list of apps.
   *
   * @param App[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return App[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string "drive#appList".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * A link back to this list.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppList::class, 'Google_Service_Drive_AppList');
