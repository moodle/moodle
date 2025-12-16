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

namespace Google\Service\Bigquery;

class TableFieldSchema extends \Google\Collection
{
  /**
   * Unspecified will default to using ROUND_HALF_AWAY_FROM_ZERO.
   */
  public const ROUNDING_MODE_ROUNDING_MODE_UNSPECIFIED = 'ROUNDING_MODE_UNSPECIFIED';
  /**
   * ROUND_HALF_AWAY_FROM_ZERO rounds half values away from zero when applying
   * precision and scale upon writing of NUMERIC and BIGNUMERIC values. For
   * Scale: 0 1.1, 1.2, 1.3, 1.4 => 1 1.5, 1.6, 1.7, 1.8, 1.9 => 2
   */
  public const ROUNDING_MODE_ROUND_HALF_AWAY_FROM_ZERO = 'ROUND_HALF_AWAY_FROM_ZERO';
  /**
   * ROUND_HALF_EVEN rounds half values to the nearest even value when applying
   * precision and scale upon writing of NUMERIC and BIGNUMERIC values. For
   * Scale: 0 1.1, 1.2, 1.3, 1.4 => 1 1.5 => 2 1.6, 1.7, 1.8, 1.9 => 2 2.5 => 2
   */
  public const ROUNDING_MODE_ROUND_HALF_EVEN = 'ROUND_HALF_EVEN';
  protected $collection_key = 'fields';
  protected $categoriesType = TableFieldSchemaCategories::class;
  protected $categoriesDataType = '';
  /**
   * Optional. Field collation can be set only when the type of field is STRING.
   * The following values are supported: * 'und:ci': undetermined locale, case
   * insensitive. * '': empty string. Default to case-sensitive behavior.
   *
   * @var string
   */
  public $collation;
  protected $dataPoliciesType = DataPolicyOption::class;
  protected $dataPoliciesDataType = 'array';
  /**
   * Optional. A SQL expression to specify the [default value]
   * (https://cloud.google.com/bigquery/docs/default-values) for this field.
   *
   * @var string
   */
  public $defaultValueExpression;
  /**
   * Optional. The field description. The maximum length is 1,024 characters.
   *
   * @var string
   */
  public $description;
  protected $fieldsType = TableFieldSchema::class;
  protected $fieldsDataType = 'array';
  /**
   * Optional. Definition of the foreign data type. Only valid for top-level
   * schema fields (not nested fields). If the type is FOREIGN, this field is
   * required.
   *
   * @var string
   */
  public $foreignTypeDefinition;
  /**
   * Optional. Maximum length of values of this field for STRINGS or BYTES. If
   * max_length is not specified, no maximum length constraint is imposed on
   * this field. If type = "STRING", then max_length represents the maximum
   * UTF-8 length of strings in this field. If type = "BYTES", then max_length
   * represents the maximum number of bytes in this field. It is invalid to set
   * this field if type ≠ "STRING" and ≠ "BYTES".
   *
   * @var string
   */
  public $maxLength;
  /**
   * Optional. The field mode. Possible values include NULLABLE, REQUIRED and
   * REPEATED. The default value is NULLABLE.
   *
   * @var string
   */
  public $mode;
  /**
   * Required. The field name. The name must contain only letters (a-z, A-Z),
   * numbers (0-9), or underscores (_), and must start with a letter or
   * underscore. The maximum length is 300 characters.
   *
   * @var string
   */
  public $name;
  protected $policyTagsType = TableFieldSchemaPolicyTags::class;
  protected $policyTagsDataType = '';
  /**
   * Optional. Precision (maximum number of total digits in base 10) and scale
   * (maximum number of digits in the fractional part in base 10) constraints
   * for values of this field for NUMERIC or BIGNUMERIC. It is invalid to set
   * precision or scale if type ≠ "NUMERIC" and ≠ "BIGNUMERIC". If precision and
   * scale are not specified, no value range constraint is imposed on this field
   * insofar as values are permitted by the type. Values of this NUMERIC or
   * BIGNUMERIC field must be in this range when: * Precision (P) and scale (S)
   * are specified: [-10P-S + 10-S, 10P-S - 10-S] * Precision (P) is specified
   * but not scale (and thus scale is interpreted to be equal to zero): [-10P +
   * 1, 10P - 1]. Acceptable values for precision and scale if both are
   * specified: * If type = "NUMERIC": 1 ≤ precision - scale ≤ 29 and 0 ≤ scale
   * ≤ 9. * If type = "BIGNUMERIC": 1 ≤ precision - scale ≤ 38 and 0 ≤ scale ≤
   * 38. Acceptable values for precision if only precision is specified but not
   * scale (and thus scale is interpreted to be equal to zero): * If type =
   * "NUMERIC": 1 ≤ precision ≤ 29. * If type = "BIGNUMERIC": 1 ≤ precision ≤
   * 38. If scale is specified but not precision, then it is invalid.
   *
   * @var string
   */
  public $precision;
  protected $rangeElementTypeType = TableFieldSchemaRangeElementType::class;
  protected $rangeElementTypeDataType = '';
  /**
   * Optional. Specifies the rounding mode to be used when storing values of
   * NUMERIC and BIGNUMERIC type.
   *
   * @var string
   */
  public $roundingMode;
  /**
   * Optional. See documentation for precision.
   *
   * @var string
   */
  public $scale;
  /**
   * Optional. Precision (maximum number of total digits in base 10) for seconds
   * of TIMESTAMP type. Possible values include: * 6 (Default, for TIMESTAMP
   * type with microsecond precision) * 12 (For TIMESTAMP type with picosecond
   * precision)
   *
   * @var string
   */
  public $timestampPrecision;
  /**
   * Required. The field data type. Possible values include: * STRING * BYTES *
   * INTEGER (or INT64) * FLOAT (or FLOAT64) * BOOLEAN (or BOOL) * TIMESTAMP *
   * DATE * TIME * DATETIME * GEOGRAPHY * NUMERIC * BIGNUMERIC * JSON * RECORD
   * (or STRUCT) * RANGE Use of RECORD/STRUCT indicates that the field contains
   * a nested schema.
   *
   * @var string
   */
  public $type;

  /**
   * Deprecated.
   *
   * @param TableFieldSchemaCategories $categories
   */
  public function setCategories(TableFieldSchemaCategories $categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return TableFieldSchemaCategories
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * Optional. Field collation can be set only when the type of field is STRING.
   * The following values are supported: * 'und:ci': undetermined locale, case
   * insensitive. * '': empty string. Default to case-sensitive behavior.
   *
   * @param string $collation
   */
  public function setCollation($collation)
  {
    $this->collation = $collation;
  }
  /**
   * @return string
   */
  public function getCollation()
  {
    return $this->collation;
  }
  /**
   * Optional. Data policies attached to this field, used for field-level access
   * control.
   *
   * @param DataPolicyOption[] $dataPolicies
   */
  public function setDataPolicies($dataPolicies)
  {
    $this->dataPolicies = $dataPolicies;
  }
  /**
   * @return DataPolicyOption[]
   */
  public function getDataPolicies()
  {
    return $this->dataPolicies;
  }
  /**
   * Optional. A SQL expression to specify the [default value]
   * (https://cloud.google.com/bigquery/docs/default-values) for this field.
   *
   * @param string $defaultValueExpression
   */
  public function setDefaultValueExpression($defaultValueExpression)
  {
    $this->defaultValueExpression = $defaultValueExpression;
  }
  /**
   * @return string
   */
  public function getDefaultValueExpression()
  {
    return $this->defaultValueExpression;
  }
  /**
   * Optional. The field description. The maximum length is 1,024 characters.
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
   * Optional. Describes the nested schema fields if the type property is set to
   * RECORD.
   *
   * @param TableFieldSchema[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return TableFieldSchema[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * Optional. Definition of the foreign data type. Only valid for top-level
   * schema fields (not nested fields). If the type is FOREIGN, this field is
   * required.
   *
   * @param string $foreignTypeDefinition
   */
  public function setForeignTypeDefinition($foreignTypeDefinition)
  {
    $this->foreignTypeDefinition = $foreignTypeDefinition;
  }
  /**
   * @return string
   */
  public function getForeignTypeDefinition()
  {
    return $this->foreignTypeDefinition;
  }
  /**
   * Optional. Maximum length of values of this field for STRINGS or BYTES. If
   * max_length is not specified, no maximum length constraint is imposed on
   * this field. If type = "STRING", then max_length represents the maximum
   * UTF-8 length of strings in this field. If type = "BYTES", then max_length
   * represents the maximum number of bytes in this field. It is invalid to set
   * this field if type ≠ "STRING" and ≠ "BYTES".
   *
   * @param string $maxLength
   */
  public function setMaxLength($maxLength)
  {
    $this->maxLength = $maxLength;
  }
  /**
   * @return string
   */
  public function getMaxLength()
  {
    return $this->maxLength;
  }
  /**
   * Optional. The field mode. Possible values include NULLABLE, REQUIRED and
   * REPEATED. The default value is NULLABLE.
   *
   * @param string $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return string
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Required. The field name. The name must contain only letters (a-z, A-Z),
   * numbers (0-9), or underscores (_), and must start with a letter or
   * underscore. The maximum length is 300 characters.
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
   * Optional. The policy tags attached to this field, used for field-level
   * access control. If not set, defaults to empty policy_tags.
   *
   * @param TableFieldSchemaPolicyTags $policyTags
   */
  public function setPolicyTags(TableFieldSchemaPolicyTags $policyTags)
  {
    $this->policyTags = $policyTags;
  }
  /**
   * @return TableFieldSchemaPolicyTags
   */
  public function getPolicyTags()
  {
    return $this->policyTags;
  }
  /**
   * Optional. Precision (maximum number of total digits in base 10) and scale
   * (maximum number of digits in the fractional part in base 10) constraints
   * for values of this field for NUMERIC or BIGNUMERIC. It is invalid to set
   * precision or scale if type ≠ "NUMERIC" and ≠ "BIGNUMERIC". If precision and
   * scale are not specified, no value range constraint is imposed on this field
   * insofar as values are permitted by the type. Values of this NUMERIC or
   * BIGNUMERIC field must be in this range when: * Precision (P) and scale (S)
   * are specified: [-10P-S + 10-S, 10P-S - 10-S] * Precision (P) is specified
   * but not scale (and thus scale is interpreted to be equal to zero): [-10P +
   * 1, 10P - 1]. Acceptable values for precision and scale if both are
   * specified: * If type = "NUMERIC": 1 ≤ precision - scale ≤ 29 and 0 ≤ scale
   * ≤ 9. * If type = "BIGNUMERIC": 1 ≤ precision - scale ≤ 38 and 0 ≤ scale ≤
   * 38. Acceptable values for precision if only precision is specified but not
   * scale (and thus scale is interpreted to be equal to zero): * If type =
   * "NUMERIC": 1 ≤ precision ≤ 29. * If type = "BIGNUMERIC": 1 ≤ precision ≤
   * 38. If scale is specified but not precision, then it is invalid.
   *
   * @param string $precision
   */
  public function setPrecision($precision)
  {
    $this->precision = $precision;
  }
  /**
   * @return string
   */
  public function getPrecision()
  {
    return $this->precision;
  }
  /**
   * Represents the type of a field element.
   *
   * @param TableFieldSchemaRangeElementType $rangeElementType
   */
  public function setRangeElementType(TableFieldSchemaRangeElementType $rangeElementType)
  {
    $this->rangeElementType = $rangeElementType;
  }
  /**
   * @return TableFieldSchemaRangeElementType
   */
  public function getRangeElementType()
  {
    return $this->rangeElementType;
  }
  /**
   * Optional. Specifies the rounding mode to be used when storing values of
   * NUMERIC and BIGNUMERIC type.
   *
   * Accepted values: ROUNDING_MODE_UNSPECIFIED, ROUND_HALF_AWAY_FROM_ZERO,
   * ROUND_HALF_EVEN
   *
   * @param self::ROUNDING_MODE_* $roundingMode
   */
  public function setRoundingMode($roundingMode)
  {
    $this->roundingMode = $roundingMode;
  }
  /**
   * @return self::ROUNDING_MODE_*
   */
  public function getRoundingMode()
  {
    return $this->roundingMode;
  }
  /**
   * Optional. See documentation for precision.
   *
   * @param string $scale
   */
  public function setScale($scale)
  {
    $this->scale = $scale;
  }
  /**
   * @return string
   */
  public function getScale()
  {
    return $this->scale;
  }
  /**
   * Optional. Precision (maximum number of total digits in base 10) for seconds
   * of TIMESTAMP type. Possible values include: * 6 (Default, for TIMESTAMP
   * type with microsecond precision) * 12 (For TIMESTAMP type with picosecond
   * precision)
   *
   * @param string $timestampPrecision
   */
  public function setTimestampPrecision($timestampPrecision)
  {
    $this->timestampPrecision = $timestampPrecision;
  }
  /**
   * @return string
   */
  public function getTimestampPrecision()
  {
    return $this->timestampPrecision;
  }
  /**
   * Required. The field data type. Possible values include: * STRING * BYTES *
   * INTEGER (or INT64) * FLOAT (or FLOAT64) * BOOLEAN (or BOOL) * TIMESTAMP *
   * DATE * TIME * DATETIME * GEOGRAPHY * NUMERIC * BIGNUMERIC * JSON * RECORD
   * (or STRUCT) * RANGE Use of RECORD/STRUCT indicates that the field contains
   * a nested schema.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableFieldSchema::class, 'Google_Service_Bigquery_TableFieldSchema');
