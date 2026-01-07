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

class GoogleCloudDocumentaiUiv1beta3DocumentSchemaEntityType extends \Google\Collection
{
  protected $collection_key = 'properties';
  /**
   * The entity type that this type is derived from. For now, one and only one
   * should be set.
   *
   * @var string[]
   */
  public $baseTypes;
  /**
   * The description of the entity type. Could be used to provide more
   * information about the entity type for model calls.
   *
   * @var string
   */
  public $description;
  /**
   * User defined name for the type.
   *
   * @var string
   */
  public $displayName;
  protected $entityTypeMetadataType = GoogleCloudDocumentaiUiv1beta3EntityTypeMetadata::class;
  protected $entityTypeMetadataDataType = '';
  protected $enumValuesType = GoogleCloudDocumentaiUiv1beta3DocumentSchemaEntityTypeEnumValues::class;
  protected $enumValuesDataType = '';
  /**
   * Name of the type. It must be unique within the schema file and cannot be a
   * "Common Type". The following naming conventions are used: - Use
   * `snake_casing`. - Name matching is case-sensitive. - Maximum 64 characters.
   * - Must start with a letter. - Allowed characters: ASCII letters
   * `[a-z0-9_-]`. (For backward compatibility internal infrastructure and
   * tooling can handle any ascii character.) - The `/` is sometimes used to
   * denote a property of a type. For example `line_item/amount`. This
   * convention is deprecated, but will still be honored for backward
   * compatibility.
   *
   * @var string
   */
  public $name;
  protected $propertiesType = GoogleCloudDocumentaiUiv1beta3DocumentSchemaEntityTypeProperty::class;
  protected $propertiesDataType = 'array';

  /**
   * The entity type that this type is derived from. For now, one and only one
   * should be set.
   *
   * @param string[] $baseTypes
   */
  public function setBaseTypes($baseTypes)
  {
    $this->baseTypes = $baseTypes;
  }
  /**
   * @return string[]
   */
  public function getBaseTypes()
  {
    return $this->baseTypes;
  }
  /**
   * The description of the entity type. Could be used to provide more
   * information about the entity type for model calls.
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
   * User defined name for the type.
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
   * Metadata for the entity type.
   *
   * @param GoogleCloudDocumentaiUiv1beta3EntityTypeMetadata $entityTypeMetadata
   */
  public function setEntityTypeMetadata(GoogleCloudDocumentaiUiv1beta3EntityTypeMetadata $entityTypeMetadata)
  {
    $this->entityTypeMetadata = $entityTypeMetadata;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3EntityTypeMetadata
   */
  public function getEntityTypeMetadata()
  {
    return $this->entityTypeMetadata;
  }
  /**
   * If specified, lists all the possible values for this entity. This should
   * not be more than a handful of values. If the number of values is >10 or
   * could change frequently use the `EntityType.value_ontology` field and
   * specify a list of all possible values in a value ontology file.
   *
   * @param GoogleCloudDocumentaiUiv1beta3DocumentSchemaEntityTypeEnumValues $enumValues
   */
  public function setEnumValues(GoogleCloudDocumentaiUiv1beta3DocumentSchemaEntityTypeEnumValues $enumValues)
  {
    $this->enumValues = $enumValues;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3DocumentSchemaEntityTypeEnumValues
   */
  public function getEnumValues()
  {
    return $this->enumValues;
  }
  /**
   * Name of the type. It must be unique within the schema file and cannot be a
   * "Common Type". The following naming conventions are used: - Use
   * `snake_casing`. - Name matching is case-sensitive. - Maximum 64 characters.
   * - Must start with a letter. - Allowed characters: ASCII letters
   * `[a-z0-9_-]`. (For backward compatibility internal infrastructure and
   * tooling can handle any ascii character.) - The `/` is sometimes used to
   * denote a property of a type. For example `line_item/amount`. This
   * convention is deprecated, but will still be honored for backward
   * compatibility.
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
   * Description the nested structure, or composition of an entity.
   *
   * @param GoogleCloudDocumentaiUiv1beta3DocumentSchemaEntityTypeProperty[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3DocumentSchemaEntityTypeProperty[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiUiv1beta3DocumentSchemaEntityType::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3DocumentSchemaEntityType');
