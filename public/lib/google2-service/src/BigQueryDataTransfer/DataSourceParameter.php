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

namespace Google\Service\BigQueryDataTransfer;

class DataSourceParameter extends \Google\Collection
{
  /**
   * Type unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * String parameter.
   */
  public const TYPE_STRING = 'STRING';
  /**
   * Integer parameter (64-bits). Will be serialized to json as string.
   */
  public const TYPE_INTEGER = 'INTEGER';
  /**
   * Double precision floating point parameter.
   */
  public const TYPE_DOUBLE = 'DOUBLE';
  /**
   * Boolean parameter.
   */
  public const TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * Deprecated. This field has no effect.
   */
  public const TYPE_RECORD = 'RECORD';
  /**
   * Page ID for a Google+ Page.
   */
  public const TYPE_PLUS_PAGE = 'PLUS_PAGE';
  /**
   * List of strings parameter.
   */
  public const TYPE_LIST = 'LIST';
  protected $collection_key = 'fields';
  /**
   * All possible values for the parameter.
   *
   * @var string[]
   */
  public $allowedValues;
  /**
   * If true, it should not be used in new transfers, and it should not be
   * visible to users.
   *
   * @var bool
   */
  public $deprecated;
  /**
   * Parameter description.
   *
   * @var string
   */
  public $description;
  /**
   * Parameter display name in the user interface.
   *
   * @var string
   */
  public $displayName;
  protected $fieldsType = DataSourceParameter::class;
  protected $fieldsDataType = 'array';
  /**
   * Cannot be changed after initial creation.
   *
   * @var bool
   */
  public $immutable;
  /**
   * For list parameters, the max size of the list.
   *
   * @var string
   */
  public $maxListSize;
  /**
   * For integer and double values specifies maximum allowed value.
   *
   * @var 
   */
  public $maxValue;
  /**
   * For integer and double values specifies minimum allowed value.
   *
   * @var 
   */
  public $minValue;
  /**
   * Parameter identifier.
   *
   * @var string
   */
  public $paramId;
  /**
   * Deprecated. This field has no effect.
   *
   * @var bool
   */
  public $recurse;
  /**
   * Deprecated. This field has no effect.
   *
   * @var bool
   */
  public $repeated;
  /**
   * Is parameter required.
   *
   * @var bool
   */
  public $required;
  /**
   * Parameter type.
   *
   * @var string
   */
  public $type;
  /**
   * Description of the requirements for this field, in case the user input does
   * not fulfill the regex pattern or min/max values.
   *
   * @var string
   */
  public $validationDescription;
  /**
   * URL to a help document to further explain the naming requirements.
   *
   * @var string
   */
  public $validationHelpUrl;
  /**
   * Regular expression which can be used for parameter validation.
   *
   * @var string
   */
  public $validationRegex;

  /**
   * All possible values for the parameter.
   *
   * @param string[] $allowedValues
   */
  public function setAllowedValues($allowedValues)
  {
    $this->allowedValues = $allowedValues;
  }
  /**
   * @return string[]
   */
  public function getAllowedValues()
  {
    return $this->allowedValues;
  }
  /**
   * If true, it should not be used in new transfers, and it should not be
   * visible to users.
   *
   * @param bool $deprecated
   */
  public function setDeprecated($deprecated)
  {
    $this->deprecated = $deprecated;
  }
  /**
   * @return bool
   */
  public function getDeprecated()
  {
    return $this->deprecated;
  }
  /**
   * Parameter description.
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
   * Parameter display name in the user interface.
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
   * Deprecated. This field has no effect.
   *
   * @param DataSourceParameter[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return DataSourceParameter[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * Cannot be changed after initial creation.
   *
   * @param bool $immutable
   */
  public function setImmutable($immutable)
  {
    $this->immutable = $immutable;
  }
  /**
   * @return bool
   */
  public function getImmutable()
  {
    return $this->immutable;
  }
  /**
   * For list parameters, the max size of the list.
   *
   * @param string $maxListSize
   */
  public function setMaxListSize($maxListSize)
  {
    $this->maxListSize = $maxListSize;
  }
  /**
   * @return string
   */
  public function getMaxListSize()
  {
    return $this->maxListSize;
  }
  public function setMaxValue($maxValue)
  {
    $this->maxValue = $maxValue;
  }
  public function getMaxValue()
  {
    return $this->maxValue;
  }
  public function setMinValue($minValue)
  {
    $this->minValue = $minValue;
  }
  public function getMinValue()
  {
    return $this->minValue;
  }
  /**
   * Parameter identifier.
   *
   * @param string $paramId
   */
  public function setParamId($paramId)
  {
    $this->paramId = $paramId;
  }
  /**
   * @return string
   */
  public function getParamId()
  {
    return $this->paramId;
  }
  /**
   * Deprecated. This field has no effect.
   *
   * @param bool $recurse
   */
  public function setRecurse($recurse)
  {
    $this->recurse = $recurse;
  }
  /**
   * @return bool
   */
  public function getRecurse()
  {
    return $this->recurse;
  }
  /**
   * Deprecated. This field has no effect.
   *
   * @param bool $repeated
   */
  public function setRepeated($repeated)
  {
    $this->repeated = $repeated;
  }
  /**
   * @return bool
   */
  public function getRepeated()
  {
    return $this->repeated;
  }
  /**
   * Is parameter required.
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
   * Parameter type.
   *
   * Accepted values: TYPE_UNSPECIFIED, STRING, INTEGER, DOUBLE, BOOLEAN,
   * RECORD, PLUS_PAGE, LIST
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Description of the requirements for this field, in case the user input does
   * not fulfill the regex pattern or min/max values.
   *
   * @param string $validationDescription
   */
  public function setValidationDescription($validationDescription)
  {
    $this->validationDescription = $validationDescription;
  }
  /**
   * @return string
   */
  public function getValidationDescription()
  {
    return $this->validationDescription;
  }
  /**
   * URL to a help document to further explain the naming requirements.
   *
   * @param string $validationHelpUrl
   */
  public function setValidationHelpUrl($validationHelpUrl)
  {
    $this->validationHelpUrl = $validationHelpUrl;
  }
  /**
   * @return string
   */
  public function getValidationHelpUrl()
  {
    return $this->validationHelpUrl;
  }
  /**
   * Regular expression which can be used for parameter validation.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataSourceParameter::class, 'Google_Service_BigQueryDataTransfer_DataSourceParameter');
