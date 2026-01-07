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

class LayoutPlaceholderIdMapping extends \Google\Model
{
  protected $layoutPlaceholderType = Placeholder::class;
  protected $layoutPlaceholderDataType = '';
  /**
   * The object ID of the placeholder on a layout that will be applied to a
   * slide.
   *
   * @var string
   */
  public $layoutPlaceholderObjectId;
  /**
   * A user-supplied object ID for the placeholder identified above that to be
   * created onto a slide. If you specify an ID, it must be unique among all
   * pages and page elements in the presentation. The ID must start with an
   * alphanumeric character or an underscore (matches regex `[a-zA-Z0-9_]`);
   * remaining characters may include those as well as a hyphen or colon
   * (matches regex `[a-zA-Z0-9_-:]`). The length of the ID must not be less
   * than 5 or greater than 50. If you don't specify an ID, a unique one is
   * generated.
   *
   * @var string
   */
  public $objectId;

  /**
   * The placeholder on a layout that will be applied to a slide. Only type and
   * index are needed. For example, a predefined `TITLE_AND_BODY` layout may
   * usually have a TITLE placeholder with index 0 and a BODY placeholder with
   * index 0.
   *
   * @param Placeholder $layoutPlaceholder
   */
  public function setLayoutPlaceholder(Placeholder $layoutPlaceholder)
  {
    $this->layoutPlaceholder = $layoutPlaceholder;
  }
  /**
   * @return Placeholder
   */
  public function getLayoutPlaceholder()
  {
    return $this->layoutPlaceholder;
  }
  /**
   * The object ID of the placeholder on a layout that will be applied to a
   * slide.
   *
   * @param string $layoutPlaceholderObjectId
   */
  public function setLayoutPlaceholderObjectId($layoutPlaceholderObjectId)
  {
    $this->layoutPlaceholderObjectId = $layoutPlaceholderObjectId;
  }
  /**
   * @return string
   */
  public function getLayoutPlaceholderObjectId()
  {
    return $this->layoutPlaceholderObjectId;
  }
  /**
   * A user-supplied object ID for the placeholder identified above that to be
   * created onto a slide. If you specify an ID, it must be unique among all
   * pages and page elements in the presentation. The ID must start with an
   * alphanumeric character or an underscore (matches regex `[a-zA-Z0-9_]`);
   * remaining characters may include those as well as a hyphen or colon
   * (matches regex `[a-zA-Z0-9_-:]`). The length of the ID must not be less
   * than 5 or greater than 50. If you don't specify an ID, a unique one is
   * generated.
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
class_alias(LayoutPlaceholderIdMapping::class, 'Google_Service_Slides_LayoutPlaceholderIdMapping');
