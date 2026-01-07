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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowV2beta1IntentParameter extends \Google\Collection
{
  protected $collection_key = 'prompts';
  /**
   * Optional. The default value to use when the `value` yields an empty result.
   * Default values can be extracted from contexts by using the following
   * syntax: `#context_name.parameter_name`.
   *
   * @var string
   */
  public $defaultValue;
  /**
   * Required. The name of the parameter.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. The name of the entity type, prefixed with `@`, that describes
   * values of the parameter. If the parameter is required, this must be
   * provided.
   *
   * @var string
   */
  public $entityTypeDisplayName;
  /**
   * Optional. Indicates whether the parameter represents a list of values.
   *
   * @var bool
   */
  public $isList;
  /**
   * Optional. Indicates whether the parameter is required. That is, whether the
   * intent cannot be completed without collecting the parameter value.
   *
   * @var bool
   */
  public $mandatory;
  /**
   * The unique identifier of this parameter.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The collection of prompts that the agent can present to the user
   * in order to collect a value for the parameter.
   *
   * @var string[]
   */
  public $prompts;
  /**
   * Optional. The definition of the parameter value. It can be: - a constant
   * string, - a parameter value defined as `$parameter_name`, - an original
   * parameter value defined as `$parameter_name.original`, - a parameter value
   * from some context defined as `#context_name.parameter_name`.
   *
   * @var string
   */
  public $value;

  /**
   * Optional. The default value to use when the `value` yields an empty result.
   * Default values can be extracted from contexts by using the following
   * syntax: `#context_name.parameter_name`.
   *
   * @param string $defaultValue
   */
  public function setDefaultValue($defaultValue)
  {
    $this->defaultValue = $defaultValue;
  }
  /**
   * @return string
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }
  /**
   * Required. The name of the parameter.
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
   * Optional. The name of the entity type, prefixed with `@`, that describes
   * values of the parameter. If the parameter is required, this must be
   * provided.
   *
   * @param string $entityTypeDisplayName
   */
  public function setEntityTypeDisplayName($entityTypeDisplayName)
  {
    $this->entityTypeDisplayName = $entityTypeDisplayName;
  }
  /**
   * @return string
   */
  public function getEntityTypeDisplayName()
  {
    return $this->entityTypeDisplayName;
  }
  /**
   * Optional. Indicates whether the parameter represents a list of values.
   *
   * @param bool $isList
   */
  public function setIsList($isList)
  {
    $this->isList = $isList;
  }
  /**
   * @return bool
   */
  public function getIsList()
  {
    return $this->isList;
  }
  /**
   * Optional. Indicates whether the parameter is required. That is, whether the
   * intent cannot be completed without collecting the parameter value.
   *
   * @param bool $mandatory
   */
  public function setMandatory($mandatory)
  {
    $this->mandatory = $mandatory;
  }
  /**
   * @return bool
   */
  public function getMandatory()
  {
    return $this->mandatory;
  }
  /**
   * The unique identifier of this parameter.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. The collection of prompts that the agent can present to the user
   * in order to collect a value for the parameter.
   *
   * @param string[] $prompts
   */
  public function setPrompts($prompts)
  {
    $this->prompts = $prompts;
  }
  /**
   * @return string[]
   */
  public function getPrompts()
  {
    return $this->prompts;
  }
  /**
   * Optional. The definition of the parameter value. It can be: - a constant
   * string, - a parameter value defined as `$parameter_name`, - an original
   * parameter value defined as `$parameter_name.original`, - a parameter value
   * from some context defined as `#context_name.parameter_name`.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1IntentParameter::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1IntentParameter');
