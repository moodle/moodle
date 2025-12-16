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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1BigtableOptionsBigtableColumn extends \Google\Model
{
  /**
   * The encoding is unspecified.
   */
  public const ENCODING_ENCODING_UNSPECIFIED = 'ENCODING_UNSPECIFIED';
  /**
   * Text encoding.
   */
  public const ENCODING_TEXT = 'TEXT';
  /**
   * Binary encoding.
   */
  public const ENCODING_BINARY = 'BINARY';
  /**
   * The type is unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * String type.
   */
  public const TYPE_STRING = 'STRING';
  /**
   * Numerical type.
   */
  public const TYPE_NUMBER = 'NUMBER';
  /**
   * Integer type.
   */
  public const TYPE_INTEGER = 'INTEGER';
  /**
   * Variable length integer type.
   */
  public const TYPE_VAR_INTEGER = 'VAR_INTEGER';
  /**
   * BigDecimal type.
   */
  public const TYPE_BIG_NUMERIC = 'BIG_NUMERIC';
  /**
   * Boolean type.
   */
  public const TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * JSON type.
   */
  public const TYPE_JSON = 'JSON';
  /**
   * The encoding mode of the values when the type is not `STRING`. Acceptable
   * encoding values are: * `TEXT`: indicates values are alphanumeric text
   * strings. * `BINARY`: indicates values are encoded using `HBase
   * Bytes.toBytes` family of functions. This can be overridden for a specific
   * column by listing that column in `columns` and specifying an encoding for
   * it.
   *
   * @var string
   */
  public $encoding;
  /**
   * The field name to use for this column in the document. The name has to
   * match the pattern `a-zA-Z0-9*`. If not set, it is parsed from the qualifier
   * bytes with best effort. However, due to different naming patterns, field
   * name collisions could happen, where parsing behavior is undefined.
   *
   * @var string
   */
  public $fieldName;
  /**
   * Required. Qualifier of the column. If it cannot be decoded with utf-8, use
   * a base-64 encoded string instead.
   *
   * @var string
   */
  public $qualifier;
  /**
   * The type of values in this column family. The values are expected to be
   * encoded using `HBase Bytes.toBytes` function when the encoding value is set
   * to `BINARY`.
   *
   * @var string
   */
  public $type;

  /**
   * The encoding mode of the values when the type is not `STRING`. Acceptable
   * encoding values are: * `TEXT`: indicates values are alphanumeric text
   * strings. * `BINARY`: indicates values are encoded using `HBase
   * Bytes.toBytes` family of functions. This can be overridden for a specific
   * column by listing that column in `columns` and specifying an encoding for
   * it.
   *
   * Accepted values: ENCODING_UNSPECIFIED, TEXT, BINARY
   *
   * @param self::ENCODING_* $encoding
   */
  public function setEncoding($encoding)
  {
    $this->encoding = $encoding;
  }
  /**
   * @return self::ENCODING_*
   */
  public function getEncoding()
  {
    return $this->encoding;
  }
  /**
   * The field name to use for this column in the document. The name has to
   * match the pattern `a-zA-Z0-9*`. If not set, it is parsed from the qualifier
   * bytes with best effort. However, due to different naming patterns, field
   * name collisions could happen, where parsing behavior is undefined.
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
   * Required. Qualifier of the column. If it cannot be decoded with utf-8, use
   * a base-64 encoded string instead.
   *
   * @param string $qualifier
   */
  public function setQualifier($qualifier)
  {
    $this->qualifier = $qualifier;
  }
  /**
   * @return string
   */
  public function getQualifier()
  {
    return $this->qualifier;
  }
  /**
   * The type of values in this column family. The values are expected to be
   * encoded using `HBase Bytes.toBytes` function when the encoding value is set
   * to `BINARY`.
   *
   * Accepted values: TYPE_UNSPECIFIED, STRING, NUMBER, INTEGER, VAR_INTEGER,
   * BIG_NUMERIC, BOOLEAN, JSON
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1BigtableOptionsBigtableColumn::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1BigtableOptionsBigtableColumn');
