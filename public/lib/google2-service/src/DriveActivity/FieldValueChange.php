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

namespace Google\Service\DriveActivity;

class FieldValueChange extends \Google\Model
{
  /**
   * The human-readable display name for this field.
   *
   * @var string
   */
  public $displayName;
  /**
   * The ID of this field. Field IDs are unique within a Label.
   *
   * @var string
   */
  public $fieldId;
  protected $newValueType = FieldValue::class;
  protected $newValueDataType = '';
  protected $oldValueType = FieldValue::class;
  protected $oldValueDataType = '';

  /**
   * The human-readable display name for this field.
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
   * The ID of this field. Field IDs are unique within a Label.
   *
   * @param string $fieldId
   */
  public function setFieldId($fieldId)
  {
    $this->fieldId = $fieldId;
  }
  /**
   * @return string
   */
  public function getFieldId()
  {
    return $this->fieldId;
  }
  /**
   * The value that is now set on the field. If not present, the field was
   * cleared. At least one of {old_value|new_value} is always set.
   *
   * @param FieldValue $newValue
   */
  public function setNewValue(FieldValue $newValue)
  {
    $this->newValue = $newValue;
  }
  /**
   * @return FieldValue
   */
  public function getNewValue()
  {
    return $this->newValue;
  }
  /**
   * The value that was previously set on the field. If not present, the field
   * was newly set. At least one of {old_value|new_value} is always set.
   *
   * @param FieldValue $oldValue
   */
  public function setOldValue(FieldValue $oldValue)
  {
    $this->oldValue = $oldValue;
  }
  /**
   * @return FieldValue
   */
  public function getOldValue()
  {
    return $this->oldValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FieldValueChange::class, 'Google_Service_DriveActivity_FieldValueChange');
