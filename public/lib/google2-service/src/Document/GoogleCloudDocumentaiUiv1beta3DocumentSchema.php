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

class GoogleCloudDocumentaiUiv1beta3DocumentSchema extends \Google\Collection
{
  protected $collection_key = 'entityTypes';
  /**
   * Description of the schema.
   *
   * @var string
   */
  public $description;
  /**
   * Display name to show to users.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Document level prompt provided by the user. This custom text is
   * injected into the AI model's prompt to provide extra, document-wide
   * guidance for processing.
   *
   * @var string
   */
  public $documentPrompt;
  protected $entityTypesType = GoogleCloudDocumentaiUiv1beta3DocumentSchemaEntityType::class;
  protected $entityTypesDataType = 'array';
  protected $metadataType = GoogleCloudDocumentaiUiv1beta3DocumentSchemaMetadata::class;
  protected $metadataDataType = '';

  /**
   * Description of the schema.
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
   * Display name to show to users.
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
   * Optional. Document level prompt provided by the user. This custom text is
   * injected into the AI model's prompt to provide extra, document-wide
   * guidance for processing.
   *
   * @param string $documentPrompt
   */
  public function setDocumentPrompt($documentPrompt)
  {
    $this->documentPrompt = $documentPrompt;
  }
  /**
   * @return string
   */
  public function getDocumentPrompt()
  {
    return $this->documentPrompt;
  }
  /**
   * Entity types of the schema.
   *
   * @param GoogleCloudDocumentaiUiv1beta3DocumentSchemaEntityType[] $entityTypes
   */
  public function setEntityTypes($entityTypes)
  {
    $this->entityTypes = $entityTypes;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3DocumentSchemaEntityType[]
   */
  public function getEntityTypes()
  {
    return $this->entityTypes;
  }
  /**
   * Metadata of the schema.
   *
   * @param GoogleCloudDocumentaiUiv1beta3DocumentSchemaMetadata $metadata
   */
  public function setMetadata(GoogleCloudDocumentaiUiv1beta3DocumentSchemaMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3DocumentSchemaMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiUiv1beta3DocumentSchema::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3DocumentSchema');
