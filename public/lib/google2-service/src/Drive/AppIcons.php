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

class AppIcons extends \Google\Model
{
  /**
   * Category of the icon. Allowed values are: * `application` - The icon for
   * the application. * `document` - The icon for a file associated with the
   * app. * `documentShared` - The icon for a shared file associated with the
   * app.
   *
   * @var string
   */
  public $category;
  /**
   * URL for the icon.
   *
   * @var string
   */
  public $iconUrl;
  /**
   * Size of the icon. Represented as the maximum of the width and height.
   *
   * @var int
   */
  public $size;

  /**
   * Category of the icon. Allowed values are: * `application` - The icon for
   * the application. * `document` - The icon for a file associated with the
   * app. * `documentShared` - The icon for a shared file associated with the
   * app.
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * URL for the icon.
   *
   * @param string $iconUrl
   */
  public function setIconUrl($iconUrl)
  {
    $this->iconUrl = $iconUrl;
  }
  /**
   * @return string
   */
  public function getIconUrl()
  {
    return $this->iconUrl;
  }
  /**
   * Size of the icon. Represented as the maximum of the width and height.
   *
   * @param int $size
   */
  public function setSize($size)
  {
    $this->size = $size;
  }
  /**
   * @return int
   */
  public function getSize()
  {
    return $this->size;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppIcons::class, 'Google_Service_Drive_AppIcons');
