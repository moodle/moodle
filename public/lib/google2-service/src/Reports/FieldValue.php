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

namespace Google\Service\Reports;

class FieldValue extends \Google\Model
{
  protected $dateValueType = Date::class;
  protected $dateValueDataType = '';
  /**
   * Display name of the field
   *
   * @var string
   */
  public $displayName;
  /**
   * Identifier of the field
   *
   * @var string
   */
  public $id;
  /**
   * Setting an integer value.
   *
   * @var string
   */
  public $integerValue;
  /**
   * Setting a long text value.
   *
   * @var string
   */
  public $longTextValue;
  protected $reasonType = Reason::class;
  protected $reasonDataType = '';
  protected $selectionListValueType = FieldValueSelectionListValue::class;
  protected $selectionListValueDataType = '';
  protected $selectionValueType = FieldValueSelectionValue::class;
  protected $selectionValueDataType = '';
  protected $textListValueType = FieldValueTextListValue::class;
  protected $textListValueDataType = '';
  /**
   * Setting a text value.
   *
   * @var string
   */
  public $textValue;
  /**
   * Type of the field
   *
   * @var string
   */
  public $type;
  /**
   * If the field is unset, this will be true.
   *
   * @var bool
   */
  public $unsetValue;
  protected $userListValueType = FieldValueUserListValue::class;
  protected $userListValueDataType = '';
  protected $userValueType = FieldValueUserValue::class;
  protected $userValueDataType = '';

  /**
   * Setting a date value.
   *
   * @param Date $dateValue
   */
  public function setDateValue(Date $dateValue)
  {
    $this->dateValue = $dateValue;
  }
  /**
   * @return Date
   */
  public function getDateValue()
  {
    return $this->dateValue;
  }
  /**
   * Display name of the field
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
   * Identifier of the field
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
   * Setting an integer value.
   *
   * @param string $integerValue
   */
  public function setIntegerValue($integerValue)
  {
    $this->integerValue = $integerValue;
  }
  /**
   * @return string
   */
  public function getIntegerValue()
  {
    return $this->integerValue;
  }
  /**
   * Setting a long text value.
   *
   * @param string $longTextValue
   */
  public function setLongTextValue($longTextValue)
  {
    $this->longTextValue = $longTextValue;
  }
  /**
   * @return string
   */
  public function getLongTextValue()
  {
    return $this->longTextValue;
  }
  /**
   * The reason why the field was applied to the label.
   *
   * @param Reason $reason
   */
  public function setReason(Reason $reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return Reason
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * Setting a selection list value by selecting multiple values from a
   * dropdown.
   *
   * @param FieldValueSelectionListValue $selectionListValue
   */
  public function setSelectionListValue(FieldValueSelectionListValue $selectionListValue)
  {
    $this->selectionListValue = $selectionListValue;
  }
  /**
   * @return FieldValueSelectionListValue
   */
  public function getSelectionListValue()
  {
    return $this->selectionListValue;
  }
  /**
   * Setting a selection value by selecting a single value from a dropdown.
   *
   * @param FieldValueSelectionValue $selectionValue
   */
  public function setSelectionValue(FieldValueSelectionValue $selectionValue)
  {
    $this->selectionValue = $selectionValue;
  }
  /**
   * @return FieldValueSelectionValue
   */
  public function getSelectionValue()
  {
    return $this->selectionValue;
  }
  /**
   * Setting a text list value.
   *
   * @param FieldValueTextListValue $textListValue
   */
  public function setTextListValue(FieldValueTextListValue $textListValue)
  {
    $this->textListValue = $textListValue;
  }
  /**
   * @return FieldValueTextListValue
   */
  public function getTextListValue()
  {
    return $this->textListValue;
  }
  /**
   * Setting a text value.
   *
   * @param string $textValue
   */
  public function setTextValue($textValue)
  {
    $this->textValue = $textValue;
  }
  /**
   * @return string
   */
  public function getTextValue()
  {
    return $this->textValue;
  }
  /**
   * Type of the field
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * If the field is unset, this will be true.
   *
   * @param bool $unsetValue
   */
  public function setUnsetValue($unsetValue)
  {
    $this->unsetValue = $unsetValue;
  }
  /**
   * @return bool
   */
  public function getUnsetValue()
  {
    return $this->unsetValue;
  }
  /**
   * Setting a user list value by selecting multiple users.
   *
   * @param FieldValueUserListValue $userListValue
   */
  public function setUserListValue(FieldValueUserListValue $userListValue)
  {
    $this->userListValue = $userListValue;
  }
  /**
   * @return FieldValueUserListValue
   */
  public function getUserListValue()
  {
    return $this->userListValue;
  }
  /**
   * Setting a user value by selecting a single user.
   *
   * @param FieldValueUserValue $userValue
   */
  public function setUserValue(FieldValueUserValue $userValue)
  {
    $this->userValue = $userValue;
  }
  /**
   * @return FieldValueUserValue
   */
  public function getUserValue()
  {
    return $this->userValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FieldValue::class, 'Google_Service_Reports_FieldValue');
