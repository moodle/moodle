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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1MetadataSchema extends \Google\Model
{
  /**
   * Unspecified type for the MetadataSchema.
   */
  public const SCHEMA_TYPE_METADATA_SCHEMA_TYPE_UNSPECIFIED = 'METADATA_SCHEMA_TYPE_UNSPECIFIED';
  /**
   * A type indicating that the MetadataSchema will be used by Artifacts.
   */
  public const SCHEMA_TYPE_ARTIFACT_TYPE = 'ARTIFACT_TYPE';
  /**
   * A typee indicating that the MetadataSchema will be used by Executions.
   */
  public const SCHEMA_TYPE_EXECUTION_TYPE = 'EXECUTION_TYPE';
  /**
   * A state indicating that the MetadataSchema will be used by Contexts.
   */
  public const SCHEMA_TYPE_CONTEXT_TYPE = 'CONTEXT_TYPE';
  /**
   * Output only. Timestamp when this MetadataSchema was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Description of the Metadata Schema
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The resource name of the MetadataSchema.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The raw YAML string representation of the MetadataSchema. The
   * combination of [MetadataSchema.version] and the schema name given by
   * `title` in [MetadataSchema.schema] must be unique within a MetadataStore.
   * The schema is defined as an OpenAPI 3.0.2 [MetadataSchema
   * Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/master/versions/3.0.2.md#schemaObject)
   *
   * @var string
   */
  public $schema;
  /**
   * The type of the MetadataSchema. This is a property that identifies which
   * metadata types will use the MetadataSchema.
   *
   * @var string
   */
  public $schemaType;
  /**
   * The version of the MetadataSchema. The version's format must match the
   * following regular expression: `^[0-9]+.+.+$`, which would allow to
   * order/compare different versions. Example: 1.0.0, 1.0.1, etc.
   *
   * @var string
   */
  public $schemaVersion;

  /**
   * Output only. Timestamp when this MetadataSchema was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Description of the Metadata Schema
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
   * Output only. The resource name of the MetadataSchema.
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
   * Required. The raw YAML string representation of the MetadataSchema. The
   * combination of [MetadataSchema.version] and the schema name given by
   * `title` in [MetadataSchema.schema] must be unique within a MetadataStore.
   * The schema is defined as an OpenAPI 3.0.2 [MetadataSchema
   * Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/master/versions/3.0.2.md#schemaObject)
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
   * The type of the MetadataSchema. This is a property that identifies which
   * metadata types will use the MetadataSchema.
   *
   * Accepted values: METADATA_SCHEMA_TYPE_UNSPECIFIED, ARTIFACT_TYPE,
   * EXECUTION_TYPE, CONTEXT_TYPE
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
   * The version of the MetadataSchema. The version's format must match the
   * following regular expression: `^[0-9]+.+.+$`, which would allow to
   * order/compare different versions. Example: 1.0.0, 1.0.1, etc.
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
class_alias(GoogleCloudAiplatformV1MetadataSchema::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MetadataSchema');
