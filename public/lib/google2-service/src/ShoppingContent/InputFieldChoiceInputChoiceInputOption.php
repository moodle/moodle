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

class InputFieldChoiceInputChoiceInputOption extends \Google\Model
{
  protected $additionalInputType = InputField::class;
  protected $additionalInputDataType = '';
  /**
   * Not for display but need to be sent back for the selected choice option.
   *
   * @var string
   */
  public $id;
  protected $labelType = TextWithTooltip::class;
  protected $labelDataType = '';

  /**
   * Input that should be displayed when this option is selected. The additional
   * input will not contain a `ChoiceInput`.
   *
   * @param InputField $additionalInput
   */
  public function setAdditionalInput(InputField $additionalInput)
  {
    $this->additionalInput = $additionalInput;
  }
  /**
   * @return InputField
   */
  public function getAdditionalInput()
  {
    return $this->additionalInput;
  }
  /**
   * Not for display but need to be sent back for the selected choice option.
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
   * Short description of the choice option. There may be more information to be
   * shown as a tooltip.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InputFieldChoiceInputChoiceInputOption::class, 'Google_Service_ShoppingContent_InputFieldChoiceInputChoiceInputOption');
