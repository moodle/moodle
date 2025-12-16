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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1SchemaVersion extends \Google\Model
{
  /**
   * Output only. The time when the SchemaVersion was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The user-defined name of the SchemaVersion.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. The GCP labels for the SchemaVersion.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the SchemaVersion. Format: `projects/{proj
   * ect}/locations/{location}/schemas/{schema}/schemaVersions/{schema_version}`
   *
   * @var string
   */
  public $name;
  protected $schemaType = GoogleCloudDocumentaiV1DocumentSchema::class;
  protected $schemaDataType = '';

  /**
   * Output only. The time when the SchemaVersion was created.
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
   * Required. The user-defined name of the SchemaVersion.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. The GCP labels for the SchemaVersion.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. The resource name of the SchemaVersion. Format: `projects/{proj
   * ect}/locations/{location}/schemas/{schema}/schemaVersions/{schema_version}`
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
   * Required. The schema of the SchemaVersion.
   *
   * @param GoogleCloudDocumentaiV1DocumentSchema $schema
   */
  public function setSchema(GoogleCloudDocumentaiV1DocumentSchema $schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentSchema
   */
  public function getSchema()
  {
    return $this->schema;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1SchemaVersion::class, 'Google_Service_Document_GoogleCloudDocumentaiV1SchemaVersion');
