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

class InputField extends \Google\Model
{
  protected $checkboxInputType = InputFieldCheckboxInput::class;
  protected $checkboxInputDataType = '';
  protected $choiceInputType = InputFieldChoiceInput::class;
  protected $choiceInputDataType = '';
  /**
   * Not for display but need to be sent back for the given input field.
   *
   * @var string
   */
  public $id;
  protected $labelType = TextWithTooltip::class;
  protected $labelDataType = '';
  /**
   * Whether the field is required. The action button needs to stay disabled
   * till values for all required fields are provided.
   *
   * @var bool
   */
  public $required;
  protected $textInputType = InputFieldTextInput::class;
  protected $textInputDataType = '';

  /**
   * Input field to provide a boolean value. Corresponds to the [html input
   * type=checkbox](https://www.w3.org/TR/2012/WD-html-
   * markup-20121025/input.checkbox.html#input.checkbox).
   *
   * @param InputFieldCheckboxInput $checkboxInput
   */
  public function setCheckboxInput(InputFieldCheckboxInput $checkboxInput)
  {
    $this->checkboxInput = $checkboxInput;
  }
  /**
   * @return InputFieldCheckboxInput
   */
  public function getCheckboxInput()
  {
    return $this->checkboxInput;
  }
  /**
   * Input field to select one of the offered choices. Corresponds to the [html
   * input type=radio](https://www.w3.org/TR/2012/WD-html-
   * markup-20121025/input.radio.html#input.radio).
   *
   * @param InputFieldChoiceInput $choiceInput
   */
  public function setChoiceInput(InputFieldChoiceInput $choiceInput)
  {
    $this->choiceInput = $choiceInput;
  }
  /**
   * @return InputFieldChoiceInput
   */
  public function getChoiceInput()
  {
    return $this->choiceInput;
  }
  /**
   * Not for display but need to be sent back for the given input field.
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
   * Input field label. There may be more information to be shown in a tooltip.
   *
   * @param TextWithTooltip $label
   */
  public function setLabel(TextWithTooltip $label)
  {
    $this->label = $label;
  }
  /**
   * @return TextWithTooltip
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * Whether the field is required. The action button needs to stay disabled
   * till values for all required fields are provided.
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
   * Input field to provide text information. Corresponds to the [html input
   * type=text](https://www.w3.org/TR/2012/WD-html-
   * markup-20121025/input.text.html#input.text) or [html
   * textarea](https://www.w3.org/TR/2012/WD-html-
   * markup-20121025/textarea.html#textarea).
   *
   * @param InputFieldTextInput $textInput
   */
  public function setTextInput(InputFieldTextInput $textInput)
  {
    $this->textInput = $textInput;
  }
  /**
   * @return InputFieldTextInput
   */
  public function getTextInput()
  {
    return $this->textInput;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InputField::class, 'Google_Service_ShoppingContent_InputField');
