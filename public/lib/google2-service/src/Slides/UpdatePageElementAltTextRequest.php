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

class UpdatePageElementAltTextRequest extends \Google\Model
{
  /**
   * The updated alt text description of the page element. If unset the existing
   * value will be maintained. The description is exposed to screen readers and
   * other accessibility interfaces. Only use human readable values related to
   * the content of the page element.
   *
   * @var string
   */
  public $description;
  /**
   * The object ID of the page element the updates are applied to.
   *
   * @var string
   */
  public $objectId;
  /**
   * The updated alt text title of the page element. If unset the existing value
   * will be maintained. The title is exposed to screen readers and other
   * accessibility interfaces. Only use human readable values related to the
   * content of the page element.
   *
   * @var string
   */
  public $title;

  /**
   * The updated alt text description of the page element. If unset the existing
   * value will be maintained. The description is exposed to screen readers and
   * other accessibility interfaces. Only use human readable values related to
   * the content of the page element.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The object ID of the page element the updates are applied to.
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
   * The updated alt text title of the page element. If unset the existing value
   * will be maintained. The title is exposed to screen readers and other
   * accessibility interfaces. Only use human readable values related to the
   * content of the page element.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdatePageElementAltTextRequest::class, 'Google_Service_Slides_UpdatePageElementAltTextRequest');
