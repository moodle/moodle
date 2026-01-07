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

namespace Google\Service\AppHub;

class ExtendedMetadataSchema extends \Google\Model
{
  /**
   * Output only. The JSON schema as a string.
   *
   * @var string
   */
  public $jsonSchema;
  /**
   * Identifier. Resource name of the schema. Format:
   * projects//locations//extendedMetadataSchemas/
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The version of the schema. New versions are required to be
   * backwards compatible.
   *
   * @var string
   */
  public $schemaVersion;

  /**
   * Output only. The JSON schema as a string.
   *
   * @param string $jsonSchema
   */
  public function setJsonSchema($jsonSchema)
  {
    $this->jsonSchema = $jsonSchema;
  }
  /**
   * @return string
   */
  public function getJsonSchema()
  {
    return $this->jsonSchema;
  }
  /**
   * Identifier. Resource name of the schema. Format:
   * projects//locations//extendedMetadataSchemas/
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
   * Output only. The version of the schema. New versions are required to be
   * backwards compatible.
   *
   * @param string $schemaVersion
   */
  public function setSchemaVersion($schemaVersion)
  {
    $this->schemaVersion = $schemaVersion;
  }
  /**
   * @return string
   */
  public function getSchemaVersion()
  {
    return $this->schemaVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExtendedMetadataSchema::class, 'Google_Service_AppHub_ExtendedMetadataSchema');
