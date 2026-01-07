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

class GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseResponse extends \Google\Model
{
  protected $createFieldType = GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseCreateFieldResponse::class;
  protected $createFieldDataType = '';
  protected $createSelectionChoiceType = GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseCreateSelectionChoiceResponse::class;
  protected $createSelectionChoiceDataType = '';
  protected $deleteFieldType = GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseDeleteFieldResponse::class;
  protected $deleteFieldDataType = '';
  protected $deleteSelectionChoiceType = GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseDeleteSelectionChoiceResponse::class;
  protected $deleteSelectionChoiceDataType = '';
  protected $disableFieldType = GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseDisableFieldResponse::class;
  protected $disableFieldDataType = '';
  protected $disableSelectionChoiceType = GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseDisableSelectionChoiceResponse::class;
  protected $disableSelectionChoiceDataType = '';
  protected $enableFieldType = GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseEnableFieldResponse::class;
  protected $enableFieldDataType = '';
  protected $enableSelectionChoiceType = GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseEnableSelectionChoiceResponse::class;
  protected $enableSelectionChoiceDataType = '';
  protected $updateFieldDataType = '';
  protected $updateFieldTypeType = GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseUpdateFieldTypeResponse::class;
  protected $updateFieldTypeDataType = '';
  protected $updateLabelType = GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseUpdateLabelPropertiesResponse::class;
  protected $updateLabelDataType = '';
  protected $updateSelectionChoicePropertiesType = GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseUpdateSelectionChoicePropertiesResponse::class;
  protected $updateSelectionChoicePropertiesDataType = '';

  /**
   * Creates a field.
   *
   * @param GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseCreateFieldResponse $createField
   */
  public function setCreateField(GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseCreateFieldResponse $createField)
  {
    $this->createField = $createField;
  }
  /**
   * @return GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseCreateFieldResponse
   */
  public function getCreateField()
  {
    return $this->createField;
  }
  /**
   * Creates a selection list option to add to a selection field.
   *
   * @param GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseCreateSelectionChoiceResponse $createSelectionChoice
   */
  public function setCreateSelectionChoice(GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseCreateSelectionChoiceResponse $createSelectionChoice)
  {
    $this->createSelectionChoice = $createSelectionChoice;
  }
  /**
   * @return GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseCreateSelectionChoiceResponse
   */
  public function getCreateSelectionChoice()
  {
    return $this->createSelectionChoice;
  }
  /**
   * Deletes a field from the label.
   *
   * @param GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseDeleteFieldResponse $deleteField
   */
  public function setDeleteField(GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseDeleteFieldResponse $deleteField)
  {
    $this->deleteField = $deleteField;
  }
  /**
   * @return GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseDeleteFieldResponse
   */
  public function getDeleteField()
  {
    return $this->deleteField;
  }
  /**
   * Deletes a choice from a selection field.
   *
   * @param GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseDeleteSelectionChoiceResponse $deleteSelectionChoice
   */
  public function setDeleteSelectionChoice(GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseDeleteSelectionChoiceResponse $deleteSelectionChoice)
  {
    $this->deleteSelectionChoice = $deleteSelectionChoice;
  }
  /**
   * @return GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseDeleteSelectionChoiceResponse
   */
  public function getDeleteSelectionChoice()
  {
    return $this->deleteSelectionChoice;
  }
  /**
   * Disables field.
   *
   * @param GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseDisableFieldResponse $disableField
   */
  public function setDisableField(GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseDisableFieldResponse $disableField)
  {
    $this->disableField = $disableField;
  }
  /**
   * @return GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseDisableFieldResponse
   */
  public function getDisableField()
  {
    return $this->disableField;
  }
  /**
   * Disables a choice within a selection field.
   *
   * @param GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseDisableSelectionChoiceResponse $disableSelectionChoice
   */
  public function setDisableSelectionChoice(GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseDisableSelectionChoiceResponse $disableSelectionChoice)
  {
    $this->disableSelectionChoice = $disableSelectionChoice;
  }
  /**
   * @return GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseDisableSelectionChoiceResponse
   */
  public function getDisableSelectionChoice()
  {
    return $this->disableSelectionChoice;
  }
  /**
   * Enables field.
   *
   * @param GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseEnableFieldResponse $enableField
   */
  public function setEnableField(GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseEnableFieldResponse $enableField)
  {
    $this->enableField = $enableField;
  }
  /**
   * @return GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseEnableFieldResponse
   */
  public function getEnableField()
  {
    return $this->enableField;
  }
  /**
   * Enables a choice within a selection field.
   *
   * @param GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseEnableSelectionChoiceResponse $enableSelectionChoice
   */
  public function setEnableSelectionChoice(GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseEnableSelectionChoiceResponse $enableSelectionChoice)
  {
    $this->enableSelectionChoice = $enableSelectionChoice;
  }
  /**
   * @return GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseEnableSelectionChoiceResponse
   */
  public function getEnableSelectionChoice()
  {
    return $this->enableSelectionChoice;
  }
  /**
   * Updates basic properties of a field.
   *
   * @param GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseUpdateFieldPropertiesResponse $updateField
   */
  public function setUpdateField(GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseUpdateFieldPropertiesResponse $updateField)
  {
    $this->updateField = $updateField;
  }
  /**
   * @return GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseUpdateFieldPropertiesResponse
   */
  public function getUpdateField()
  {
    return $this->updateField;
  }
  /**
   * Updates field type and/or type options.
   *
   * @param GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseUpdateFieldTypeResponse $updateFieldType
   */
  public function setUpdateFieldType(GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseUpdateFieldTypeResponse $updateFieldType)
  {
    $this->updateFieldType = $updateFieldType;
  }
  /**
   * @return GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseUpdateFieldTypeResponse
   */
  public function getUpdateFieldType()
  {
    return $this->updateFieldType;
  }
  /**
   * Updates basic properties of a label.
   *
   * @param GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseUpdateLabelPropertiesResponse $updateLabel
   */
  public function setUpdateLabel(GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseUpdateLabelPropertiesResponse $updateLabel)
  {
    $this->updateLabel = $updateLabel;
  }
  /**
   * @return GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseUpdateLabelPropertiesResponse
   */
  public function getUpdateLabel()
  {
    return $this->updateLabel;
  }
  /**
   * Updates a choice within a selection field.
   *
   * @param GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseUpdateSelectionChoicePropertiesResponse $updateSelectionChoiceProperties
   */
  public function setUpdateSelectionChoiceProperties(GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseUpdateSelectionChoicePropertiesResponse $updateSelectionChoiceProperties)
  {
    $this->updateSelectionChoiceProperties = $updateSelectionChoiceProperties;
  }
  /**
   * @return GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseUpdateSelectionChoicePropertiesResponse
   */
  public function getUpdateSelectionChoiceProperties()
  {
    return $this->updateSelectionChoiceProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseResponse::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2DeltaUpdateLabelResponseResponse');
