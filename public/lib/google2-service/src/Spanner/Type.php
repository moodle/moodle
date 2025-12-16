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

namespace Google\Service\Spanner;

class Type extends \Google\Model
{
  /**
   * Not specified.
   */
  public const CODE_TYPE_CODE_UNSPECIFIED = 'TYPE_CODE_UNSPECIFIED';
  /**
   * Encoded as JSON `true` or `false`.
   */
  public const CODE_BOOL = 'BOOL';
  /**
   * Encoded as `string`, in decimal format.
   */
  public const CODE_INT64 = 'INT64';
  /**
   * Encoded as `number`, or the strings `"NaN"`, `"Infinity"`, or
   * `"-Infinity"`.
   */
  public const CODE_FLOAT64 = 'FLOAT64';
  /**
   * Encoded as `number`, or the strings `"NaN"`, `"Infinity"`, or
   * `"-Infinity"`.
   */
  public const CODE_FLOAT32 = 'FLOAT32';
  /**
   * Encoded as `string` in RFC 3339 timestamp format. The time zone must be
   * present, and must be `"Z"`. If the schema has the column option
   * `allow_commit_timestamp=true`, the placeholder string
   * `"spanner.commit_timestamp()"` can be used to instruct the system to insert
   * the commit timestamp associated with the transaction commit.
   */
  public const CODE_TIMESTAMP = 'TIMESTAMP';
  /**
   * Encoded as `string` in RFC 3339 date format.
   */
  public const CODE_DATE = 'DATE';
  /**
   * Encoded as `string`.
   */
  public const CODE_STRING = 'STRING';
  /**
   * Encoded as a base64-encoded `string`, as described in RFC 4648, section 4.
   */
  public const CODE_BYTES = 'BYTES';
  /**
   * Encoded as `list`, where the list elements are represented according to
   * array_element_type.
   */
  public const CODE_ARRAY = 'ARRAY';
  /**
   * Encoded as `list`, where list element `i` is represented according to
   * [struct_type.fields[i]][google.spanner.v1.StructType.fields].
   */
  public const CODE_STRUCT = 'STRUCT';
  /**
   * Encoded as `string`, in decimal format or scientific notation format.
   * Decimal format: `[+-]Digits[.[Digits]]` or `+-.Digits` Scientific notation:
   * `[+-]Digits[.[Digits]][ExponentIndicator[+-]Digits]` or
   * `+-.Digits[ExponentIndicator[+-]Digits]` (ExponentIndicator is `"e"` or
   * `"E"`)
   */
  public const CODE_NUMERIC = 'NUMERIC';
  /**
   * Encoded as a JSON-formatted `string` as described in RFC 7159. The
   * following rules are applied when parsing JSON input: - Whitespace
   * characters are not preserved. - If a JSON object has duplicate keys, only
   * the first key is preserved. - Members of a JSON object are not guaranteed
   * to have their order preserved. - JSON array elements will have their order
   * preserved.
   */
  public const CODE_JSON = 'JSON';
  /**
   * Encoded as a base64-encoded `string`, as described in RFC 4648, section 4.
   */
  public const CODE_PROTO = 'PROTO';
  /**
   * Encoded as `string`, in decimal format.
   */
  public const CODE_ENUM = 'ENUM';
  /**
   * Encoded as `string`, in `ISO8601` duration format -
   * `P[n]Y[n]M[n]DT[n]H[n]M[n[.fraction]]S` where `n` is an integer. For
   * example, `P1Y2M3DT4H5M6.5S` represents time duration of 1 year, 2 months, 3
   * days, 4 hours, 5 minutes, and 6.5 seconds.
   */
  public const CODE_INTERVAL = 'INTERVAL';
  /**
   * Encoded as `string`, in lower-case hexa-decimal format, as described in RFC
   * 9562, section 4.
   */
  public const CODE_UUID = 'UUID';
  /**
   * Not specified.
   */
  public const TYPE_ANNOTATION_TYPE_ANNOTATION_CODE_UNSPECIFIED = 'TYPE_ANNOTATION_CODE_UNSPECIFIED';
  /**
   * PostgreSQL compatible NUMERIC type. This annotation needs to be applied to
   * Type instances having NUMERIC type code to specify that values of this type
   * should be treated as PostgreSQL NUMERIC values. Currently this annotation
   * is always needed for NUMERIC when a client interacts with PostgreSQL-
   * enabled Spanner databases.
   */
  public const TYPE_ANNOTATION_PG_NUMERIC = 'PG_NUMERIC';
  /**
   * PostgreSQL compatible JSONB type. This annotation needs to be applied to
   * Type instances having JSON type code to specify that values of this type
   * should be treated as PostgreSQL JSONB values. Currently this annotation is
   * always needed for JSON when a client interacts with PostgreSQL-enabled
   * Spanner databases.
   */
  public const TYPE_ANNOTATION_PG_JSONB = 'PG_JSONB';
  /**
   * PostgreSQL compatible OID type. This annotation can be used by a client
   * interacting with PostgreSQL-enabled Spanner database to specify that a
   * value should be treated using the semantics of the OID type.
   */
  public const TYPE_ANNOTATION_PG_OID = 'PG_OID';
  protected $arrayElementTypeType = Type::class;
  protected $arrayElementTypeDataType = '';
  /**
   * Required. The TypeCode for this type.
   *
   * @var string
   */
  public $code;
  /**
   * If code == PROTO or code == ENUM, then `proto_type_fqn` is the fully
   * qualified name of the proto type representing the proto/enum definition.
   *
   * @var string
   */
  public $protoTypeFqn;
  protected $structTypeType = StructType::class;
  protected $structTypeDataType = '';
  /**
   * The TypeAnnotationCode that disambiguates SQL type that Spanner will use to
   * represent values of this type during query processing. This is necessary
   * for some type codes because a single TypeCode can be mapped to different
   * SQL types depending on the SQL dialect. type_annotation typically is not
   * needed to process the content of a value (it doesn't affect serialization)
   * and clients can ignore it on the read path.
   *
   * @var string
   */
  public $typeAnnotation;

  /**
   * If code == ARRAY, then `array_element_type` is the type of the array
   * elements.
   *
   * @param Type $arrayElementType
   */
  public function setArrayElementType(Type $arrayElementType)
  {
    $this->arrayElementType = $arrayElementType;
  }
  /**
   * @return Type
   */
  public function getArrayElementType()
  {
    return $this->arrayElementType;
  }
  /**
   * Required. The TypeCode for this type.
   *
   * Accepted values: TYPE_CODE_UNSPECIFIED, BOOL, INT64, FLOAT64, FLOAT32,
   * TIMESTAMP, DATE, STRING, BYTES, ARRAY, STRUCT, NUMERIC, JSON, PROTO, ENUM,
   * INTERVAL, UUID
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * If code == PROTO or code == ENUM, then `proto_type_fqn` is the fully
   * qualified name of the proto type representing the proto/enum definition.
   *
   * @param string $protoTypeFqn
   */
  public function setProtoTypeFqn($protoTypeFqn)
  {
    $this->protoTypeFqn = $protoTypeFqn;
  }
  /**
   * @return string
   */
  public function getProtoTypeFqn()
  {
    return $this->protoTypeFqn;
  }
  /**
   * If code == STRUCT, then `struct_type` provides type information for the
   * struct's fields.
   *
   * @param StructType $structType
   */
  public function setStructType(StructType $structType)
  {
    $this->structType = $structType;
  }
  /**
   * @return StructType
   */
  public function getStructType()
  {
    return $this->structType;
  }
  /**
   * The TypeAnnotationCode that disambiguates SQL type that Spanner will use to
   * represent values of this type during query processing. This is necessary
   * for some type codes because a single TypeCode can be mapped to different
   * SQL types depending on the SQL dialect. type_annotation typically is not
   * needed to process the content of a value (it doesn't affect serialization)
   * and clients can ignore it on the read path.
   *
   * Accepted values: TYPE_ANNOTATION_CODE_UNSPECIFIED, PG_NUMERIC, PG_JSONB,
   * PG_OID
   *
   * @param self::TYPE_ANNOTATION_* $typeAnnotation
   */
  public function setTypeAnnotation($typeAnnotation)
  {
    $this->typeAnnotation = $typeAnnotation;
  }
  /**
   * @return self::TYPE_ANNOTATION_*
   */
  public function getTypeAnnotation()
  {
    return $this->typeAnnotation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Type::class, 'Google_Service_Spanner_Type');
