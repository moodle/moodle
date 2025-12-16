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

namespace Google\Service\Sheets;

class ManualRuleGroup extends \Google\Collection
{
  protected $collection_key = 'items';
  protected $groupNameType = ExtendedValue::class;
  protected $groupNameDataType = '';
  protected $itemsType = ExtendedValue::class;
  protected $itemsDataType = 'array';

  /**
   * The group name, which must be a string. Each group in a given ManualRule
   * must have a unique group name.
   *
   * @param ExtendedValue $groupName
   */
  public function setGroupName(ExtendedValue $groupName)
  {
    $this->groupName = $groupName;
  }
  /**
   * @return ExtendedValue
   */
  public function getGroupName()
  {
    return $this->groupName;
  }
  /**
   * The items in the source data that should be placed into this group. Each
   * item may be a string, number, or boolean. Items may appear in at most one
   * group within a given ManualRule. Items that do not appear in any group will
   * appear on their own.
   *
   * @param ExtendedValue[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return ExtendedValue[]
   */
  public function getItems()
  {
    return $this->items;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManualRuleGroup::class, 'Google_Service_Sheets_ManualRuleGroup');
