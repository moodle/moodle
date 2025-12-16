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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1DocumentSchema extends \Google\Collection
{
  protected $collection_key = 'propertyDefinitions';
  /**
   * Output only. The time when the document schema is created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Schema description.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Name of the schema given by the user. Must be unique per project.
   *
   * @var string
   */
  public $displayName;
  /**
   * Document Type, true refers the document is a folder, otherwise it is a
   * typical document.
   *
   * @var bool
   */
  public $documentIsFolder;
  /**
   * The resource name of the document schema. Format: projects/{project_number}
   * /locations/{location}/documentSchemas/{document_schema_id}. The name is
   * ignored when creating a document schema.
   *
   * @var string
   */
  public $name;
  protected $propertyDefinitionsType = GoogleCloudContentwarehouseV1PropertyDefinition::class;
  protected $propertyDefinitionsDataType = 'array';
  /**
   * Output only. The time when the document schema is last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time when the document schema is created.
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
   * Schema description.
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
   * Required. Name of the schema given by the user. Must be unique per project.
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
   * Document Type, true refers the document is a folder, otherwise it is a
   * typical document.
   *
   * @param bool $documentIsFolder
   */
  public function setDocumentIsFolder($documentIsFolder)
  {
    $this->documentIsFolder = $documentIsFolder;
  }
  /**
   * @return bool
   */
  public function getDocumentIsFolder()
  {
    return $this->documentIsFolder;
  }
  /**
   * The resource name of the document schema. Format: projects/{project_number}
   * /locations/{location}/documentSchemas/{document_schema_id}. The name is
   * ignored when creating a document schema.
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
   * Document details.
   *
   * @param GoogleCloudContentwarehouseV1PropertyDefinition[] $propertyDefinitions
   */
  public function setPropertyDefinitions($propertyDefinitions)
  {
    $this->propertyDefinitions = $propertyDefinitions;
  }
  /**
   * @return GoogleCloudContentwarehouseV1PropertyDefinition[]
   */
  public function getPropertyDefinitions()
  {
    return $this->propertyDefinitions;
  }
  /**
   * Output only. The time when the document schema is last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1DocumentSchema::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1DocumentSchema');
