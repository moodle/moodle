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

namespace Google\Service\PagespeedInsights;

class StackPack extends \Google\Model
{
  /**
   * The stack pack advice strings.
   *
   * @var string[]
   */
  public $descriptions;
  /**
   * The stack pack icon data uri.
   *
   * @var string
   */
  public $iconDataURL;
  /**
   * The stack pack id.
   *
   * @var string
   */
  public $id;
  /**
   * The stack pack title.
   *
   * @var string
   */
  public $title;

  /**
   * The stack pack advice strings.
   *
   * @param string[] $descriptions
   */
  public function setDescriptions($descriptions)
  {
    $this->descriptions = $descriptions;
  }
  /**
   * @return string[]
   */
  public function getDescriptions()
  {
    return $this->descriptions;
  }
  /**
   * The stack pack icon data uri.
   *
   * @param string $iconDataURL
   */
  public function setIconDataURL($iconDataURL)
  {
    $this->iconDataURL = $iconDataURL;
  }
  /**
   * @return string
   */
  public function getIconDataURL()
  {
    return $this->iconDataURL;
  }
  /**
   * The stack pack id.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The stack pack title.
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
class_alias(StackPack::class, 'Google_Service_PagespeedInsights_StackPack');
