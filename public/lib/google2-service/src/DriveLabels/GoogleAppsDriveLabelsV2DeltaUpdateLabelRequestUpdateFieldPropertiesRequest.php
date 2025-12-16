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

class GoogleAppsDriveLabelsV2DeltaUpdateLabelRequestUpdateFieldPropertiesRequest extends \Google\Model
{
  /**
   * Required. The field to update.
   *
   * @var string
   */
  public $id;
  protected $propertiesType = GoogleAppsDriveLabelsV2FieldProperties::class;
  protected $propertiesDataType = '';
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `properties` is implied and should not be specified. A single `*`
   * can be used as a short-hand for updating every field.
   *
   * @var string
   */
  public $updateMask;

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
   * Required. Basic field properties.
   *
   * @param GoogleAppsDriveLabelsV2FieldProperties $properties
   */
  public function setProperties(GoogleAppsDriveLabelsV2FieldProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return GoogleAppsDriveLabelsV2FieldProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `properties` is implied and should not be specified. A single `*`
   * can be used as a short-hand for updating every field.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2DeltaUpdateLabelRequestUpdateFieldPropertiesRequest::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2DeltaUpdateLabelRequestUpdateFieldPropertiesRequest');
