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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoParamSpecEntryConfig extends \Google\Model
{
  /**
   * A single-line input for strings, a numeric input box for numbers, or a
   * checkbox for booleans.
   */
  public const INPUT_DISPLAY_OPTION_DEFAULT = 'DEFAULT';
  /**
   * A multi-line input box for longer strings/string templates.
   */
  public const INPUT_DISPLAY_OPTION_STRING_MULTI_LINE = 'STRING_MULTI_LINE';
  /**
   * A slider to select a numerical value. The default range is [0, 100].
   */
  public const INPUT_DISPLAY_OPTION_NUMBER_SLIDER = 'NUMBER_SLIDER';
  /**
   * A toggle button for boolean parameters.
   */
  public const INPUT_DISPLAY_OPTION_BOOLEAN_TOGGLE = 'BOOLEAN_TOGGLE';
  /**
   * This field is not a parameter name.
   */
  public const PARAMETER_NAME_OPTION_DEFAULT_NOT_PARAMETER_NAME = 'DEFAULT_NOT_PARAMETER_NAME';
  /**
   * If this field is a string and this option is selected, the field will be
   * interpreted as a parameter name. Users will be able to choose a variable
   * using the autocomplete, but the name will be stored as a literal string.
   */
  public const PARAMETER_NAME_OPTION_IS_PARAMETER_NAME = 'IS_PARAMETER_NAME';
  /**
   * If this field is a ParameterMap and this option is selected, the map's keys
   * will be interpreted as parameter names. Ignored if this field is not a
   * ParameterMap.
   */
  public const PARAMETER_NAME_OPTION_KEY_IS_PARAMETER_NAME = 'KEY_IS_PARAMETER_NAME';
  /**
   * If this field is a ParameterMap and this option is selected, the map's
   * values will be interpreted as parameter names. Ignored if this field is not
   * a ParameterMap.
   */
  public const PARAMETER_NAME_OPTION_VALUE_IS_PARAMETER_NAME = 'VALUE_IS_PARAMETER_NAME';
  /**
   * A short phrase to describe what this parameter contains.
   *
   * @var string
   */
  public $descriptivePhrase;
  /**
   * Detailed help text for this parameter containing information not provided
   * elsewhere. For example, instructions on how to migrate from a deprecated
   * parameter.
   *
   * @var string
   */
  public $helpText;
  /**
   * Whether the default value is hidden in the UI.
   *
   * @var bool
   */
  public $hideDefaultValue;
  /**
   * @var string
   */
  public $inputDisplayOption;
  /**
   * Whether this field is hidden in the UI.
   *
   * @var bool
   */
  public $isHidden;
  /**
   * A user-friendly label for the parameter.
   *
   * @var string
   */
  public $label;
  /**
   * @var string
   */
  public $parameterNameOption;
  /**
   * A user-friendly label for subSection under which the parameter will be
   * displayed.
   *
   * @var string
   */
  public $subSectionLabel;
  /**
   * Placeholder text which will appear in the UI input form for this parameter.
   *
   * @var string
   */
  public $uiPlaceholderText;

  /**
   * A short phrase to describe what this parameter contains.
   *
   * @param string $descriptivePhrase
   */
  public function setDescriptivePhrase($descriptivePhrase)
  {
    $this->descriptivePhrase = $descriptivePhrase;
  }
  /**
   * @return string
   */
  public function getDescriptivePhrase()
  {
    return $this->descriptivePhrase;
  }
  /**
   * Detailed help text for this parameter containing information not provided
   * elsewhere. For example, instructions on how to migrate from a deprecated
   * parameter.
   *
   * @param string $helpText
   */
  public function setHelpText($helpText)
  {
    $this->helpText = $helpText;
  }
  /**
   * @return string
   */
  public function getHelpText()
  {
    return $this->helpText;
  }
  /**
   * Whether the default value is hidden in the UI.
   *
   * @param bool $hideDefaultValue
   */
  public function setHideDefaultValue($hideDefaultValue)
  {
    $this->hideDefaultValue = $hideDefaultValue;
  }
  /**
   * @return bool
   */
  public function getHideDefaultValue()
  {
    return $this->hideDefaultValue;
  }
  /**
   * @param self::INPUT_DISPLAY_OPTION_* $inputDisplayOption
   */
  public function setInputDisplayOption($inputDisplayOption)
  {
    $this->inputDisplayOption = $inputDisplayOption;
  }
  /**
   * @return self::INPUT_DISPLAY_OPTION_*
   */
  public function getInputDisplayOption()
  {
    return $this->inputDisplayOption;
  }
  /**
   * Whether this field is hidden in the UI.
   *
   * @param bool $isHidden
   */
  public function setIsHidden($isHidden)
  {
    $this->isHidden = $isHidden;
  }
  /**
   * @return bool
   */
  public function getIsHidden()
  {
    return $this->isHidden;
  }
  /**
   * A user-friendly label for the parameter.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * @param self::PARAMETER_NAME_OPTION_* $parameterNameOption
   */
  public function setParameterNameOption($parameterNameOption)
  {
    $this->parameterNameOption = $parameterNameOption;
  }
  /**
   * @return self::PARAMETER_NAME_OPTION_*
   */
  public function getParameterNameOption()
  {
    return $this->parameterNameOption;
  }
  /**
   * A user-friendly label for subSection under which the parameter will be
   * displayed.
   *
   * @param string $subSectionLabel
   */
  public function setSubSectionLabel($subSectionLabel)
  {
    $this->subSectionLabel = $subSectionLabel;
  }
  /**
   * @return string
   */
  public function getSubSectionLabel()
  {
    return $this->subSectionLabel;
  }
  /**
   * Placeholder text which will appear in the UI input form for this parameter.
   *
   * @param string $uiPlaceholderText
   */
  public function setUiPlaceholderText($uiPlaceholderText)
  {
    $this->uiPlaceholderText = $uiPlaceholderText;
  }
  /**
   * @return string
   */
  public function getUiPlaceholderText()
  {
    return $this->uiPlaceholderText;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoParamSpecEntryConfig::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoParamSpecEntryConfig');
