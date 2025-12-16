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

namespace Google\Service\CloudSearch;

class PropertyDisplayOptions extends \Google\Model
{
  /**
   * The user friendly label for the property that is used if the property is
   * specified to be displayed in ObjectDisplayOptions. If provided, the display
   * label is shown in front of the property values when the property is part of
   * the object display options. For example, if the property value is '1', the
   * value by itself may not be useful context for the user. If the display name
   * given was 'priority', then the user sees 'priority : 1' in the search
   * results which provides clear context to search users. This is OPTIONAL; if
   * not given, only the property values are displayed. The maximum length is 64
   * characters.
   *
   * @var string
   */
  public $displayLabel;

  /**
   * The user friendly label for the property that is used if the property is
   * specified to be displayed in ObjectDisplayOptions. If provided, the display
   * label is shown in front of the property values when the property is part of
   * the object display options. For example, if the property value is '1', the
   * value by itself may not be useful context for the user. If the display name
   * given was 'priority', then the user sees 'priority : 1' in the search
   * results which provides clear context to search users. This is OPTIONAL; if
   * not given, only the property values are displayed. The maximum length is 64
   * characters.
   *
   * @param string $displayLabel
   */
  public function setDisplayLabel($displayLabel)
  {
    $this->displayLabel = $displayLabel;
  }
  /**
   * @return string
   */
  public function getDisplayLabel()
  {
    return $this->displayLabel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PropertyDisplayOptions::class, 'Google_Service_CloudSearch_PropertyDisplayOptions');
