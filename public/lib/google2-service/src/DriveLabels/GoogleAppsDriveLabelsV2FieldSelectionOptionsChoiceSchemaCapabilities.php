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

class GoogleAppsDriveLabelsV2FieldSelectionOptionsChoiceSchemaCapabilities extends \Google\Model
{
  /**
   * Whether the user can delete this choice.
   *
   * @var bool
   */
  public $canDelete;
  /**
   * Whether the user can disable this choice.
   *
   * @var bool
   */
  public $canDisable;
  /**
   * Whether the user can enable this choice.
   *
   * @var bool
   */
  public $canEnable;
  /**
   * Whether the user can update this choice.
   *
   * @var bool
   */
  public $canUpdate;

  /**
   * Whether the user can delete this choice.
   *
   * @param bool $canDelete
   */
  public function setCanDelete($canDelete)
  {
    $this->canDelete = $canDelete;
  }
  /**
   * @return bool
   */
  public function getCanDelete()
  {
    return $this->canDelete;
  }
  /**
   * Whether the user can disable this choice.
   *
   * @param bool $canDisable
   */
  public function setCanDisable($canDisable)
  {
    $this->canDisable = $canDisable;
  }
  /**
   * @return bool
   */
  public function getCanDisable()
  {
    return $this->canDisable;
  }
  /**
   * Whether the user can enable this choice.
   *
   * @param bool $canEnable
   */
  public function setCanEnable($canEnable)
  {
    $this->canEnable = $canEnable;
  }
  /**
   * @return bool
   */
  public function getCanEnable()
  {
    return $this->canEnable;
  }
  /**
   * Whether the user can update this choice.
   *
   * @param bool $canUpdate
   */
  public function setCanUpdate($canUpdate)
  {
    $this->canUpdate = $canUpdate;
  }
  /**
   * @return bool
   */
  public function getCanUpdate()
  {
    return $this->canUpdate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2FieldSelectionOptionsChoiceSchemaCapabilities::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2FieldSelectionOptionsChoiceSchemaCapabilities');
