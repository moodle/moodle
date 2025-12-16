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

namespace Google\Service\Forms;

class UpdateItemRequest extends \Google\Model
{
  protected $itemType = Item::class;
  protected $itemDataType = '';
  protected $locationType = Location::class;
  protected $locationDataType = '';
  /**
   * Required. Only values named in this mask are changed.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Required. New values for the item. Note that item and question IDs are used
   * if they are provided (and are in the field mask). If an ID is blank (and in
   * the field mask) a new ID is generated. This means you can modify an item by
   * getting the form via forms.get, modifying your local copy of that item to
   * be how you want it, and using UpdateItemRequest to write it back, with the
   * IDs being the same (or not in the field mask).
   *
   * @param Item $item
   */
  public function setItem(Item $item)
  {
    $this->item = $item;
  }
  /**
   * @return Item
   */
  public function getItem()
  {
    return $this->item;
  }
  /**
   * Required. The location identifying the item to update.
   *
   * @param Location $location
   */
  public function setLocation(Location $location)
  {
    $this->location = $location;
  }
  /**
   * @return Location
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Required. Only values named in this mask are changed.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateItemRequest::class, 'Google_Service_Forms_UpdateItemRequest');
