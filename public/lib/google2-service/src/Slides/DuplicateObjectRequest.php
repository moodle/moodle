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

class DuplicateObjectRequest extends \Google\Model
{
  /**
   * The ID of the object to duplicate.
   *
   * @var string
   */
  public $objectId;
  /**
   * The object being duplicated may contain other objects, for example when
   * duplicating a slide or a group page element. This map defines how the IDs
   * of duplicated objects are generated: the keys are the IDs of the original
   * objects and its values are the IDs that will be assigned to the
   * corresponding duplicate object. The ID of the source object's duplicate may
   * be specified in this map as well, using the same value of the `object_id`
   * field as a key and the newly desired ID as the value. All keys must
   * correspond to existing IDs in the presentation. All values must be unique
   * in the presentation and must start with an alphanumeric character or an
   * underscore (matches regex `[a-zA-Z0-9_]`); remaining characters may include
   * those as well as a hyphen or colon (matches regex `[a-zA-Z0-9_-:]`). The
   * length of the new ID must not be less than 5 or greater than 50. If any IDs
   * of source objects are omitted from the map, a new random ID will be
   * assigned. If the map is empty or unset, all duplicate objects will receive
   * a new random ID.
   *
   * @var string[]
   */
  public $objectIds;

  /**
   * The ID of the object to duplicate.
   *
   * @param string $objectId
   */
  public function setObjectId($objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return string
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
  /**
   * The object being duplicated may contain other objects, for example when
   * duplicating a slide or a group page element. This map defines how the IDs
   * of duplicated objects are generated: the keys are the IDs of the original
   * objects and its values are the IDs that will be assigned to the
   * corresponding duplicate object. The ID of the source object's duplicate may
   * be specified in this map as well, using the same value of the `object_id`
   * field as a key and the newly desired ID as the value. All keys must
   * correspond to existing IDs in the presentation. All values must be unique
   * in the presentation and must start with an alphanumeric character or an
   * underscore (matches regex `[a-zA-Z0-9_]`); remaining characters may include
   * those as well as a hyphen or colon (matches regex `[a-zA-Z0-9_-:]`). The
   * length of the new ID must not be less than 5 or greater than 50. If any IDs
   * of source objects are omitted from the map, a new random ID will be
   * assigned. If the map is empty or unset, all duplicate objects will receive
   * a new random ID.
   *
   * @param string[] $objectIds
   */
  public function setObjectIds($objectIds)
  {
    $this->objectIds = $objectIds;
  }
  /**
   * @return string[]
   */
  public function getObjectIds()
  {
    return $this->objectIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DuplicateObjectRequest::class, 'Google_Service_Slides_DuplicateObjectRequest');
