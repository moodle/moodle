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

class GoogleCloudAiplatformV1Schema extends \Google\Collection
{
  /**
   * Not specified, should not be used.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * OpenAPI string type
   */
  public const TYPE_STRING = 'STRING';
  /**
   * OpenAPI number type
   */
  public const TYPE_NUMBER = 'NUMBER';
  /**
   * OpenAPI integer type
   */
  public const TYPE_INTEGER = 'INTEGER';
  /**
   * OpenAPI boolean type
   */
  public const TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * OpenAPI array type
   */
  public const TYPE_ARRAY = 'ARRAY';
  /**
   * OpenAPI object type
   */
  public const TYPE_OBJECT = 'OBJECT';
  /**
   * Null type
   */
  public const TYPE_NULL = 'NULL';
  protected $collection_key = 'required';
  /**
   * Optional. Can either be a boolean or an object; controls the presence of
   * additional properties.
   *
   * @var array
   */
  public $additionalProperties;
  protected $anyOfType = GoogleCloudAiplatformV1Schema::class;
  protected $anyOfDataType = 'array';
  /**
   * Optional. Default value of the data.
   *
   * @var array
   */
  public $default;
  protected $defsType = GoogleCloudAiplatformV1Schema::class;
  protected $defsDataType = 'map';
  /**
   * Optional. The description of the data.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Possible values of the element of primitive type with enum
   * format. Examples: 1. We can define direction as : {type:STRING,
   * format:enum, enum:["EAST", NORTH", "SOUTH", "WEST"]} 2. We can define
   * apartment number as : {type:INTEGER, format:enum, enum:["101", "201",
   * "301"]}
   *
   * @var string[]
   */
  public $enum;
  /**
   * Optional. Example of the object. Will only populated when the object is the
   * root.
   *
   * @var array
   */
  public $example;
  /**
   * Optional. The format of the data. Supported formats: for NUMBER type:
   * "float", "double" for INTEGER type: "int32", "int64" for STRING type:
   * "email", "byte", etc
   *
   * @var string
   */
  public $format;
  protected $itemsType = GoogleCloudAiplatformV1Schema::class;
  protected $itemsDataType = '';
  /**
   * Optional. Maximum number of the elements for Type.ARRAY.
   *
   * @var string
   */
  public $maxItems;
  /**
   * Optional. Maximum length of the Type.STRING
   *
   * @var string
   */
  public $maxLength;
  /**
   * Optional. Maximum number of the properties for Type.OBJECT.
   *
   * @var string
   */
  public $maxProperties;
  /**
   * Optional. Maximum value of the Type.INTEGER and Type.NUMBER
   *
   * @var 
   */
  public $maximum;
  /**
   * Optional. Minimum number of the elements for Type.ARRAY.
   *
   * @var string
   */
  public $minItems;
  /**
   * Optional. SCHEMA FIELDS FOR TYPE STRING Minimum length of the Type.STRING
   *
   * @var string
   */
  public $minLength;
  /**
   * Optional. Minimum number of the properties for Type.OBJECT.
   *
   * @var string
   */
  public $minProperties;
  /**
   * Optional. SCHEMA FIELDS FOR TYPE INTEGER and NUMBER Minimum value of the
   * Type.INTEGER and Type.NUMBER
   *
   * @var 
   */
  public $minimum;
  /**
   * Optional. Indicates if the value may be null.
   *
   * @var bool
   */
  public $nullable;
  /**
   * Optional. Pattern of the Type.STRING to restrict a string to a regular
   * expression.
   *
   * @var string
   */
  public $pattern;
  protected $propertiesType = GoogleCloudAiplatformV1Schema::class;
  protected $propertiesDataType = 'map';
  /**
   * Optional. The order of the properties. Not a standard field in open api
   * spec. Only used to support the order of the properties.
   *
   * @var string[]
   */
  public $propertyOrdering;
  /**
   * Optional. Allows indirect references between schema nodes. The value should
   * be a valid reference to a child of the root `defs`. For example, the
   * following schema defines a reference to a schema node named "Pet": type:
   * object properties: pet: ref: #/defs/Pet defs: Pet: type: object properties:
   * name: type: string The value of the "pet" property is a reference to the
   * schema node named "Pet". See details in https://json-
   * schema.org/understanding-json-schema/structuring
   *
   * @var string
   */
  public $ref;
  /**
   * Optional. Required properties of Type.OBJECT.
   *
   * @var string[]
   */
  public $required;
  /**
   * Optional. The title of the Schema.
   *
   * @var string
   */
  public $title;
  /**
   * Optional. The type of the data.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. Can either be a boolean or an object; controls the presence of
   * additional properties.
   *
   * @param array $additionalProperties
   */
  public function setAdditionalProperties($additionalProperties)
  {
    $this->additionalProperties = $additionalProperties;
  }
  /**
   * @return array
   */
  public function getAdditionalProperties()
  {
    return $this->additionalProperties;
  }
  /**
   * Optional. The value should be validated against any (one or more) of the
   * subschemas in the list.
   *
   * @param GoogleCloudAiplatformV1Schema[] $anyOf
   */
  public function setAnyOf($anyOf)
  {
    $this->anyOf = $anyOf;
  }
  /**
   * @return GoogleCloudAiplatformV1Schema[]
   */
  public function getAnyOf()
  {
    return $this->anyOf;
  }
  /**
   * Optional. Default value of the data.
   *
   * @param array $default
   */
  public function setDefault($default)
  {
    $this->default = $default;
  }
  /**
   * @return array
   */
  public function getDefault()
  {
    return $this->default;
  }
  /**
   * Optional. A map of definitions for use by `ref` Only allowed at the root of
   * the schema.
   *
   * @param GoogleCloudAiplatformV1Schema[] $defs
   */
  public function setDefs($defs)
  {
    $this->defs = $defs;
  }
  /**
   * @return GoogleCloudAiplatformV1Schema[]
   */
  public function getDefs()
  {
    return $this->defs;
  }
  /**
   * Optional. The description of the data.
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
   * Optional. Possible values of the element of primitive type with enum
   * format. Examples: 1. We can define direction as : {type:STRING,
   * format:enum, enum:["EAST", NORTH", "SOUTH", "WEST"]} 2. We can define
   * apartment number as : {type:INTEGER, format:enum, enum:["101", "201",
   * "301"]}
   *
   * @param string[] $enum
   */
  public function setEnum($enum)
  {
    $this->enum = $enum;
  }
  /**
   * @return string[]
   */
  public function getEnum()
  {
    return $this->enum;
  }
  /**
   * Optional. Example of the object. Will only populated when the object is the
   * root.
   *
   * @param array $example
   */
  public function setExample($example)
  {
    $this->example = $example;
  }
  /**
   * @return array
   */
  public function getExample()
  {
    return $this->example;
  }
  /**
   * Optional. The format of the data. Supported formats: for NUMBER type:
   * "float", "double" for INTEGER type: "int32", "int64" for STRING type:
   * "email", "byte", etc
   *
   * @param string $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return string
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * Optional. SCHEMA FIELDS FOR TYPE ARRAY Schema of the elements of
   * Type.ARRAY.
   *
   * @param GoogleCloudAiplatformV1Schema $items
   */
  public function setItems(GoogleCloudAiplatformV1Schema $items)
  {
    $this->items = $items;
  }
  /**
   * @return GoogleCloudAiplatformV1Schema
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Optional. Maximum number of the elements for Type.ARRAY.
   *
   * @param string $maxItems
   */
  public function setMaxItems($maxItems)
  {
    $this->maxItems = $maxItems;
  }
  /**
   * @return string
   */
  public function getMaxItems()
  {
    return $this->maxItems;
  }
  /**
   * Optional. Maximum length of the Type.STRING
   *
   * @param string $maxLength
   */
  public function setMaxLength($maxLength)
  {
    $this->maxLength = $maxLength;
  }
  /**
   * @return string
   */
  public function getMaxLength()
  {
    return $this->maxLength;
  }
  /**
   * Optional. Maximum number of the properties for Type.OBJECT.
   *
   * @param string $maxProperties
   */
  public function setMaxProperties($maxProperties)
  {
    $this->maxProperties = $maxProperties;
  }
  /**
   * @return string
   */
  public function getMaxProperties()
  {
    return $this->maxProperties;
  }
  public function setMaximum($maximum)
  {
    $this->maximum = $maximum;
  }
  public function getMaximum()
  {
    return $this->maximum;
  }
  /**
   * Optional. Minimum number of the elements for Type.ARRAY.
   *
   * @param string $minItems
   */
  public function setMinItems($minItems)
  {
    $this->minItems = $minItems;
  }
  /**
   * @return string
   */
  public function getMinItems()
  {
    return $this->minItems;
  }
  /**
   * Optional. SCHEMA FIELDS FOR TYPE STRING Minimum length of the Type.STRING
   *
   * @param string $minLength
   */
  public function setMinLength($minLength)
  {
    $this->minLength = $minLength;
  }
  /**
   * @return string
   */
  public function getMinLength()
  {
    return $this->minLength;
  }
  /**
   * Optional. Minimum number of the properties for Type.OBJECT.
   *
   * @param string $minProperties
   */
  public function setMinProperties($minProperties)
  {
    $this->minProperties = $minProperties;
  }
  /**
   * @return string
   */
  public function getMinProperties()
  {
    return $this->minProperties;
  }
  public function setMinimum($minimum)
  {
    $this->minimum = $minimum;
  }
  public function getMinimum()
  {
    return $this->minimum;
  }
  /**
   * Optional. Indicates if the value may be null.
   *
   * @param bool $nullable
   */
  public function setNullable($nullable)
  {
    $this->nullable = $nullable;
  }
  /**
   * @return bool
   */
  public function getNullable()
  {
    return $this->nullable;
  }
  /**
   * Optional. Pattern of the Type.STRING to restrict a string to a regular
   * expression.
   *
   * @param string $pattern
   */
  public function setPattern($pattern)
  {
    $this->pattern = $pattern;
  }
  /**
   * @return string
   */
  public function getPattern()
  {
    return $this->pattern;
  }
  /**
   * Optional. SCHEMA FIELDS FOR TYPE OBJECT Properties of Type.OBJECT.
   *
   * @param GoogleCloudAiplatformV1Schema[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return GoogleCloudAiplatformV1Schema[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Optional. The order of the properties. Not a standard field in open api
   * spec. Only used to support the order of the properties.
   *
   * @param string[] $propertyOrdering
   */
  public function setPropertyOrdering($propertyOrdering)
  {
    $this->propertyOrdering = $propertyOrdering;
  }
  /**
   * @return string[]
   */
  public function getPropertyOrdering()
  {
    return $this->propertyOrdering;
  }
  /**
   * Optional. Allows indirect references between schema nodes. The value should
   * be a valid reference to a child of the root `defs`. For example, the
   * following schema defines a reference to a schema node named "Pet": type:
   * object properties: pet: ref: #/defs/Pet defs: Pet: type: object properties:
   * name: type: string The value of the "pet" property is a reference to the
   * schema node named "Pet". See details in https://json-
   * schema.org/understanding-json-schema/structuring
   *
   * @param string $ref
   */
  public function setRef($ref)
  {
    $this->ref = $ref;
  }
  /**
   * @return string
   */
  public function getRef()
  {
    return $this->ref;
  }
  /**
   * Optional. Required properties of Type.OBJECT.
   *
   * @param string[] $required
   */
  public function setRequired($required)
  {
    $this->required = $required;
  }
  /**
   * @return string[]
   */
  public function getRequired()
  {
    return $this->required;
  }
  /**
   * Optional. The title of the Schema.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Optional. The type of the data.
   *
   * Accepted values: TYPE_UNSPECIFIED, STRING, NUMBER, INTEGER, BOOLEAN, ARRAY,
   * OBJECT, NULL
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Schema::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Schema');
