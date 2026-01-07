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

class GoogleCloudApihubV1Attribute extends \Google\Collection
{
  /**
   * Attribute data type unspecified.
   */
  public const DATA_TYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  /**
   * Attribute's value is of type enum.
   */
  public const DATA_TYPE_ENUM = 'ENUM';
  /**
   * Attribute's value is of type json.
   */
  public const DATA_TYPE_JSON = 'JSON';
  /**
   * Attribute's value is of type string.
   */
  public const DATA_TYPE_STRING = 'STRING';
  /**
   * Attribute's value is of type uri.
   */
  public const DATA_TYPE_URI = 'URI';
  /**
   * Attribute definition type unspecified.
   */
  public const DEFINITION_TYPE_DEFINITION_TYPE_UNSPECIFIED = 'DEFINITION_TYPE_UNSPECIFIED';
  /**
   * The attribute is predefined by the API Hub. Note that only the list of
   * allowed values can be updated in this case via UpdateAttribute method.
   */
  public const DEFINITION_TYPE_SYSTEM_DEFINED = 'SYSTEM_DEFINED';
  /**
   * The attribute is defined by the user.
   */
  public const DEFINITION_TYPE_USER_DEFINED = 'USER_DEFINED';
  /**
   * Scope Unspecified.
   */
  public const SCOPE_SCOPE_UNSPECIFIED = 'SCOPE_UNSPECIFIED';
  /**
   * Attribute can be linked to an API.
   */
  public const SCOPE_API = 'API';
  /**
   * Attribute can be linked to an API version.
   */
  public const SCOPE_VERSION = 'VERSION';
  /**
   * Attribute can be linked to a Spec.
   */
  public const SCOPE_SPEC = 'SPEC';
  /**
   * Attribute can be linked to an API Operation.
   */
  public const SCOPE_API_OPERATION = 'API_OPERATION';
  /**
   * Attribute can be linked to a Deployment.
   */
  public const SCOPE_DEPLOYMENT = 'DEPLOYMENT';
  /**
   * Attribute can be linked to a Dependency.
   */
  public const SCOPE_DEPENDENCY = 'DEPENDENCY';
  /**
   * Attribute can be linked to a definition.
   */
  public const SCOPE_DEFINITION = 'DEFINITION';
  /**
   * Attribute can be linked to a ExternalAPI.
   */
  public const SCOPE_EXTERNAL_API = 'EXTERNAL_API';
  /**
   * Attribute can be linked to a Plugin.
   */
  public const SCOPE_PLUGIN = 'PLUGIN';
  protected $collection_key = 'allowedValues';
  protected $allowedValuesType = GoogleCloudApihubV1AllowedValue::class;
  protected $allowedValuesDataType = 'array';
  /**
   * Optional. The maximum number of values that the attribute can have when
   * associated with an API Hub resource. Cardinality 1 would represent a
   * single-valued attribute. It must not be less than 1 or greater than 20. If
   * not specified, the cardinality would be set to 1 by default and represent a
   * single-valued attribute.
   *
   * @var int
   */
  public $cardinality;
  /**
   * Output only. The time at which the attribute was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The type of the data of the attribute.
   *
   * @var string
   */
  public $dataType;
  /**
   * Output only. The definition type of the attribute.
   *
   * @var string
   */
  public $definitionType;
  /**
   * Optional. The description of the attribute.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the attribute.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. When mandatory is true, the attribute is mandatory for the
   * resource specified in the scope. Only System defined attributes can be
   * mandatory.
   *
   * @var bool
   */
  public $mandatory;
  /**
   * Identifier. The name of the attribute in the API Hub. Format:
   * `projects/{project}/locations/{location}/attributes/{attribute}`
   *
   * @var string
   */
  public $name;
  /**
   * Required. The scope of the attribute. It represents the resource in the API
   * Hub to which the attribute can be linked.
   *
   * @var string
   */
  public $scope;
  /**
   * Output only. The time at which the attribute was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The list of allowed values when the attribute value is of type
   * enum. This is required when the data_type of the attribute is ENUM. The
   * maximum number of allowed values of an attribute will be 1000.
   *
   * @param GoogleCloudApihubV1AllowedValue[] $allowedValues
   */
  public function setAllowedValues($allowedValues)
  {
    $this->allowedValues = $allowedValues;
  }
  /**
   * @return GoogleCloudApihubV1AllowedValue[]
   */
  public function getAllowedValues()
  {
    return $this->allowedValues;
  }
  /**
   * Optional. The maximum number of values that the attribute can have when
   * associated with an API Hub resource. Cardinality 1 would represent a
   * single-valued attribute. It must not be less than 1 or greater than 20. If
   * not specified, the cardinality would be set to 1 by default and represent a
   * single-valued attribute.
   *
   * @param int $cardinality
   */
  public function setCardinality($cardinality)
  {
    $this->cardinality = $cardinality;
  }
  /**
   * @return int
   */
  public function getCardinality()
  {
    return $this->cardinality;
  }
  /**
   * Output only. The time at which the attribute was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. The type of the data of the attribute.
   *
   * Accepted values: DATA_TYPE_UNSPECIFIED, ENUM, JSON, STRING, URI
   *
   * @param self::DATA_TYPE_* $dataType
   */
  public function setDataType($dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return self::DATA_TYPE_*
   */
  public function getDataType()
  {
    return $this->dataType;
  }
  /**
   * Output only. The definition type of the attribute.
   *
   * Accepted values: DEFINITION_TYPE_UNSPECIFIED, SYSTEM_DEFINED, USER_DEFINED
   *
   * @param self::DEFINITION_TYPE_* $definitionType
   */
  public function setDefinitionType($definitionType)
  {
    $this->definitionType = $definitionType;
  }
  /**
   * @return self::DEFINITION_TYPE_*
   */
  public function getDefinitionType()
  {
    return $this->definitionType;
  }
  /**
   * Optional. The description of the attribute.
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
   * Required. The display name of the attribute.
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
   * Output only. When mandatory is true, the attribute is mandatory for the
   * resource specified in the scope. Only System defined attributes can be
   * mandatory.
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
   * Identifier. The name of the attribute in the API Hub. Format:
   * `projects/{project}/locations/{location}/attributes/{attribute}`
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
   * Required. The scope of the attribute. It represents the resource in the API
   * Hub to which the attribute can be linked.
   *
   * Accepted values: SCOPE_UNSPECIFIED, API, VERSION, SPEC, API_OPERATION,
   * DEPLOYMENT, DEPENDENCY, DEFINITION, EXTERNAL_API, PLUGIN
   *
   * @param self::SCOPE_* $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return self::SCOPE_*
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * Output only. The time at which the attribute was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1Attribute::class, 'Google_Service_APIhub_GoogleCloudApihubV1Attribute');
