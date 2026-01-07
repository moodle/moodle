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

class BigtableColumn extends \Google\Model
{
  /**
   * Optional. The encoding of the values when the type is not STRING.
   * Acceptable encoding values are: TEXT - indicates values are alphanumeric
   * text strings. BINARY - indicates values are encoded using HBase
   * Bytes.toBytes family of functions. 'encoding' can also be set at the column
   * family level. However, the setting at this level takes precedence if
   * 'encoding' is set at both levels.
   *
   * @var string
   */
  public $encoding;
  /**
   * Optional. If the qualifier is not a valid BigQuery field identifier i.e.
   * does not match a-zA-Z*, a valid identifier must be provided as the column
   * field name and is used as field name in queries.
   *
   * @var string
   */
  public $fieldName;
  /**
   * Optional. If this is set, only the latest version of value in this column
   * are exposed. 'onlyReadLatest' can also be set at the column family level.
   * However, the setting at this level takes precedence if 'onlyReadLatest' is
   * set at both levels.
   *
   * @var bool
   */
  public $onlyReadLatest;
  /**
   * [Required] Qualifier of the column. Columns in the parent column family
   * that has this exact qualifier are exposed as `.` field. If the qualifier is
   * valid UTF-8 string, it can be specified in the qualifier_string field.
   * Otherwise, a base-64 encoded value must be set to qualifier_encoded. The
   * column field name is the same as the column qualifier. However, if the
   * qualifier is not a valid BigQuery field identifier i.e. does not match
   * a-zA-Z*, a valid identifier must be provided as field_name.
   *
   * @var string
   */
  public $qualifierEncoded;
  /**
   * Qualifier string.
   *
   * @var string
   */
  public $qualifierString;
  /**
   * Optional. The type to convert the value in cells of this column. The values
   * are expected to be encoded using HBase Bytes.toBytes function when using
   * the BINARY encoding value. Following BigQuery types are allowed (case-
   * sensitive): * BYTES * STRING * INTEGER * FLOAT * BOOLEAN * JSON Default
   * type is BYTES. 'type' can also be set at the column family level. However,
   * the setting at this level takes precedence if 'type' is set at both levels.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. The encoding of the values when the type is not STRING.
   * Acceptable encoding values are: TEXT - indicates values are alphanumeric
   * text strings. BINARY - indicates values are encoded using HBase
   * Bytes.toBytes family of functions. 'encoding' can also be set at the column
   * family level. However, the setting at this level takes precedence if
   * 'encoding' is set at both levels.
   *
   * @param string $encoding
   */
  public function setEncoding($encoding)
  {
    $this->encoding = $encoding;
  }
  /**
   * @return string
   */
  public function getEncoding()
  {
    return $this->encoding;
  }
  /**
   * Optional. If the qualifier is not a valid BigQuery field identifier i.e.
   * does not match a-zA-Z*, a valid identifier must be provided as the column
   * field name and is used as field name in queries.
   *
   * @param string $fieldName
   */
  public function setFieldName($fieldName)
  {
    $this->fieldName = $fieldName;
  }
  /**
   * @return string
   */
  public function getFieldName()
  {
    return $this->fieldName;
  }
  /**
   * Optional. If this is set, only the latest version of value in this column
   * are exposed. 'onlyReadLatest' can also be set at the column family level.
   * However, the setting at this level takes precedence if 'onlyReadLatest' is
   * set at both levels.
   *
   * @param bool $onlyReadLatest
   */
  public function setOnlyReadLatest($onlyReadLatest)
  {
    $this->onlyReadLatest = $onlyReadLatest;
  }
  /**
   * @return bool
   */
  public function getOnlyReadLatest()
  {
    return $this->onlyReadLatest;
  }
  /**
   * [Required] Qualifier of the column. Columns in the parent column family
   * that has this exact qualifier are exposed as `.` field. If the qualifier is
   * valid UTF-8 string, it can be specified in the qualifier_string field.
   * Otherwise, a base-64 encoded value must be set to qualifier_encoded. The
   * column field name is the same as the column qualifier. However, if the
   * qualifier is not a valid BigQuery field identifier i.e. does not match
   * a-zA-Z*, a valid identifier must be provided as field_name.
   *
   * @param string $qualifierEncoded
   */
  public function setQualifierEncoded($qualifierEncoded)
  {
    $this->qualifierEncoded = $qualifierEncoded;
  }
  /**
   * @return string
   */
  public function getQualifierEncoded()
  {
    return $this->qualifierEncoded;
  }
  /**
   * Qualifier string.
   *
   * @param string $qualifierString
   */
  public function setQualifierString($qualifierString)
  {
    $this->qualifierString = $qualifierString;
  }
  /**
   * @return string
   */
  public function getQualifierString()
  {
    return $this->qualifierString;
  }
  /**
   * Optional. The type to convert the value in cells of this column. The values
   * are expected to be encoded using HBase Bytes.toBytes function when using
   * the BINARY encoding value. Following BigQuery types are allowed (case-
   * sensitive): * BYTES * STRING * INTEGER * FLOAT * BOOLEAN * JSON Default
   * type is BYTES. 'type' can also be set at the column family level. However,
   * the setting at this level takes precedence if 'type' is set at both levels.
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
class_alias(BigtableColumn::class, 'Google_Service_Bigquery_BigtableColumn');
