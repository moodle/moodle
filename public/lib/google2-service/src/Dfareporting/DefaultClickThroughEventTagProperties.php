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

namespace Google\Service\Dfareporting;

class DefaultClickThroughEventTagProperties extends \Google\Model
{
  /**
   * ID of the click-through event tag to apply to all ads in this entity's
   * scope.
   *
   * @var string
   */
  public $defaultClickThroughEventTagId;
  /**
   * Whether this entity should override the inherited default click-through
   * event tag with its own defined value.
   *
   * @var bool
   */
  public $overrideInheritedEventTag;

  /**
   * ID of the click-through event tag to apply to all ads in this entity's
   * scope.
   *
   * @param string $defaultClickThroughEventTagId
   */
  public function setDefaultClickThroughEventTagId($defaultClickThroughEventTagId)
  {
    $this->defaultClickThroughEventTagId = $defaultClickThroughEventTagId;
  }
  /**
   * @return string
   */
  public function getDefaultClickThroughEventTagId()
  {
    return $this->defaultClickThroughEventTagId;
  }
  /**
   * Whether this entity should override the inherited default click-through
   * event tag with its own defined value.
   *
   * @param bool $overrideInheritedEventTag
   */
  public function setOverrideInheritedEventTag($overrideInheritedEventTag)
  {
    $this->overrideInheritedEventTag = $overrideInheritedEventTag;
  }
  /**
   * @return bool
   */
  public function getOverrideInheritedEventTag()
  {
    return $this->overrideInheritedEventTag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DefaultClickThroughEventTagProperties::class, 'Google_Service_Dfareporting_DefaultClickThroughEventTagProperties');
