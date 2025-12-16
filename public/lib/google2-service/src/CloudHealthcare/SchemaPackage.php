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

class SchemaPackage extends \Google\Collection
{
  /**
   * Unspecified schematized parsing type, equivalent to `SOFT_FAIL`.
   */
  public const SCHEMATIZED_PARSING_TYPE_SCHEMATIZED_PARSING_TYPE_UNSPECIFIED = 'SCHEMATIZED_PARSING_TYPE_UNSPECIFIED';
  /**
   * Messages that fail to parse are still stored and ACKed but a parser error
   * is stored in place of the schematized data.
   */
  public const SCHEMATIZED_PARSING_TYPE_SOFT_FAIL = 'SOFT_FAIL';
  /**
   * Messages that fail to parse are rejected from ingestion/insertion and
   * return an error code.
   */
  public const SCHEMATIZED_PARSING_TYPE_HARD_FAIL = 'HARD_FAIL';
  /**
   * Unspecified handling mode, equivalent to FAIL.
   */
  public const UNEXPECTED_SEGMENT_HANDLING_UNEXPECTED_SEGMENT_HANDLING_MODE_UNSPECIFIED = 'UNEXPECTED_SEGMENT_HANDLING_MODE_UNSPECIFIED';
  /**
   * Unexpected segments fail to parse and return an error.
   */
  public const UNEXPECTED_SEGMENT_HANDLING_FAIL = 'FAIL';
  /**
   * Unexpected segments do not fail, but are omitted from the output.
   */
  public const UNEXPECTED_SEGMENT_HANDLING_SKIP = 'SKIP';
  /**
   * Unexpected segments do not fail, but are parsed in place and added to the
   * current group. If a segment has a type definition, it is used, otherwise it
   * is parsed as VARIES.
   */
  public const UNEXPECTED_SEGMENT_HANDLING_PARSE = 'PARSE';
  protected $collection_key = 'types';
  /**
   * Optional. Flag to ignore all min_occurs restrictions in the schema. This
   * means that incoming messages can omit any group, segment, field, component,
   * or subcomponent.
   *
   * @var bool
   */
  public $ignoreMinOccurs;
  protected $schemasType = Hl7SchemaConfig::class;
  protected $schemasDataType = 'array';
  /**
   * Optional. Determines how messages that fail to parse are handled.
   *
   * @var string
   */
  public $schematizedParsingType;
  protected $typesType = Hl7TypesConfig::class;
  protected $typesDataType = 'array';
  /**
   * Optional. Determines how unexpected segments (segments not matched to the
   * schema) are handled.
   *
   * @var string
   */
  public $unexpectedSegmentHandling;

  /**
   * Optional. Flag to ignore all min_occurs restrictions in the schema. This
   * means that incoming messages can omit any group, segment, field, component,
   * or subcomponent.
   *
   * @param bool $ignoreMinOccurs
   */
  public function setIgnoreMinOccurs($ignoreMinOccurs)
  {
    $this->ignoreMinOccurs = $ignoreMinOccurs;
  }
  /**
   * @return bool
   */
  public function getIgnoreMinOccurs()
  {
    return $this->ignoreMinOccurs;
  }
  /**
   * Optional. Schema configs that are layered based on their VersionSources
   * that match the incoming message. Schema configs present in higher indices
   * override those in lower indices with the same message type and trigger
   * event if their VersionSources all match an incoming message.
   *
   * @param Hl7SchemaConfig[] $schemas
   */
  public function setSchemas($schemas)
  {
    $this->schemas = $schemas;
  }
  /**
   * @return Hl7SchemaConfig[]
   */
  public function getSchemas()
  {
    return $this->schemas;
  }
  /**
   * Optional. Determines how messages that fail to parse are handled.
   *
   * Accepted values: SCHEMATIZED_PARSING_TYPE_UNSPECIFIED, SOFT_FAIL, HARD_FAIL
   *
   * @param self::SCHEMATIZED_PARSING_TYPE_* $schematizedParsingType
   */
  public function setSchematizedParsingType($schematizedParsingType)
  {
    $this->schematizedParsingType = $schematizedParsingType;
  }
  /**
   * @return self::SCHEMATIZED_PARSING_TYPE_*
   */
  public function getSchematizedParsingType()
  {
    return $this->schematizedParsingType;
  }
  /**
   * Optional. Schema type definitions that are layered based on their
   * VersionSources that match the incoming message. Type definitions present in
   * higher indices override those in lower indices with the same type name if
   * their VersionSources all match an incoming message.
   *
   * @param Hl7TypesConfig[] $types
   */
  public function setTypes($types)
  {
    $this->types = $types;
  }
  /**
   * @return Hl7TypesConfig[]
   */
  public function getTypes()
  {
    return $this->types;
  }
  /**
   * Optional. Determines how unexpected segments (segments not matched to the
   * schema) are handled.
   *
   * Accepted values: UNEXPECTED_SEGMENT_HANDLING_MODE_UNSPECIFIED, FAIL, SKIP,
   * PARSE
   *
   * @param self::UNEXPECTED_SEGMENT_HANDLING_* $unexpectedSegmentHandling
   */
  public function setUnexpectedSegmentHandling($unexpectedSegmentHandling)
  {
    $this->unexpectedSegmentHandling = $unexpectedSegmentHandling;
  }
  /**
   * @return self::UNEXPECTED_SEGMENT_HANDLING_*
   */
  public function getUnexpectedSegmentHandling()
  {
    return $this->unexpectedSegmentHandling;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SchemaPackage::class, 'Google_Service_CloudHealthcare_SchemaPackage');
