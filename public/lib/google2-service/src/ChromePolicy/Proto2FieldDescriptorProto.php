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

namespace Google\Service\ChromePolicy;

class Proto2FieldDescriptorProto extends \Google\Model
{
  /**
   * 0 is reserved for errors
   */
  public const LABEL_LABEL_OPTIONAL = 'LABEL_OPTIONAL';
  public const LABEL_LABEL_REPEATED = 'LABEL_REPEATED';
  /**
   * The required label is only allowed in proto2. In proto3 and Editions it's
   * explicitly prohibited. In Editions, the `field_presence` feature can be
   * used to get this behavior.
   */
  public const LABEL_LABEL_REQUIRED = 'LABEL_REQUIRED';
  /**
   * 0 is reserved for errors. Order is weird for historical reasons.
   */
  public const TYPE_TYPE_DOUBLE = 'TYPE_DOUBLE';
  public const TYPE_TYPE_FLOAT = 'TYPE_FLOAT';
  /**
   * Not ZigZag encoded. Negative numbers take 10 bytes. Use TYPE_SINT64 if
   * negative values are likely.
   */
  public const TYPE_TYPE_INT64 = 'TYPE_INT64';
  public const TYPE_TYPE_UINT64 = 'TYPE_UINT64';
  /**
   * Not ZigZag encoded. Negative numbers take 10 bytes. Use TYPE_SINT32 if
   * negative values are likely.
   */
  public const TYPE_TYPE_INT32 = 'TYPE_INT32';
  public const TYPE_TYPE_FIXED64 = 'TYPE_FIXED64';
  public const TYPE_TYPE_FIXED32 = 'TYPE_FIXED32';
  public const TYPE_TYPE_BOOL = 'TYPE_BOOL';
  public const TYPE_TYPE_STRING = 'TYPE_STRING';
  /**
   * Tag-delimited aggregate. Group type is deprecated and not supported after
   * proto2. However, Proto3 implementations should still be able to parse the
   * group wire format and treat group fields as unknown fields. In Editions,
   * the group wire format can be enabled via the `message_encoding` feature.
   */
  public const TYPE_TYPE_GROUP = 'TYPE_GROUP';
  /**
   * Length-delimited aggregate.
   */
  public const TYPE_TYPE_MESSAGE = 'TYPE_MESSAGE';
  /**
   * New in version 2.
   */
  public const TYPE_TYPE_BYTES = 'TYPE_BYTES';
  public const TYPE_TYPE_UINT32 = 'TYPE_UINT32';
  public const TYPE_TYPE_ENUM = 'TYPE_ENUM';
  public const TYPE_TYPE_SFIXED32 = 'TYPE_SFIXED32';
  public const TYPE_TYPE_SFIXED64 = 'TYPE_SFIXED64';
  /**
   * Uses ZigZag encoding.
   */
  public const TYPE_TYPE_SINT32 = 'TYPE_SINT32';
  /**
   * Uses ZigZag encoding.
   */
  public const TYPE_TYPE_SINT64 = 'TYPE_SINT64';
  /**
   * For numeric types, contains the original text representation of the value.
   * For booleans, "true" or "false". For strings, contains the default text
   * contents (not escaped in any way). For bytes, contains the C escaped value.
   * All bytes >= 128 are escaped.
   *
   * @var string
   */
  public $defaultValue;
  /**
   * JSON name of this field. The value is set by protocol compiler. If the user
   * has set a "json_name" option on this field, that option's value will be
   * used. Otherwise, it's deduced from the field's name by converting it to
   * camelCase.
   *
   * @var string
   */
  public $jsonName;
  /**
   * @var string
   */
  public $label;
  /**
   * @var string
   */
  public $name;
  /**
   * @var int
   */
  public $number;
  /**
   * If set, gives the index of a oneof in the containing type's oneof_decl
   * list. This field is a member of that oneof.
   *
   * @var int
   */
  public $oneofIndex;
  /**
   * If true, this is a proto3 "optional". When a proto3 field is optional, it
   * tracks presence regardless of field type. When proto3_optional is true,
   * this field must belong to a oneof to signal to old proto3 clients that
   * presence is tracked for this field. This oneof is known as a "synthetic"
   * oneof, and this field must be its sole member (each proto3 optional field
   * gets its own synthetic oneof). Synthetic oneofs exist in the descriptor
   * only, and do not generate any API. Synthetic oneofs must be ordered after
   * all "real" oneofs. For message fields, proto3_optional doesn't create any
   * semantic change, since non-repeated message fields always track presence.
   * However it still indicates the semantic detail of whether the user wrote
   * "optional" or not. This can be useful for round-tripping the .proto file.
   * For consistency we give message fields a synthetic oneof also, even though
   * it is not required to track presence. This is especially important because
   * the parser can't tell if a field is a message or an enum, so it must always
   * create a synthetic oneof. Proto2 optional fields do not set this flag,
   * because they already indicate optional with `LABEL_OPTIONAL`.
   *
   * @var bool
   */
  public $proto3Optional;
  /**
   * If type_name is set, this need not be set. If both this and type_name are
   * set, this must be one of TYPE_ENUM, TYPE_MESSAGE or TYPE_GROUP.
   *
   * @var string
   */
  public $type;
  /**
   * For message and enum types, this is the name of the type. If the name
   * starts with a '.', it is fully-qualified. Otherwise, C++-like scoping rules
   * are used to find the type (i.e. first the nested types within this message
   * are searched, then within the parent, on up to the root namespace).
   *
   * @var string
   */
  public $typeName;

  /**
   * For numeric types, contains the original text representation of the value.
   * For booleans, "true" or "false". For strings, contains the default text
   * contents (not escaped in any way). For bytes, contains the C escaped value.
   * All bytes >= 128 are escaped.
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
   * JSON name of this field. The value is set by protocol compiler. If the user
   * has set a "json_name" option on this field, that option's value will be
   * used. Otherwise, it's deduced from the field's name by converting it to
   * camelCase.
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
   * @param self::LABEL_* $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return self::LABEL_*
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
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
   * If set, gives the index of a oneof in the containing type's oneof_decl
   * list. This field is a member of that oneof.
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
   * If true, this is a proto3 "optional". When a proto3 field is optional, it
   * tracks presence regardless of field type. When proto3_optional is true,
   * this field must belong to a oneof to signal to old proto3 clients that
   * presence is tracked for this field. This oneof is known as a "synthetic"
   * oneof, and this field must be its sole member (each proto3 optional field
   * gets its own synthetic oneof). Synthetic oneofs exist in the descriptor
   * only, and do not generate any API. Synthetic oneofs must be ordered after
   * all "real" oneofs. For message fields, proto3_optional doesn't create any
   * semantic change, since non-repeated message fields always track presence.
   * However it still indicates the semantic detail of whether the user wrote
   * "optional" or not. This can be useful for round-tripping the .proto file.
   * For consistency we give message fields a synthetic oneof also, even though
   * it is not required to track presence. This is especially important because
   * the parser can't tell if a field is a message or an enum, so it must always
   * create a synthetic oneof. Proto2 optional fields do not set this flag,
   * because they already indicate optional with `LABEL_OPTIONAL`.
   *
   * @param bool $proto3Optional
   */
  public function setProto3Optional($proto3Optional)
  {
    $this->proto3Optional = $proto3Optional;
  }
  /**
   * @return bool
   */
  public function getProto3Optional()
  {
    return $this->proto3Optional;
  }
  /**
   * If type_name is set, this need not be set. If both this and type_name are
   * set, this must be one of TYPE_ENUM, TYPE_MESSAGE or TYPE_GROUP.
   *
   * Accepted values: TYPE_DOUBLE, TYPE_FLOAT, TYPE_INT64, TYPE_UINT64,
   * TYPE_INT32, TYPE_FIXED64, TYPE_FIXED32, TYPE_BOOL, TYPE_STRING, TYPE_GROUP,
   * TYPE_MESSAGE, TYPE_BYTES, TYPE_UINT32, TYPE_ENUM, TYPE_SFIXED32,
   * TYPE_SFIXED64, TYPE_SINT32, TYPE_SINT64
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
   * For message and enum types, this is the name of the type. If the name
   * starts with a '.', it is fully-qualified. Otherwise, C++-like scoping rules
   * are used to find the type (i.e. first the nested types within this message
   * are searched, then within the parent, on up to the root namespace).
   *
   * @param string $typeName
   */
  public function setTypeName($typeName)
  {
    $this->typeName = $typeName;
  }
  /**
   * @return string
   */
  public function getTypeName()
  {
    return $this->typeName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Proto2FieldDescriptorProto::class, 'Google_Service_ChromePolicy_Proto2FieldDescriptorProto');
