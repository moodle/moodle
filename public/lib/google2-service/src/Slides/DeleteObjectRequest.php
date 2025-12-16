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

class DeleteObjectRequest extends \Google\Model
{
  /**
   * The object ID of the page or page element to delete. If after a delete
   * operation a group contains only 1 or no page elements, the group is also
   * deleted. If a placeholder is deleted on a layout, any empty inheriting
   * placeholders are also deleted.
   *
   * @var string
   */
  public $objectId;

  /**
   * The object ID of the page or page element to delete. If after a delete
   * operation a group contains only 1 or no page elements, the group is also
   * deleted. If a placeholder is deleted on a layout, any empty inheriting
   * placeholders are also deleted.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeleteObjectRequest::class, 'Google_Service_Slides_DeleteObjectRequest');
