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

namespace Google\Service\CloudHealthcare;

class ParserConfig extends \Google\Model
{
  /**
   * Unspecified parser version, equivalent to V1.
   */
  public const VERSION_PARSER_VERSION_UNSPECIFIED = 'PARSER_VERSION_UNSPECIFIED';
  /**
   * The `parsed_data` includes every given non-empty message field except the
   * Field Separator (MSH-1) field. As a result, the parsed MSH segment starts
   * with the MSH-2 field and the field numbers are off-by-one with respect to
   * the HL7 standard.
   */
  public const VERSION_V1 = 'V1';
  /**
   * The `parsed_data` includes every given non-empty message field.
   */
  public const VERSION_V2 = 'V2';
  /**
   * This version is the same as V2, with the following change. The
   * `parsed_data` contains unescaped escaped field separators, component
   * separators, sub-component separators, repetition separators, escape
   * characters, and truncation characters. If `schema` is specified, the
   * schematized parser uses improved parsing heuristics compared to previous
   * versions.
   */
  public const VERSION_V3 = 'V3';
  /**
   * Optional. Determines whether messages with no header are allowed.
   *
   * @var bool
   */
  public $allowNullHeader;
  protected $schemaType = SchemaPackage::class;
  protected $schemaDataType = '';
  /**
   * Optional. Byte(s) to use as the segment terminator. If this is unset, '\r'
   * is used as segment terminator, matching the HL7 version 2 specification.
   *
   * @var string
   */
  public $segmentTerminator;
  /**
   * Immutable. Determines the version of both the default parser to be used
   * when `schema` is not given, as well as the schematized parser used when
   * `schema` is specified. This field is immutable after HL7v2 store creation.
   *
   * @var string
   */
  public $version;

  /**
   * Optional. Determines whether messages with no header are allowed.
   *
   * @param bool $allowNullHeader
   */
  public function setAllowNullHeader($allowNullHeader)
  {
    $this->allowNullHeader = $allowNullHeader;
  }
  /**
   * @return bool
   */
  public function getAllowNullHeader()
  {
    return $this->allowNullHeader;
  }
  /**
   * Optional. Schemas used to parse messages in this store, if schematized
   * parsing is desired.
   *
   * @param SchemaPackage $schema
   */
  public function setSchema(SchemaPackage $schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return SchemaPackage
   */
  public function getSchema()
  {
    return $this->schema;
  }
  /**
   * Optional. Byte(s) to use as the segment terminator. If this is unset, '\r'
   * is used as segment terminator, matching the HL7 version 2 specification.
   *
   * @param string $segmentTerminator
   */
  public function setSegmentTerminator($segmentTerminator)
  {
    $this->segmentTerminator = $segmentTerminator;
  }
  /**
   * @return string
   */
  public function getSegmentTerminator()
  {
    return $this->segmentTerminator;
  }
  /**
   * Immutable. Determines the version of both the default parser to be used
   * when `schema` is not given, as well as the schematized parser used when
   * `schema` is specified. This field is immutable after HL7v2 store creation.
   *
   * Accepted values: PARSER_VERSION_UNSPECIFIED, V1, V2, V3
   *
   * @param self::VERSION_* $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return self::VERSION_*
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ParserConfig::class, 'Google_Service_CloudHealthcare_ParserConfig');
