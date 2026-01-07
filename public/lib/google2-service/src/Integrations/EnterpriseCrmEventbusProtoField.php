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

class EnterpriseCrmEventbusProtoField extends \Google\Model
{
  /**
   * For fields with unspecified cardinality.
   */
  public const CARDINALITY_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * If field cardinality is set to optional, ignore errors if input field value
   * is null or the reference_key is not found.
   */
  public const CARDINALITY_OPTIONAL = 'OPTIONAL';
  public const FIELD_TYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  public const FIELD_TYPE_STRING_VALUE = 'STRING_VALUE';
  public const FIELD_TYPE_INT_VALUE = 'INT_VALUE';
  public const FIELD_TYPE_DOUBLE_VALUE = 'DOUBLE_VALUE';
  public const FIELD_TYPE_BOOLEAN_VALUE = 'BOOLEAN_VALUE';
  public const FIELD_TYPE_PROTO_VALUE = 'PROTO_VALUE';
  public const FIELD_TYPE_SERIALIZED_OBJECT_VALUE = 'SERIALIZED_OBJECT_VALUE';
  public const FIELD_TYPE_STRING_ARRAY = 'STRING_ARRAY';
  public const FIELD_TYPE_INT_ARRAY = 'INT_ARRAY';
  public const FIELD_TYPE_DOUBLE_ARRAY = 'DOUBLE_ARRAY';
  public const FIELD_TYPE_PROTO_ARRAY = 'PROTO_ARRAY';
  public const FIELD_TYPE_PROTO_ENUM = 'PROTO_ENUM';
  public const FIELD_TYPE_BOOLEAN_ARRAY = 'BOOLEAN_ARRAY';
  public const FIELD_TYPE_PROTO_ENUM_ARRAY = 'PROTO_ENUM_ARRAY';
  /**
   * BYTES and BYTES_ARRAY data types are not allowed for top-level params.
   * They're only meant to support protobufs with BYTES (sub)fields.
   */
  public const FIELD_TYPE_BYTES = 'BYTES';
  public const FIELD_TYPE_BYTES_ARRAY = 'BYTES_ARRAY';
  public const FIELD_TYPE_NON_SERIALIZABLE_OBJECT = 'NON_SERIALIZABLE_OBJECT';
  public const FIELD_TYPE_JSON_VALUE = 'JSON_VALUE';
  /**
   * By default, if the cardinality is unspecified the field is considered
   * required while mapping.
   *
   * @var string
   */
  public $cardinality;
  protected $defaultValueType = EnterpriseCrmEventbusProtoParameterValueType::class;
  protected $defaultValueDataType = '';
  /**
   * Specifies the data type of the field.
   *
   * @var string
   */
  public $fieldType;
  /**
   * Optional. The fully qualified proto name (e.g.
   * enterprise.crm.storage.Account). Required for output field of type
   * PROTO_VALUE or PROTO_ARRAY. For e.g., if input field_type is BYTES and
   * output field_type is PROTO_VALUE, then fully qualified proto type url
   * should be provided to parse the input bytes. If field_type is *_ARRAY, then
   * all the converted protos are of the same type.
   *
   * @var string
   */
  public $protoDefPath;
  /**
   * This holds the reference key of the workflow or task parameter. 1. Any
   * workflow parameter, for e.g. $workflowParam1$. 2. Any task input or output
   * parameter, for e.g. $task1_param1$. 3. Any workflow or task parameters with
   * subfield references, for e.g., $task1_param1.employee.id$
   *
   * @var string
   */
  public $referenceKey;
  protected $transformExpressionType = EnterpriseCrmEventbusProtoTransformExpression::class;
  protected $transformExpressionDataType = '';

  /**
   * By default, if the cardinality is unspecified the field is considered
   * required while mapping.
   *
   * Accepted values: UNSPECIFIED, OPTIONAL
   *
   * @param self::CARDINALITY_* $cardinality
   */
  public function setCardinality($cardinality)
  {
    $this->cardinality = $cardinality;
  }
  /**
   * @return self::CARDINALITY_*
   */
  public function getCardinality()
  {
    return $this->cardinality;
  }
  /**
   * This holds the default values for the fields. This value is supplied by
   * user so may or may not contain PII or SPII data.
   *
   * @param EnterpriseCrmEventbusProtoParameterValueType $defaultValue
   */
  public function setDefaultValue(EnterpriseCrmEventbusProtoParameterValueType $defaultValue)
  {
    $this->defaultValue = $defaultValue;
  }
  /**
   * @return EnterpriseCrmEventbusProtoParameterValueType
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }
  /**
   * Specifies the data type of the field.
   *
   * Accepted values: DATA_TYPE_UNSPECIFIED, STRING_VALUE, INT_VALUE,
   * DOUBLE_VALUE, BOOLEAN_VALUE, PROTO_VALUE, SERIALIZED_OBJECT_VALUE,
   * STRING_ARRAY, INT_ARRAY, DOUBLE_ARRAY, PROTO_ARRAY, PROTO_ENUM,
   * BOOLEAN_ARRAY, PROTO_ENUM_ARRAY, BYTES, BYTES_ARRAY,
   * NON_SERIALIZABLE_OBJECT, JSON_VALUE
   *
   * @param self::FIELD_TYPE_* $fieldType
   */
  public function setFieldType($fieldType)
  {
    $this->fieldType = $fieldType;
  }
  /**
   * @return self::FIELD_TYPE_*
   */
  public function getFieldType()
  {
    return $this->fieldType;
  }
  /**
   * Optional. The fully qualified proto name (e.g.
   * enterprise.crm.storage.Account). Required for output field of type
   * PROTO_VALUE or PROTO_ARRAY. For e.g., if input field_type is BYTES and
   * output field_type is PROTO_VALUE, then fully qualified proto type url
   * should be provided to parse the input bytes. If field_type is *_ARRAY, then
   * all the converted protos are of the same type.
   *
   * @param string $protoDefPath
   */
  public function setProtoDefPath($protoDefPath)
  {
    $this->protoDefPath = $protoDefPath;
  }
  /**
   * @return string
   */
  public function getProtoDefPath()
  {
    return $this->protoDefPath;
  }
  /**
   * This holds the reference key of the workflow or task parameter. 1. Any
   * workflow parameter, for e.g. $workflowParam1$. 2. Any task input or output
   * parameter, for e.g. $task1_param1$. 3. Any workflow or task parameters with
   * subfield references, for e.g., $task1_param1.employee.id$
   *
   * @param string $referenceKey
   */
  public function setReferenceKey($referenceKey)
  {
    $this->referenceKey = $referenceKey;
  }
  /**
   * @return string
   */
  public function getReferenceKey()
  {
    return $this->referenceKey;
  }
  /**
   * This is the transform expression to fetch the input field value. for e.g.
   * $param1$.CONCAT('test'). Keep points - 1. Only input field can have a
   * transform expression. 2. If a transform expression is provided,
   * reference_key will be ignored. 3. If no value is returned after evaluation
   * of transform expression, default_value can be mapped if provided. 4. The
   * field_type should be the type of the final object returned after the
   * transform expression is evaluated. Scrubs the transform expression before
   * logging as value provided by user so may or may not contain PII or SPII
   * data.
   *
   * @param EnterpriseCrmEventbusProtoTransformExpression $transformExpression
   */
  public function setTransformExpression(EnterpriseCrmEventbusProtoTransformExpression $transformExpression)
  {
    $this->transformExpression = $transformExpression;
  }
  /**
   * @return EnterpriseCrmEventbusProtoTransformExpression
   */
  public function getTransformExpression()
  {
    return $this->transformExpression;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoField::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoField');
