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

class EnterpriseCrmFrontendsEventbusProtoParameterMap extends \Google\Collection
{
  public const KEY_TYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  public const KEY_TYPE_STRING_VALUE = 'STRING_VALUE';
  public const KEY_TYPE_INT_VALUE = 'INT_VALUE';
  public const KEY_TYPE_DOUBLE_VALUE = 'DOUBLE_VALUE';
  public const KEY_TYPE_BOOLEAN_VALUE = 'BOOLEAN_VALUE';
  public const KEY_TYPE_PROTO_VALUE = 'PROTO_VALUE';
  public const KEY_TYPE_SERIALIZED_OBJECT_VALUE = 'SERIALIZED_OBJECT_VALUE';
  public const KEY_TYPE_STRING_ARRAY = 'STRING_ARRAY';
  public const KEY_TYPE_INT_ARRAY = 'INT_ARRAY';
  public const KEY_TYPE_DOUBLE_ARRAY = 'DOUBLE_ARRAY';
  public const KEY_TYPE_PROTO_ARRAY = 'PROTO_ARRAY';
  public const KEY_TYPE_PROTO_ENUM = 'PROTO_ENUM';
  public const KEY_TYPE_BOOLEAN_ARRAY = 'BOOLEAN_ARRAY';
  public const KEY_TYPE_PROTO_ENUM_ARRAY = 'PROTO_ENUM_ARRAY';
  /**
   * BYTES and BYTES_ARRAY data types are not allowed for top-level params.
   * They're only meant to support protobufs with BYTES (sub)fields.
   */
  public const KEY_TYPE_BYTES = 'BYTES';
  public const KEY_TYPE_BYTES_ARRAY = 'BYTES_ARRAY';
  public const KEY_TYPE_NON_SERIALIZABLE_OBJECT = 'NON_SERIALIZABLE_OBJECT';
  public const KEY_TYPE_JSON_VALUE = 'JSON_VALUE';
  public const VALUE_TYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  public const VALUE_TYPE_STRING_VALUE = 'STRING_VALUE';
  public const VALUE_TYPE_INT_VALUE = 'INT_VALUE';
  public const VALUE_TYPE_DOUBLE_VALUE = 'DOUBLE_VALUE';
  public const VALUE_TYPE_BOOLEAN_VALUE = 'BOOLEAN_VALUE';
  public const VALUE_TYPE_PROTO_VALUE = 'PROTO_VALUE';
  public const VALUE_TYPE_SERIALIZED_OBJECT_VALUE = 'SERIALIZED_OBJECT_VALUE';
  public const VALUE_TYPE_STRING_ARRAY = 'STRING_ARRAY';
  public const VALUE_TYPE_INT_ARRAY = 'INT_ARRAY';
  public const VALUE_TYPE_DOUBLE_ARRAY = 'DOUBLE_ARRAY';
  public const VALUE_TYPE_PROTO_ARRAY = 'PROTO_ARRAY';
  public const VALUE_TYPE_PROTO_ENUM = 'PROTO_ENUM';
  public const VALUE_TYPE_BOOLEAN_ARRAY = 'BOOLEAN_ARRAY';
  public const VALUE_TYPE_PROTO_ENUM_ARRAY = 'PROTO_ENUM_ARRAY';
  /**
   * BYTES and BYTES_ARRAY data types are not allowed for top-level params.
   * They're only meant to support protobufs with BYTES (sub)fields.
   */
  public const VALUE_TYPE_BYTES = 'BYTES';
  public const VALUE_TYPE_BYTES_ARRAY = 'BYTES_ARRAY';
  public const VALUE_TYPE_NON_SERIALIZABLE_OBJECT = 'NON_SERIALIZABLE_OBJECT';
  public const VALUE_TYPE_JSON_VALUE = 'JSON_VALUE';
  protected $collection_key = 'entries';
  protected $entriesType = EnterpriseCrmFrontendsEventbusProtoParameterMapEntry::class;
  protected $entriesDataType = 'array';
  /**
   * Option to specify key value type for all entries of the map. If provided
   * then field types for all entries must conform to this.
   *
   * @var string
   */
  public $keyType;
  /**
   * @var string
   */
  public $valueType;

  /**
   * @param EnterpriseCrmFrontendsEventbusProtoParameterMapEntry[] $entries
   */
  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  /**
   * @return EnterpriseCrmFrontendsEventbusProtoParameterMapEntry[]
   */
  public function getEntries()
  {
    return $this->entries;
  }
  /**
   * Option to specify key value type for all entries of the map. If provided
   * then field types for all entries must conform to this.
   *
   * Accepted values: DATA_TYPE_UNSPECIFIED, STRING_VALUE, INT_VALUE,
   * DOUBLE_VALUE, BOOLEAN_VALUE, PROTO_VALUE, SERIALIZED_OBJECT_VALUE,
   * STRING_ARRAY, INT_ARRAY, DOUBLE_ARRAY, PROTO_ARRAY, PROTO_ENUM,
   * BOOLEAN_ARRAY, PROTO_ENUM_ARRAY, BYTES, BYTES_ARRAY,
   * NON_SERIALIZABLE_OBJECT, JSON_VALUE
   *
   * @param self::KEY_TYPE_* $keyType
   */
  public function setKeyType($keyType)
  {
    $this->keyType = $keyType;
  }
  /**
   * @return self::KEY_TYPE_*
   */
  public function getKeyType()
  {
    return $this->keyType;
  }
  /**
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
class_alias(EnterpriseCrmFrontendsEventbusProtoParameterMap::class, 'Google_Service_Integrations_EnterpriseCrmFrontendsEventbusProtoParameterMap');
