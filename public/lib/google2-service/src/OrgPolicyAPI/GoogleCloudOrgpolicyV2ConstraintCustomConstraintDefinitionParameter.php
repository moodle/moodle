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

namespace Google\Service\OrgPolicyAPI;

class GoogleCloudOrgpolicyV2ConstraintCustomConstraintDefinitionParameter extends \Google\Model
{
  /**
   * This is only used for distinguishing unset values and should never be used.
   * Results in an error.
   */
  public const ITEM_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * List parameter type.
   */
  public const ITEM_LIST = 'LIST';
  /**
   * String parameter type.
   */
  public const ITEM_STRING = 'STRING';
  /**
   * Boolean parameter type.
   */
  public const ITEM_BOOLEAN = 'BOOLEAN';
  /**
   * This is only used for distinguishing unset values and should never be used.
   * Results in an error.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * List parameter type.
   */
  public const TYPE_LIST = 'LIST';
  /**
   * String parameter type.
   */
  public const TYPE_STRING = 'STRING';
  /**
   * Boolean parameter type.
   */
  public const TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * Sets the value of the parameter in an assignment if no value is given.
   *
   * @var array
   */
  public $defaultValue;
  /**
   * Determines the parameter's value structure. For example, `LIST` can be
   * specified by defining `type: LIST`, and `item: STRING`.
   *
   * @var string
   */
  public $item;
  protected $metadataType = GoogleCloudOrgpolicyV2ConstraintCustomConstraintDefinitionParameterMetadata::class;
  protected $metadataDataType = '';
  /**
   * Type of the parameter.
   *
   * @var string
   */
  public $type;
  /**
   * Provides a CEL expression to specify the acceptable parameter values during
   * assignment. For example, parameterName in ("parameterValue1",
   * "parameterValue2")
   *
   * @var string
   */
  public $validValuesExpr;

  /**
   * Sets the value of the parameter in an assignment if no value is given.
   *
   * @param array $defaultValue
   */
  public function setDefaultValue($defaultValue)
  {
    $this->defaultValue = $defaultValue;
  }
  /**
   * @return array
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }
  /**
   * Determines the parameter's value structure. For example, `LIST` can be
   * specified by defining `type: LIST`, and `item: STRING`.
   *
   * Accepted values: TYPE_UNSPECIFIED, LIST, STRING, BOOLEAN
   *
   * @param self::ITEM_* $item
   */
  public function setItem($item)
  {
    $this->item = $item;
  }
  /**
   * @return self::ITEM_*
   */
  public function getItem()
  {
    return $this->item;
  }
  /**
   * Defines subproperties primarily used by the UI to display user-friendly
   * information.
   *
   * @param GoogleCloudOrgpolicyV2ConstraintCustomConstraintDefinitionParameterMetadata $metadata
   */
  public function setMetadata(GoogleCloudOrgpolicyV2ConstraintCustomConstraintDefinitionParameterMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return GoogleCloudOrgpolicyV2ConstraintCustomConstraintDefinitionParameterMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Type of the parameter.
   *
   * Accepted values: TYPE_UNSPECIFIED, LIST, STRING, BOOLEAN
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
   * Provides a CEL expression to specify the acceptable parameter values during
   * assignment. For example, parameterName in ("parameterValue1",
   * "parameterValue2")
   *
   * @param string $validValuesExpr
   */
  public function setValidValuesExpr($validValuesExpr)
  {
    $this->validValuesExpr = $validValuesExpr;
  }
  /**
   * @return string
   */
  public function getValidValuesExpr()
  {
    return $this->validValuesExpr;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudOrgpolicyV2ConstraintCustomConstraintDefinitionParameter::class, 'Google_Service_OrgPolicyAPI_GoogleCloudOrgpolicyV2ConstraintCustomConstraintDefinitionParameter');
