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

class LayoutProperties extends \Google\Model
{
  /**
   * The human-readable name of the layout.
   *
   * @var string
   */
  public $displayName;
  /**
   * The object ID of the master that this layout is based on.
   *
   * @var string
   */
  public $masterObjectId;
  /**
   * The name of the layout.
   *
   * @var string
   */
  public $name;

  /**
   * The human-readable name of the layout.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The object ID of the master that this layout is based on.
   *
   * @param string $masterObjectId
   */
  public function setMasterObjectId($masterObjectId)
  {
    $this->masterObjectId = $masterObjectId;
  }
  /**
   * @return string
   */
  public function getMasterObjectId()
  {
    return $this->masterObjectId;
  }
  /**
   * The name of the layout.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LayoutProperties::class, 'Google_Service_Slides_LayoutProperties');
