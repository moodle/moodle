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

namespace Google\Service\DriveLabels;

class GoogleAppsDriveLabelsV2FieldDisplayHints extends \Google\Model
{
  /**
   * Whether the field should be shown in the UI as disabled.
   *
   * @var bool
   */
  public $disabled;
  /**
   * This field should be hidden in the search menu when searching for Drive
   * items.
   *
   * @var bool
   */
  public $hiddenInSearch;
  /**
   * Whether the field should be shown as required in the UI.
   *
   * @var bool
   */
  public $required;
  /**
   * This field should be shown in the apply menu when applying values to a
   * Drive item.
   *
   * @var bool
   */
  public $shownInApply;

  /**
   * Whether the field should be shown in the UI as disabled.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * This field should be hidden in the search menu when searching for Drive
   * items.
   *
   * @param bool $hiddenInSearch
   */
  public function setHiddenInSearch($hiddenInSearch)
  {
    $this->hiddenInSearch = $hiddenInSearch;
  }
  /**
   * @return bool
   */
  public function getHiddenInSearch()
  {
    return $this->hiddenInSearch;
  }
  /**
   * Whether the field should be shown as required in the UI.
   *
   * @param bool $required
   */
  public function setRequired($required)
  {
    $this->required = $required;
  }
  /**
   * @return bool
   */
  public function getRequired()
  {
    return $this->required;
  }
  /**
   * This field should be shown in the apply menu when applying values to a
   * Drive item.
   *
   * @param bool $shownInApply
   */
  public function setShownInApply($shownInApply)
  {
    $this->shownInApply = $shownInApply;
  }
  /**
   * @return bool
   */
  public function getShownInApply()
  {
    return $this->shownInApply;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2FieldDisplayHints::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2FieldDisplayHints');
