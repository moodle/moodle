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

class EnterpriseCrmFrontendsEventbusProtoParameterEntry extends \Google\Model
{
  public const DATA_TYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  public const DATA_TYPE_STRING_VALUE = 'STRING_VALUE';
  public const DATA_TYPE_INT_VALUE = 'INT_VALUE';
  public const DATA_TYPE_DOUBLE_VALUE = 'DOUBLE_VALUE';
  public const DATA_TYPE_BOOLEAN_VALUE = 'BOOLEAN_VALUE';
  public const DATA_TYPE_PROTO_VALUE = 'PROTO_VALUE';
  public const DATA_TYPE_SERIALIZED_OBJECT_VALUE = 'SERIALIZED_OBJECT_VALUE';
  public const DATA_TYPE_STRING_ARRAY = 'STRING_ARRAY';
  public const DATA_TYPE_INT_ARRAY = 'INT_ARRAY';
  public const DATA_TYPE_DOUBLE_ARRAY = 'DOUBLE_ARRAY';
  public const DATA_TYPE_PROTO_ARRAY = 'PROTO_ARRAY';
  public const DATA_TYPE_PROTO_ENUM = 'PROTO_ENUM';
  public const DATA_TYPE_BOOLEAN_ARRAY = 'BOOLEAN_ARRAY';
  public const DATA_TYPE_PROTO_ENUM_ARRAY = 'PROTO_ENUM_ARRAY';
  /**
   * BYTES and BYTES_ARRAY data types are not allowed for top-level params.
   * They're only meant to support protobufs with BYTES (sub)fields.
   */
  public const DATA_TYPE_BYTES = 'BYTES';
  public const DATA_TYPE_BYTES_ARRAY = 'BYTES_ARRAY';
  public const DATA_TYPE_NON_SERIALIZABLE_OBJECT = 'NON_SERIALIZABLE_OBJECT';
  public const DATA_TYPE_JSON_VALUE = 'JSON_VALUE';
  /**
   * Explicitly getting the type of the parameter.
   *
   * @var string
   */
  public $dataType;
  /**
   * Key is used to retrieve the corresponding parameter value. This should be
   * unique for a given fired event. These parameters must be predefined in the
   * workflow definition.
   *
   * @var string
   */
  public $key;
  /**
   * True if this parameter should be masked in the logs
   *
   * @var bool
   */
  public $masked;
  protected $valueType = EnterpriseCrmFrontendsEventbusProtoParameterValueType::class;
  protected $valueDataType = '';

  /**
   * Explicitly getting the type of the parameter.
   *
   * Accepted values: DATA_TYPE_UNSPECIFIED, STRING_VALUE, INT_VALUE,
   * DOUBLE_VALUE, BOOLEAN_VALUE, PROTO_VALUE, SERIALIZED_OBJECT_VALUE,
   * STRING_ARRAY, INT_ARRAY, DOUBLE_ARRAY, PROTO_ARRAY, PROTO_ENUM,
   * BOOLEAN_ARRAY, PROTO_ENUM_ARRAY, BYTES, BYTES_ARRAY,
   * NON_SERIALIZABLE_OBJECT, JSON_VALUE
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
   * Key is used to retrieve the corresponding parameter value. This should be
   * unique for a given fired event. These parameters must be predefined in the
   * workflow definition.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * True if this parameter should be masked in the logs
   *
   * @param bool $masked
   */
  public function setMasked($masked)
  {
    $this->masked = $masked;
  }
  /**
   * @return bool
   */
  public function getMasked()
  {
    return $this->masked;
  }
  /**
   * Values for the defined keys. Each value can either be string, int, double
   * or any proto message.
   *
   * @param EnterpriseCrmFrontendsEventbusProtoParameterValueType $value
   */
  public function setValue(EnterpriseCrmFrontendsEventbusProtoParameterValueType $value)
  {
    $this->value = $value;
  }
  /**
   * @return EnterpriseCrmFrontendsEventbusProtoParameterValueType
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmFrontendsEventbusProtoParameterEntry::class, 'Google_Service_Integrations_EnterpriseCrmFrontendsEventbusProtoParameterEntry');
