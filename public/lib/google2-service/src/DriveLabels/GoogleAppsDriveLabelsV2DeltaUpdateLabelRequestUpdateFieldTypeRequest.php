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

class GoogleAppsDriveLabelsV2DeltaUpdateLabelRequestUpdateFieldTypeRequest extends \Google\Model
{
  protected $dateOptionsType = GoogleAppsDriveLabelsV2FieldDateOptions::class;
  protected $dateOptionsDataType = '';
  /**
   * Required. The field to update.
   *
   * @var string
   */
  public $id;
  protected $integerOptionsType = GoogleAppsDriveLabelsV2FieldIntegerOptions::class;
  protected $integerOptionsDataType = '';
  protected $selectionOptionsType = GoogleAppsDriveLabelsV2FieldSelectionOptions::class;
  protected $selectionOptionsDataType = '';
  protected $textOptionsType = GoogleAppsDriveLabelsV2FieldTextOptions::class;
  protected $textOptionsDataType = '';
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root of `type_options` is implied and should not be specified. A single
   * `*` can be used as a short-hand for updating every field.
   *
   * @var string
   */
  public $updateMask;
  protected $userOptionsType = GoogleAppsDriveLabelsV2FieldUserOptions::class;
  protected $userOptionsDataType = '';

  /**
   * Update field to Date.
   *
   * @param GoogleAppsDriveLabelsV2FieldDateOptions $dateOptions
   */
  public function setDateOptions(GoogleAppsDriveLabelsV2FieldDateOptions $dateOptions)
  {
    $this->dateOptions = $dateOptions;
  }
  /**
   * @return GoogleAppsDriveLabelsV2FieldDateOptions
   */
  public function getDateOptions()
  {
    return $this->dateOptions;
  }
  /**
   * Required. The field to update.
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
   * Update field to Integer.
   *
   * @param GoogleAppsDriveLabelsV2FieldIntegerOptions $integerOptions
   */
  public function setIntegerOptions(GoogleAppsDriveLabelsV2FieldIntegerOptions $integerOptions)
  {
    $this->integerOptions = $integerOptions;
  }
  /**
   * @return GoogleAppsDriveLabelsV2FieldIntegerOptions
   */
  public function getIntegerOptions()
  {
    return $this->integerOptions;
  }
  /**
   * Update field to Selection.
   *
   * @param GoogleAppsDriveLabelsV2FieldSelectionOptions $selectionOptions
   */
  public function setSelectionOptions(GoogleAppsDriveLabelsV2FieldSelectionOptions $selectionOptions)
  {
    $this->selectionOptions = $selectionOptions;
  }
  /**
   * @return GoogleAppsDriveLabelsV2FieldSelectionOptions
   */
  public function getSelectionOptions()
  {
    return $this->selectionOptions;
  }
  /**
   * Update field to Text.
   *
   * @param GoogleAppsDriveLabelsV2FieldTextOptions $textOptions
   */
  public function setTextOptions(GoogleAppsDriveLabelsV2FieldTextOptions $textOptions)
  {
    $this->textOptions = $textOptions;
  }
  /**
   * @return GoogleAppsDriveLabelsV2FieldTextOptions
   */
  public function getTextOptions()
  {
    return $this->textOptions;
  }
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root of `type_options` is implied and should not be specified. A single
   * `*` can be used as a short-hand for updating every field.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
  /**
   * Update field to User.
   *
   * @param GoogleAppsDriveLabelsV2FieldUserOptions $userOptions
   */
  public function setUserOptions(GoogleAppsDriveLabelsV2FieldUserOptions $userOptions)
  {
    $this->userOptions = $userOptions;
  }
  /**
   * @return GoogleAppsDriveLabelsV2FieldUserOptions
   */
  public function getUserOptions()
  {
    return $this->userOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2DeltaUpdateLabelRequestUpdateFieldTypeRequest::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2DeltaUpdateLabelRequestUpdateFieldTypeRequest');
