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

class GoogleCloudDocumentaiUiv1beta3SchemaEntityType extends \Google\Collection
{
  /**
   * Unspecified method. It defaults to `EXTRACT`.
   */
  public const METHOD_METHOD_UNSPECIFIED = 'METHOD_UNSPECIFIED';
  /**
   * The entity's value is directly extracted as-is from the document text.
   */
  public const METHOD_EXTRACT = 'EXTRACT';
  /**
   * The entity's value is derived through inference and is not necessarily an
   * exact text extraction from the document.
   */
  public const METHOD_DERIVE = 'DERIVE';
  /**
   * Unspecified occurrence type.
   */
  public const OCCURRENCE_TYPE_OCCURRENCE_TYPE_UNSPECIFIED = 'OCCURRENCE_TYPE_UNSPECIFIED';
  /**
   * The entity type will appear zero times or once.
   */
  public const OCCURRENCE_TYPE_OPTIONAL_ONCE = 'OPTIONAL_ONCE';
  /**
   * The entity type will appear zero or multiple times.
   */
  public const OCCURRENCE_TYPE_OPTIONAL_MULTIPLE = 'OPTIONAL_MULTIPLE';
  /**
   * The entity type will only appear exactly once.
   */
  public const OCCURRENCE_TYPE_REQUIRED_ONCE = 'REQUIRED_ONCE';
  /**
   * The entity type will appear once or more times.
   */
  public const OCCURRENCE_TYPE_REQUIRED_MULTIPLE = 'REQUIRED_MULTIPLE';
  /**
   * Unspecified source.
   */
  public const SOURCE_SOURCE_UNSPECIFIED = 'SOURCE_UNSPECIFIED';
  /**
   * The entity type is in the predefined schema of a pretrained version of a
   * processor.
   */
  public const SOURCE_PREDEFINED = 'PREDEFINED';
  /**
   * The entity type is added by the users either: - during an uptraining of an
   * existing processor, or - during the process of creating a customized
   * processor.
   */
  public const SOURCE_USER_INPUT = 'USER_INPUT';
  protected $collection_key = 'properties';
  /**
   * @var string
   */
  public $baseType;
  /**
   * Description of the entity type.
   *
   * @var string
   */
  public $description;
  /**
   * If specified, lists all the possible values for this entity.
   *
   * @var string[]
   */
  public $enumValues;
  /**
   * If the entity type is hidden in the schema. This provides the functionality
   * to temporally "disable" an entity without deleting it.
   *
   * @var bool
   */
  public $hide;
  /**
   * Specifies how the entity's value is obtained.
   *
   * @var string
   */
  public $method;
  /**
   * Occurrence type limits the number of times an entity type appears in the
   * document.
   *
   * @var string
   */
  public $occurrenceType;
  protected $propertiesType = GoogleCloudDocumentaiUiv1beta3SchemaEntityType::class;
  protected $propertiesDataType = 'array';
  /**
   * Source of this entity type.
   *
   * @var string
   */
  public $source;
  /**
   * Name of the type. It must satisfy the following constraints: 1. Must be
   * unique within the set of same level types (with case-insensitive match). 2.
   * Maximum 64 characters. 3. Must start with a letter. 4. Allowed characters:
   * ASCII letters [a-zA-Z], ASCII digits [0-9], or one of the following
   * punctuation characters: * underscore '_' (recommended) * hyphen '-'
   * (allowed, not recommended) * colon ':' (allowed, not recommended) NOTE:
   * Whitespace characters are not allowed. 5. Cannot end with a punctuation
   * character. 6. Cannot contain the following restricted strings: "google",
   * "DocumentAI" (case-insensitive match). 7. A slash character '/' is reserved
   * as a separator in flattened representations of nested entity types (e.g.,
   * "line_item/amount") in which case each part (e.g., "line_item", "amount")
   * must comply with the rules defined above. We recommend using the snake case
   * ("snake_case") in entity type names.
   *
   * @var string
   */
  public $type;

  /**
   * @param string $baseType
   */
  public function setBaseType($baseType)
  {
    $this->baseType = $baseType;
  }
  /**
   * @return string
   */
  public function getBaseType()
  {
    return $this->baseType;
  }
  /**
   * Description of the entity type.
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
   * If specified, lists all the possible values for this entity.
   *
   * @param string[] $enumValues
   */
  public function setEnumValues($enumValues)
  {
    $this->enumValues = $enumValues;
  }
  /**
   * @return string[]
   */
  public function getEnumValues()
  {
    return $this->enumValues;
  }
  /**
   * If the entity type is hidden in the schema. This provides the functionality
   * to temporally "disable" an entity without deleting it.
   *
   * @param bool $hide
   */
  public function setHide($hide)
  {
    $this->hide = $hide;
  }
  /**
   * @return bool
   */
  public function getHide()
  {
    return $this->hide;
  }
  /**
   * Specifies how the entity's value is obtained.
   *
   * Accepted values: METHOD_UNSPECIFIED, EXTRACT, DERIVE
   *
   * @param self::METHOD_* $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return self::METHOD_*
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * Occurrence type limits the number of times an entity type appears in the
   * document.
   *
   * Accepted values: OCCURRENCE_TYPE_UNSPECIFIED, OPTIONAL_ONCE,
   * OPTIONAL_MULTIPLE, REQUIRED_ONCE, REQUIRED_MULTIPLE
   *
   * @param self::OCCURRENCE_TYPE_* $occurrenceType
   */
  public function setOccurrenceType($occurrenceType)
  {
    $this->occurrenceType = $occurrenceType;
  }
  /**
   * @return self::OCCURRENCE_TYPE_*
   */
  public function getOccurrenceType()
  {
    return $this->occurrenceType;
  }
  /**
   * Describing the nested structure of an entity. An EntityType may consist of
   * several other EntityTypes. For example, in a document there can be an
   * EntityType `ID`, which consists of EntityType `name` and `address`, with
   * corresponding attributes, such as TEXT for both types and ONCE for
   * occurrence types.
   *
   * @param GoogleCloudDocumentaiUiv1beta3SchemaEntityType[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3SchemaEntityType[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Source of this entity type.
   *
   * Accepted values: SOURCE_UNSPECIFIED, PREDEFINED, USER_INPUT
   *
   * @param self::SOURCE_* $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return self::SOURCE_*
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Name of the type. It must satisfy the following constraints: 1. Must be
   * unique within the set of same level types (with case-insensitive match). 2.
   * Maximum 64 characters. 3. Must start with a letter. 4. Allowed characters:
   * ASCII letters [a-zA-Z], ASCII digits [0-9], or one of the following
   * punctuation characters: * underscore '_' (recommended) * hyphen '-'
   * (allowed, not recommended) * colon ':' (allowed, not recommended) NOTE:
   * Whitespace characters are not allowed. 5. Cannot end with a punctuation
   * character. 6. Cannot contain the following restricted strings: "google",
   * "DocumentAI" (case-insensitive match). 7. A slash character '/' is reserved
   * as a separator in flattened representations of nested entity types (e.g.,
   * "line_item/amount") in which case each part (e.g., "line_item", "amount")
   * must comply with the rules defined above. We recommend using the snake case
   * ("snake_case") in entity type names.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiUiv1beta3SchemaEntityType::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3SchemaEntityType');
