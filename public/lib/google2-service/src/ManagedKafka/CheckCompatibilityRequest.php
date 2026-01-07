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

namespace Google\Service\ManagedKafka;

class CheckCompatibilityRequest extends \Google\Collection
{
  /**
   * No schema type. The default will be AVRO.
   */
  public const SCHEMA_TYPE_SCHEMA_TYPE_UNSPECIFIED = 'SCHEMA_TYPE_UNSPECIFIED';
  /**
   * Avro schema type.
   */
  public const SCHEMA_TYPE_AVRO = 'AVRO';
  /**
   * JSON schema type.
   */
  public const SCHEMA_TYPE_JSON = 'JSON';
  /**
   * Protobuf schema type.
   */
  public const SCHEMA_TYPE_PROTOBUF = 'PROTOBUF';
  protected $collection_key = 'references';
  protected $referencesType = SchemaReference::class;
  protected $referencesDataType = 'array';
  /**
   * Required. The schema payload
   *
   * @var string
   */
  public $schema;
  /**
   * Optional. The schema type of the schema.
   *
   * @var string
   */
  public $schemaType;
  /**
   * Optional. If true, the response will contain the compatibility check result
   * with reasons for failed checks. The default is false.
   *
   * @var bool
   */
  public $verbose;

  /**
   * Optional. The schema references used by the schema.
   *
   * @param SchemaReference[] $references
   */
  public function setReferences($references)
  {
    $this->references = $references;
  }
  /**
   * @return SchemaReference[]
   */
  public function getReferences()
  {
    return $this->references;
  }
  /**
   * Required. The schema payload
   *
   * @param string $schema
   */
  public function setSchema($schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return string
   */
  public function getSchema()
  {
    return $this->schema;
  }
  /**
   * Optional. The schema type of the schema.
   *
   * Accepted values: SCHEMA_TYPE_UNSPECIFIED, AVRO, JSON, PROTOBUF
   *
   * @param self::SCHEMA_TYPE_* $schemaType
   */
  public function setSchemaType($schemaType)
  {
    $this->schemaType = $schemaType;
  }
  /**
   * @return self::SCHEMA_TYPE_*
   */
  public function getSchemaType()
  {
    return $this->schemaType;
  }
  /**
   * Optional. If true, the response will contain the compatibility check result
   * with reasons for failed checks. The default is false.
   *
   * @param bool $verbose
   */
  public function setVerbose($verbose)
  {
    $this->verbose = $verbose;
  }
  /**
   * @return bool
   */
  public function getVerbose()
  {
    return $this->verbose;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckCompatibilityRequest::class, 'Google_Service_ManagedKafka_CheckCompatibilityRequest');
