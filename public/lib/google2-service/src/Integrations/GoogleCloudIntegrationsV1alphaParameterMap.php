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

class GoogleCloudIntegrationsV1alphaParameterMap extends \Google\Collection
{
  /**
   * Unspecified.
   */
  public const KEY_TYPE_INTEGRATION_PARAMETER_DATA_TYPE_UNSPECIFIED = 'INTEGRATION_PARAMETER_DATA_TYPE_UNSPECIFIED';
  /**
   * String.
   */
  public const KEY_TYPE_STRING_VALUE = 'STRING_VALUE';
  /**
   * Integer.
   */
  public const KEY_TYPE_INT_VALUE = 'INT_VALUE';
  /**
   * Double Number.
   */
  public const KEY_TYPE_DOUBLE_VALUE = 'DOUBLE_VALUE';
  /**
   * Boolean.
   */
  public const KEY_TYPE_BOOLEAN_VALUE = 'BOOLEAN_VALUE';
  /**
   * String Array.
   */
  public const KEY_TYPE_STRING_ARRAY = 'STRING_ARRAY';
  /**
   * Integer Array.
   */
  public const KEY_TYPE_INT_ARRAY = 'INT_ARRAY';
  /**
   * Double Number Array.
   */
  public const KEY_TYPE_DOUBLE_ARRAY = 'DOUBLE_ARRAY';
  /**
   * Boolean Array.
   */
  public const KEY_TYPE_BOOLEAN_ARRAY = 'BOOLEAN_ARRAY';
  /**
   * Json.
   */
  public const KEY_TYPE_JSON_VALUE = 'JSON_VALUE';
  /**
   * Proto Value (Internal use only).
   */
  public const KEY_TYPE_PROTO_VALUE = 'PROTO_VALUE';
  /**
   * Proto Array (Internal use only).
   */
  public const KEY_TYPE_PROTO_ARRAY = 'PROTO_ARRAY';
  /**
   * // Non-serializable object (Internal use only).
   */
  public const KEY_TYPE_NON_SERIALIZABLE_OBJECT = 'NON_SERIALIZABLE_OBJECT';
  /**
   * Proto Enum (Internal use only).
   */
  public const KEY_TYPE_PROTO_ENUM = 'PROTO_ENUM';
  /**
   * Serialized object (Internal use only).
   */
  public const KEY_TYPE_SERIALIZED_OBJECT_VALUE = 'SERIALIZED_OBJECT_VALUE';
  /**
   * Proto Enum Array (Internal use only).
   */
  public const KEY_TYPE_PROTO_ENUM_ARRAY = 'PROTO_ENUM_ARRAY';
  /**
   * BYTES data types are not allowed for top-level params. They're only meant
   * to support protobufs with BYTES (sub)fields.
   */
  public const KEY_TYPE_BYTES = 'BYTES';
  /**
   * BYTES_ARRAY data types are not allowed for top-level params. They're only
   * meant to support protobufs with BYTES (sub)fields.
   */
  public const KEY_TYPE_BYTES_ARRAY = 'BYTES_ARRAY';
  /**
   * Unspecified.
   */
  public const VALUE_TYPE_INTEGRATION_PARAMETER_DATA_TYPE_UNSPECIFIED = 'INTEGRATION_PARAMETER_DATA_TYPE_UNSPECIFIED';
  /**
   * String.
   */
  public const VALUE_TYPE_STRING_VALUE = 'STRING_VALUE';
  /**
   * Integer.
   */
  public const VALUE_TYPE_INT_VALUE = 'INT_VALUE';
  /**
   * Double Number.
   */
  public const VALUE_TYPE_DOUBLE_VALUE = 'DOUBLE_VALUE';
  /**
   * Boolean.
   */
  public const VALUE_TYPE_BOOLEAN_VALUE = 'BOOLEAN_VALUE';
  /**
   * String Array.
   */
  public const VALUE_TYPE_STRING_ARRAY = 'STRING_ARRAY';
  /**
   * Integer Array.
   */
  public const VALUE_TYPE_INT_ARRAY = 'INT_ARRAY';
  /**
   * Double Number Array.
   */
  public const VALUE_TYPE_DOUBLE_ARRAY = 'DOUBLE_ARRAY';
  /**
   * Boolean Array.
   */
  public const VALUE_TYPE_BOOLEAN_ARRAY = 'BOOLEAN_ARRAY';
  /**
   * Json.
   */
  public const VALUE_TYPE_JSON_VALUE = 'JSON_VALUE';
  /**
   * Proto Value (Internal use only).
   */
  public const VALUE_TYPE_PROTO_VALUE = 'PROTO_VALUE';
  /**
   * Proto Array (Internal use only).
   */
  public const VALUE_TYPE_PROTO_ARRAY = 'PROTO_ARRAY';
  /**
   * // Non-serializable object (Internal use only).
   */
  public const VALUE_TYPE_NON_SERIALIZABLE_OBJECT = 'NON_SERIALIZABLE_OBJECT';
  /**
   * Proto Enum (Internal use only).
   */
  public const VALUE_TYPE_PROTO_ENUM = 'PROTO_ENUM';
  /**
   * Serialized object (Internal use only).
   */
  public const VALUE_TYPE_SERIALIZED_OBJECT_VALUE = 'SERIALIZED_OBJECT_VALUE';
  /**
   * Proto Enum Array (Internal use only).
   */
  public const VALUE_TYPE_PROTO_ENUM_ARRAY = 'PROTO_ENUM_ARRAY';
  /**
   * BYTES data types are not allowed for top-level params. They're only meant
   * to support protobufs with BYTES (sub)fields.
   */
  public const VALUE_TYPE_BYTES = 'BYTES';
  /**
   * BYTES_ARRAY data types are not allowed for top-level params. They're only
   * meant to support protobufs with BYTES (sub)fields.
   */
  public const VALUE_TYPE_BYTES_ARRAY = 'BYTES_ARRAY';
  protected $collection_key = 'entries';
  protected $entriesType = GoogleCloudIntegrationsV1alphaParameterMapEntry::class;
  protected $entriesDataType = 'array';
  /**
   * Option to specify key type for all entries of the map. If provided then
   * field types for all entries must conform to this.
   *
   * @var string
   */
  public $keyType;
  /**
   * Option to specify value type for all entries of the map. If provided then
   * field types for all entries must conform to this.
   *
   * @var string
   */
  public $valueType;

  /**
   * A list of parameter map entries.
   *
   * @param GoogleCloudIntegrationsV1alphaParameterMapEntry[] $entries
   */
  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaParameterMapEntry[]
   */
  public function getEntries()
  {
    return $this->entries;
  }
  /**
   * Option to specify key type for all entries of the map. If provided then
   * field types for all entries must conform to this.
   *
   * Accepted values: INTEGRATION_PARAMETER_DATA_TYPE_UNSPECIFIED, STRING_VALUE,
   * INT_VALUE, DOUBLE_VALUE, BOOLEAN_VALUE, STRING_ARRAY, INT_ARRAY,
   * DOUBLE_ARRAY, BOOLEAN_ARRAY, JSON_VALUE, PROTO_VALUE, PROTO_ARRAY,
   * NON_SERIALIZABLE_OBJECT, PROTO_ENUM, SERIALIZED_OBJECT_VALUE,
   * PROTO_ENUM_ARRAY, BYTES, BYTES_ARRAY
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
   * Option to specify value type for all entries of the map. If provided then
   * field types for all entries must conform to this.
   *
   * Accepted values: INTEGRATION_PARAMETER_DATA_TYPE_UNSPECIFIED, STRING_VALUE,
   * INT_VALUE, DOUBLE_VALUE, BOOLEAN_VALUE, STRING_ARRAY, INT_ARRAY,
   * DOUBLE_ARRAY, BOOLEAN_ARRAY, JSON_VALUE, PROTO_VALUE, PROTO_ARRAY,
   * NON_SERIALIZABLE_OBJECT, PROTO_ENUM, SERIALIZED_OBJECT_VALUE,
   * PROTO_ENUM_ARRAY, BYTES, BYTES_ARRAY
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
class_alias(GoogleCloudIntegrationsV1alphaParameterMap::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaParameterMap');
