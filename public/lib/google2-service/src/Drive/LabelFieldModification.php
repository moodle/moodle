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

namespace Google\Service\Drive;

class LabelFieldModification extends \Google\Collection
{
  protected $collection_key = 'setUserValues';
  /**
   * The ID of the field to be modified.
   *
   * @var string
   */
  public $fieldId;
  /**
   * This is always `"drive#labelFieldModification"`.
   *
   * @var string
   */
  public $kind;
  /**
   * Replaces the value of a dateString Field with these new values. The string
   * must be in the RFC 3339 full-date format: YYYY-MM-DD.
   *
   * @var string[]
   */
  public $setDateValues;
  /**
   * Replaces the value of an `integer` field with these new values.
   *
   * @var string[]
   */
  public $setIntegerValues;
  /**
   * Replaces a `selection` field with these new values.
   *
   * @var string[]
   */
  public $setSelectionValues;
  /**
   * Sets the value of a `text` field.
   *
   * @var string[]
   */
  public $setTextValues;
  /**
   * Replaces a `user` field with these new values. The values must be a valid
   * email addresses.
   *
   * @var string[]
   */
  public $setUserValues;
  /**
   * Unsets the values for this field.
   *
   * @var bool
   */
  public $unsetValues;

  /**
   * The ID of the field to be modified.
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
   * This is always `"drive#labelFieldModification"`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Replaces the value of a dateString Field with these new values. The string
   * must be in the RFC 3339 full-date format: YYYY-MM-DD.
   *
   * @param string[] $setDateValues
   */
  public function setSetDateValues($setDateValues)
  {
    $this->setDateValues = $setDateValues;
  }
  /**
   * @return string[]
   */
  public function getSetDateValues()
  {
    return $this->setDateValues;
  }
  /**
   * Replaces the value of an `integer` field with these new values.
   *
   * @param string[] $setIntegerValues
   */
  public function setSetIntegerValues($setIntegerValues)
  {
    $this->setIntegerValues = $setIntegerValues;
  }
  /**
   * @return string[]
   */
  public function getSetIntegerValues()
  {
    return $this->setIntegerValues;
  }
  /**
   * Replaces a `selection` field with these new values.
   *
   * @param string[] $setSelectionValues
   */
  public function setSetSelectionValues($setSelectionValues)
  {
    $this->setSelectionValues = $setSelectionValues;
  }
  /**
   * @return string[]
   */
  public function getSetSelectionValues()
  {
    return $this->setSelectionValues;
  }
  /**
   * Sets the value of a `text` field.
   *
   * @param string[] $setTextValues
   */
  public function setSetTextValues($setTextValues)
  {
    $this->setTextValues = $setTextValues;
  }
  /**
   * @return string[]
   */
  public function getSetTextValues()
  {
    return $this->setTextValues;
  }
  /**
   * Replaces a `user` field with these new values. The values must be a valid
   * email addresses.
   *
   * @param string[] $setUserValues
   */
  public function setSetUserValues($setUserValues)
  {
    $this->setUserValues = $setUserValues;
  }
  /**
   * @return string[]
   */
  public function getSetUserValues()
  {
    return $this->setUserValues;
  }
  /**
   * Unsets the values for this field.
   *
   * @param bool $unsetValues
   */
  public function setUnsetValues($unsetValues)
  {
    $this->unsetValues = $unsetValues;
  }
  /**
   * @return bool
   */
  public function getUnsetValues()
  {
    return $this->unsetValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LabelFieldModification::class, 'Google_Service_Drive_LabelFieldModification');
