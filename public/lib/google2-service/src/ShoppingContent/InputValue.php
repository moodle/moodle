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

namespace Google\Service\ShoppingContent;

class InputValue extends \Google\Model
{
  protected $checkboxInputValueType = InputValueCheckboxInputValue::class;
  protected $checkboxInputValueDataType = '';
  protected $choiceInputValueType = InputValueChoiceInputValue::class;
  protected $choiceInputValueDataType = '';
  /**
   * Required. Id of the corresponding input field.
   *
   * @var string
   */
  public $inputFieldId;
  protected $textInputValueType = InputValueTextInputValue::class;
  protected $textInputValueDataType = '';

  /**
   * Value for checkbox input field.
   *
   * @param InputValueCheckboxInputValue $checkboxInputValue
   */
  public function setCheckboxInputValue(InputValueCheckboxInputValue $checkboxInputValue)
  {
    $this->checkboxInputValue = $checkboxInputValue;
  }
  /**
   * @return InputValueCheckboxInputValue
   */
  public function getCheckboxInputValue()
  {
    return $this->checkboxInputValue;
  }
  /**
   * Value for choice input field.
   *
   * @param InputValueChoiceInputValue $choiceInputValue
   */
  public function setChoiceInputValue(InputValueChoiceInputValue $choiceInputValue)
  {
    $this->choiceInputValue = $choiceInputValue;
  }
  /**
   * @return InputValueChoiceInputValue
   */
  public function getChoiceInputValue()
  {
    return $this->choiceInputValue;
  }
  /**
   * Required. Id of the corresponding input field.
   *
   * @param string $inputFieldId
   */
  public function setInputFieldId($inputFieldId)
  {
    $this->inputFieldId = $inputFieldId;
  }
  /**
   * @return string
   */
  public function getInputFieldId()
  {
    return $this->inputFieldId;
  }
  /**
   * Value for text input field.
   *
   * @param InputValueTextInputValue $textInputValue
   */
  public function setTextInputValue(InputValueTextInputValue $textInputValue)
  {
    $this->textInputValue = $textInputValue;
  }
  /**
   * @return InputValueTextInputValue
   */
  public function getTextInputValue()
  {
    return $this->textInputValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InputValue::class, 'Google_Service_ShoppingContent_InputValue');
