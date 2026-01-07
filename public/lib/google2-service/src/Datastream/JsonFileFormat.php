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

namespace Google\Service\Datastream;

class JsonFileFormat extends \Google\Model
{
  /**
   * Unspecified json file compression.
   */
  public const COMPRESSION_JSON_COMPRESSION_UNSPECIFIED = 'JSON_COMPRESSION_UNSPECIFIED';
  /**
   * Do not compress JSON file.
   */
  public const COMPRESSION_NO_COMPRESSION = 'NO_COMPRESSION';
  /**
   * Gzip compression.
   */
  public const COMPRESSION_GZIP = 'GZIP';
  /**
   * Unspecified schema file format.
   */
  public const SCHEMA_FILE_FORMAT_SCHEMA_FILE_FORMAT_UNSPECIFIED = 'SCHEMA_FILE_FORMAT_UNSPECIFIED';
  /**
   * Do not attach schema file.
   */
  public const SCHEMA_FILE_FORMAT_NO_SCHEMA_FILE = 'NO_SCHEMA_FILE';
  /**
   * Avro schema format.
   */
  public const SCHEMA_FILE_FORMAT_AVRO_SCHEMA_FILE = 'AVRO_SCHEMA_FILE';
  /**
   * Compression of the loaded JSON file.
   *
   * @var string
   */
  public $compression;
  /**
   * The schema file format along JSON data files.
   *
   * @var string
   */
  public $schemaFileFormat;

  /**
   * Compression of the loaded JSON file.
   *
   * Accepted values: JSON_COMPRESSION_UNSPECIFIED, NO_COMPRESSION, GZIP
   *
   * @param self::COMPRESSION_* $compression
   */
  public function setCompression($compression)
  {
    $this->compression = $compression;
  }
  /**
   * @return self::COMPRESSION_*
   */
  public function getCompression()
  {
    return $this->compression;
  }
  /**
   * The schema file format along JSON data files.
   *
   * Accepted values: SCHEMA_FILE_FORMAT_UNSPECIFIED, NO_SCHEMA_FILE,
   * AVRO_SCHEMA_FILE
   *
   * @param self::SCHEMA_FILE_FORMAT_* $schemaFileFormat
   */
  public function setSchemaFileFormat($schemaFileFormat)
  {
    $this->schemaFileFormat = $schemaFileFormat;
  }
  /**
   * @return self::SCHEMA_FILE_FORMAT_*
   */
  public function getSchemaFileFormat()
  {
    return $this->schemaFileFormat;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JsonFileFormat::class, 'Google_Service_Datastream_JsonFileFormat');
