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

namespace Google\Service\ServiceManagement;

class Field extends \Google\Collection
{
  /**
   * For fields with unknown cardinality.
   */
  public const CARDINALITY_CARDINALITY_UNKNOWN = 'CARDINALITY_UNKNOWN';
  /**
   * For optional fields.
   */
  public const CARDINALITY_CARDINALITY_OPTIONAL = 'CARDINALITY_OPTIONAL';
  /**
   * For required fields. Proto2 syntax only.
   */
  public const CARDINALITY_CARDINALITY_REQUIRED = 'CARDINALITY_REQUIRED';
  /**
   * For repeated fields.
   */
  public const CARDINALITY_CARDINALITY_REPEATED = 'CARDINALITY_REPEATED';
  /**
   * Field type unknown.
   */
  public const KIND_TYPE_UNKNOWN = 'TYPE_UNKNOWN';
  /**
   * Field type double.
   */
  public const KIND_TYPE_DOUBLE = 'TYPE_DOUBLE';
  /**
   * Field type float.
   */
  public const KIND_TYPE_FLOAT = 'TYPE_FLOAT';
  /**
   * Field type int64.
   */
  public const KIND_TYPE_INT64 = 'TYPE_INT64';
  /**
   * Field type uint64.
   */
  public const KIND_TYPE_UINT64 = 'TYPE_UINT64';
  /**
   * Field type int32.
   */
  public const KIND_TYPE_INT32 = 'TYPE_INT32';
  /**
   * Field type fixed64.
   */
  public const KIND_TYPE_FIXED64 = 'TYPE_FIXED64';
  /**
   * Field type fixed32.
   */
  public const KIND_TYPE_FIXED32 = 'TYPE_FIXED32';
  /**
   * Field type bool.
   */
  public const KIND_TYPE_BOOL = 'TYPE_BOOL';
  /**
   * Field type string.
   */
  public const KIND_TYPE_STRING = 'TYPE_STRING';
  /**
   * Field type group. Proto2 syntax only, and deprecated.
   */
  public const KIND_TYPE_GROUP = 'TYPE_GROUP';
  /**
   * Field type message.
   */
  public const KIND_TYPE_MESSAGE = 'TYPE_MESSAGE';
  /**
   * Field type bytes.
   */
  public const KIND_TYPE_BYTES = 'TYPE_BYTES';
  /**
   * Field type uint32.
   */
  public const KIND_TYPE_UINT32 = 'TYPE_UINT32';
  /**
   * Field type enum.
   */
  public const KIND_TYPE_ENUM = 'TYPE_ENUM';
  /**
   * Field type sfixed32.
   */
  public const KIND_TYPE_SFIXED32 = 'TYPE_SFIXED32';
  /**
   * Field type sfixed64.
   */
  public const KIND_TYPE_SFIXED64 = 'TYPE_SFIXED64';
  /**
   * Field type sint32.
   */
  public const KIND_TYPE_SINT32 = 'TYPE_SINT32';
  /**
   * Field type sint64.
   */
  public const KIND_TYPE_SINT64 = 'TYPE_SINT64';
  protected $collection_key = 'options';
  /**
   * The field cardinality.
   *
   * @var string
   */
  public $cardinality;
  /**
   * The string value of the default value of this field. Proto2 syntax only.
   *
   * @var string
   */
  public $defaultValue;
  /**
   * The field JSON name.
   *
   * @var string
   */
  public $jsonName;
  /**
   * The field type.
   *
   * @var string
   */
  public $kind;
  /**
   * The field name.
   *
   * @var string
   */
  public $name;
  /**
   * The field number.
   *
   * @var int
   */
  public $number;
  /**
   * The index of the field type in `Type.oneofs`, for message or enumeration
   * types. The first type has index 1; zero means the type is not in the list.
   *
   * @var int
   */
  public $oneofIndex;
  protected $optionsType = Option::class;
  protected $optionsDataType = 'array';
  /**
   * Whether to use alternative packed wire representation.
   *
   * @var bool
   */
  public $packed;
  /**
   * The field type URL, without the scheme, for message or enumeration types.
   * Example: `"type.googleapis.com/google.protobuf.Timestamp"`.
   *
   * @var string
   */
  public $typeUrl;

  /**
   * The field cardinality.
   *
   * Accepted values: CARDINALITY_UNKNOWN, CARDINALITY_OPTIONAL,
   * CARDINALITY_REQUIRED, CARDINALITY_REPEATED
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
   * The string value of the default value of this field. Proto2 syntax only.
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
   * The field JSON name.
   *
   * @param string $jsonName
   */
  public function setJsonName($jsonName)
  {
    $this->jsonName = $jsonName;
  }
  /**
   * @return string
   */
  public function getJsonName()
  {
    return $this->jsonName;
  }
  /**
   * The field type.
   *
   * Accepted values: TYPE_UNKNOWN, TYPE_DOUBLE, TYPE_FLOAT, TYPE_INT64,
   * TYPE_UINT64, TYPE_INT32, TYPE_FIXED64, TYPE_FIXED32, TYPE_BOOL,
   * TYPE_STRING, TYPE_GROUP, TYPE_MESSAGE, TYPE_BYTES, TYPE_UINT32, TYPE_ENUM,
   * TYPE_SFIXED32, TYPE_SFIXED64, TYPE_SINT32, TYPE_SINT64
   *
   * @param self::KIND_* $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return self::KIND_*
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The field name.
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
   * The field number.
   *
   * @param int $number
   */
  public function setNumber($number)
  {
    $this->number = $number;
  }
  /**
   * @return int
   */
  public function getNumber()
  {
    return $this->number;
  }
  /**
   * The index of the field type in `Type.oneofs`, for message or enumeration
   * types. The first type has index 1; zero means the type is not in the list.
   *
   * @param int $oneofIndex
   */
  public function setOneofIndex($oneofIndex)
  {
    $this->oneofIndex = $oneofIndex;
  }
  /**
   * @return int
   */
  public function getOneofIndex()
  {
    return $this->oneofIndex;
  }
  /**
   * The protocol buffer options.
   *
   * @param Option[] $options
   */
  public function setOptions($options)
  {
    $this->options = $options;
  }
  /**
   * @return Option[]
   */
  public function getOptions()
  {
    return $this->options;
  }
  /**
   * Whether to use alternative packed wire representation.
   *
   * @param bool $packed
   */
  public function setPacked($packed)
  {
    $this->packed = $packed;
  }
  /**
   * @return bool
   */
  public function getPacked()
  {
    return $this->packed;
  }
  /**
   * The field type URL, without the scheme, for message or enumeration types.
   * Example: `"type.googleapis.com/google.protobuf.Timestamp"`.
   *
   * @param string $typeUrl
   */
  public function setTypeUrl($typeUrl)
  {
    $this->typeUrl = $typeUrl;
  }
  /**
   * @return string
   */
  public function getTypeUrl()
  {
    return $this->typeUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Field::class, 'Google_Service_ServiceManagement_Field');
