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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1ConfigVariableTemplate extends \Google\Collection
{
  /**
   * Value type is not specified.
   */
  public const VALUE_TYPE_VALUE_TYPE_UNSPECIFIED = 'VALUE_TYPE_UNSPECIFIED';
  /**
   * Value type is string.
   */
  public const VALUE_TYPE_STRING = 'STRING';
  /**
   * Value type is integer.
   */
  public const VALUE_TYPE_INT = 'INT';
  /**
   * Value type is boolean.
   */
  public const VALUE_TYPE_BOOL = 'BOOL';
  /**
   * Value type is secret.
   */
  public const VALUE_TYPE_SECRET = 'SECRET';
  /**
   * Value type is enum.
   */
  public const VALUE_TYPE_ENUM = 'ENUM';
  /**
   * Value type is multi select.
   */
  public const VALUE_TYPE_MULTI_SELECT = 'MULTI_SELECT';
  /**
   * Value type is multi string.
   */
  public const VALUE_TYPE_MULTI_STRING = 'MULTI_STRING';
  /**
   * Value type is multi int.
   */
  public const VALUE_TYPE_MULTI_INT = 'MULTI_INT';
  protected $collection_key = 'multiSelectOptions';
  /**
   * Optional. Description.
   *
   * @var string
   */
  public $description;
  protected $enumOptionsType = GoogleCloudApihubV1ConfigValueOption::class;
  protected $enumOptionsDataType = 'array';
  /**
   * Required. ID of the config variable. Must be unique within the
   * configuration.
   *
   * @var string
   */
  public $id;
  protected $multiSelectOptionsType = GoogleCloudApihubV1ConfigValueOption::class;
  protected $multiSelectOptionsDataType = 'array';
  /**
   * Optional. Flag represents that this `ConfigVariable` must be provided for a
   * PluginInstance.
   *
   * @var bool
   */
  public $required;
  /**
   * Optional. Regular expression in RE2 syntax used for validating the `value`
   * of a `ConfigVariable`.
   *
   * @var string
   */
  public $validationRegex;
  /**
   * Required. Type of the parameter: string, int, bool etc.
   *
   * @var string
   */
  public $valueType;

  /**
   * Optional. Description.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. Enum options. To be populated if `ValueType` is `ENUM`.
   *
   * @param GoogleCloudApihubV1ConfigValueOption[] $enumOptions
   */
  public function setEnumOptions($enumOptions)
  {
    $this->enumOptions = $enumOptions;
  }
  /**
   * @return GoogleCloudApihubV1ConfigValueOption[]
   */
  public function getEnumOptions()
  {
    return $this->enumOptions;
  }
  /**
   * Required. ID of the config variable. Must be unique within the
   * configuration.
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
   * Optional. Multi select options. To be populated if `ValueType` is
   * `MULTI_SELECT`.
   *
   * @param GoogleCloudApihubV1ConfigValueOption[] $multiSelectOptions
   */
  public function setMultiSelectOptions($multiSelectOptions)
  {
    $this->multiSelectOptions = $multiSelectOptions;
  }
  /**
   * @return GoogleCloudApihubV1ConfigValueOption[]
   */
  public function getMultiSelectOptions()
  {
    return $this->multiSelectOptions;
  }
  /**
   * Optional. Flag represents that this `ConfigVariable` must be provided for a
   * PluginInstance.
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
   * Optional. Regular expression in RE2 syntax used for validating the `value`
   * of a `ConfigVariable`.
   *
   * @param string $validationRegex
   */
  public function setValidationRegex($validationRegex)
  {
    $this->validationRegex = $validationRegex;
  }
  /**
   * @return string
   */
  public function getValidationRegex()
  {
    return $this->validationRegex;
  }
  /**
   * Required. Type of the parameter: string, int, bool etc.
   *
   * Accepted values: VALUE_TYPE_UNSPECIFIED, STRING, INT, BOOL, SECRET, ENUM,
   * MULTI_SELECT, MULTI_STRING, MULTI_INT
   *
   * @param self::VALUE_TYPE_* $valueType
   */
  public function setValueType($valueType)
  {
    $this->valueType = $valueType;
  }
  /**
   * @return self::VALUE_TYPE_*
   */
  public function getValueType()
  {
    return $this->valueType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1ConfigVariableTemplate::class, 'Google_Service_APIhub_GoogleCloudApihubV1ConfigVariableTemplate');
