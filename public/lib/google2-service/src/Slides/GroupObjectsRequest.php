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

class GroupObjectsRequest extends \Google\Collection
{
  protected $collection_key = 'childrenObjectIds';
  /**
   * The object IDs of the objects to group. Only page elements can be grouped.
   * There should be at least two page elements on the same page that are not
   * already in another group. Some page elements, such as videos, tables and
   * placeholders cannot be grouped.
   *
   * @var string[]
   */
  public $childrenObjectIds;
  /**
   * A user-supplied object ID for the group to be created. If you specify an
   * ID, it must be unique among all pages and page elements in the
   * presentation. The ID must start with an alphanumeric character or an
   * underscore (matches regex `[a-zA-Z0-9_]`); remaining characters may include
   * those as well as a hyphen or colon (matches regex `[a-zA-Z0-9_-:]`). The
   * length of the ID must not be less than 5 or greater than 50. If you don't
   * specify an ID, a unique one is generated.
   *
   * @var string
   */
  public $groupObjectId;

  /**
   * The object IDs of the objects to group. Only page elements can be grouped.
   * There should be at least two page elements on the same page that are not
   * already in another group. Some page elements, such as videos, tables and
   * placeholders cannot be grouped.
   *
   * @param string[] $childrenObjectIds
   */
  public function setChildrenObjectIds($childrenObjectIds)
  {
    $this->childrenObjectIds = $childrenObjectIds;
  }
  /**
   * @return string[]
   */
  public function getChildrenObjectIds()
  {
    return $this->childrenObjectIds;
  }
  /**
   * A user-supplied object ID for the group to be created. If you specify an
   * ID, it must be unique among all pages and page elements in the
   * presentation. The ID must start with an alphanumeric character or an
   * underscore (matches regex `[a-zA-Z0-9_]`); remaining characters may include
   * those as well as a hyphen or colon (matches regex `[a-zA-Z0-9_-:]`). The
   * length of the ID must not be less than 5 or greater than 50. If you don't
   * specify an ID, a unique one is generated.
   *
   * @param string $groupObjectId
   */
  public function setGroupObjectId($groupObjectId)
  {
    $this->groupObjectId = $groupObjectId;
  }
  /**
   * @return string
   */
  public function getGroupObjectId()
  {
    return $this->groupObjectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GroupObjectsRequest::class, 'Google_Service_Slides_GroupObjectsRequest');
