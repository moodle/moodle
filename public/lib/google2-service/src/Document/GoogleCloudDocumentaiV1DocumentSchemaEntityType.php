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

class GoogleCloudDocumentaiV1DocumentSchemaEntityType extends \Google\Collection
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
   * User defined name for the type.
   *
   * @var string
   */
  public $displayName;
  protected $enumValuesType = GoogleCloudDocumentaiV1DocumentSchemaEntityTypeEnumValues::class;
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
  protected $propertiesType = GoogleCloudDocumentaiV1DocumentSchemaEntityTypeProperty::class;
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
   * If specified, lists all the possible values for this entity. This should
   * not be more than a handful of values. If the number of values is >10 or
   * could change frequently use the `EntityType.value_ontology` field and
   * specify a list of all possible values in a value ontology file.
   *
   * @param GoogleCloudDocumentaiV1DocumentSchemaEntityTypeEnumValues $enumValues
   */
  public function setEnumValues(GoogleCloudDocumentaiV1DocumentSchemaEntityTypeEnumValues $enumValues)
  {
    $this->enumValues = $enumValues;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentSchemaEntityTypeEnumValues
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
   * @param GoogleCloudDocumentaiV1DocumentSchemaEntityTypeProperty[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentSchemaEntityTypeProperty[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentSchemaEntityType::class, 'Google_Service_Document_GoogleCloudDocumentaiV1DocumentSchemaEntityType');
