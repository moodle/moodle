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

class GoogleAppsDriveLabelsV2FieldProperties extends \Google\Model
{
  /**
   * Required. The display text to show in the UI identifying this field.
   *
   * @var string
   */
  public $displayName;
  /**
   * Input only. Insert or move this field before the indicated field. If empty,
   * the field is placed at the end of the list.
   *
   * @var string
   */
  public $insertBeforeField;
  /**
   * Whether the field should be marked as required.
   *
   * @var bool
   */
  public $required;

  /**
   * Required. The display text to show in the UI identifying this field.
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
   * Input only. Insert or move this field before the indicated field. If empty,
   * the field is placed at the end of the list.
   *
   * @param string $insertBeforeField
   */
  public function setInsertBeforeField($insertBeforeField)
  {
    $this->insertBeforeField = $insertBeforeField;
  }
  /**
   * @return string
   */
  public function getInsertBeforeField()
  {
    return $this->insertBeforeField;
  }
  /**
   * Whether the field should be marked as required.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2FieldProperties::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2FieldProperties');
