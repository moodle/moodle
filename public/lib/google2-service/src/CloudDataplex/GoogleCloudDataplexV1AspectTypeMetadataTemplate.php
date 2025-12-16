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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1AspectTypeMetadataTemplate extends \Google\Collection
{
  protected $collection_key = 'recordFields';
  protected $annotationsType = GoogleCloudDataplexV1AspectTypeMetadataTemplateAnnotations::class;
  protected $annotationsDataType = '';
  protected $arrayItemsType = GoogleCloudDataplexV1AspectTypeMetadataTemplate::class;
  protected $arrayItemsDataType = '';
  protected $constraintsType = GoogleCloudDataplexV1AspectTypeMetadataTemplateConstraints::class;
  protected $constraintsDataType = '';
  protected $enumValuesType = GoogleCloudDataplexV1AspectTypeMetadataTemplateEnumValue::class;
  protected $enumValuesDataType = 'array';
  /**
   * Optional. Index is used to encode Template messages. The value of index can
   * range between 1 and 2,147,483,647. Index must be unique within all fields
   * in a Template. (Nested Templates can reuse indexes). Once a Template is
   * defined, the index cannot be changed, because it identifies the field in
   * the actual storage format. Index is a mandatory field, but it is optional
   * for top level fields, and map/array "values" definitions.
   *
   * @var int
   */
  public $index;
  protected $mapItemsType = GoogleCloudDataplexV1AspectTypeMetadataTemplate::class;
  protected $mapItemsDataType = '';
  /**
   * Required. The name of the field.
   *
   * @var string
   */
  public $name;
  protected $recordFieldsType = GoogleCloudDataplexV1AspectTypeMetadataTemplate::class;
  protected $recordFieldsDataType = 'array';
  /**
   * Required. The datatype of this field. The following values are
   * supported:Primitive types: string int bool double datetime. Must be of the
   * format RFC3339 UTC "Zulu" (Examples: "2014-10-02T15:01:23Z" and
   * "2014-10-02T15:01:23.045123456Z").Complex types: enum array map record
   *
   * @var string
   */
  public $type;
  /**
   * Optional. You can use type id if this definition of the field needs to be
   * reused later. The type id must be unique across the entire template. You
   * can only specify it if the field type is record.
   *
   * @var string
   */
  public $typeId;
  /**
   * Optional. A reference to another field definition (not an inline
   * definition). The value must be equal to the value of an id field defined
   * elsewhere in the MetadataTemplate. Only fields with record type can refer
   * to other fields.
   *
   * @var string
   */
  public $typeRef;

  /**
   * Optional. Specifies annotations on this field.
   *
   * @param GoogleCloudDataplexV1AspectTypeMetadataTemplateAnnotations $annotations
   */
  public function setAnnotations(GoogleCloudDataplexV1AspectTypeMetadataTemplateAnnotations $annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return GoogleCloudDataplexV1AspectTypeMetadataTemplateAnnotations
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Optional. If the type is array, set array_items. array_items can refer to a
   * primitive field or a complex (record only) field. To specify a primitive
   * field, you only need to set name and type in the nested MetadataTemplate.
   * The recommended value for the name field is item, as this isn't used in the
   * actual payload.
   *
   * @param GoogleCloudDataplexV1AspectTypeMetadataTemplate $arrayItems
   */
  public function setArrayItems(GoogleCloudDataplexV1AspectTypeMetadataTemplate $arrayItems)
  {
    $this->arrayItems = $arrayItems;
  }
  /**
   * @return GoogleCloudDataplexV1AspectTypeMetadataTemplate
   */
  public function getArrayItems()
  {
    return $this->arrayItems;
  }
  /**
   * Optional. Specifies the constraints on this field.
   *
   * @param GoogleCloudDataplexV1AspectTypeMetadataTemplateConstraints $constraints
   */
  public function setConstraints(GoogleCloudDataplexV1AspectTypeMetadataTemplateConstraints $constraints)
  {
    $this->constraints = $constraints;
  }
  /**
   * @return GoogleCloudDataplexV1AspectTypeMetadataTemplateConstraints
   */
  public function getConstraints()
  {
    return $this->constraints;
  }
  /**
   * Optional. The list of values for an enum type. You must define it if the
   * type is enum.
   *
   * @param GoogleCloudDataplexV1AspectTypeMetadataTemplateEnumValue[] $enumValues
   */
  public function setEnumValues($enumValues)
  {
    $this->enumValues = $enumValues;
  }
  /**
   * @return GoogleCloudDataplexV1AspectTypeMetadataTemplateEnumValue[]
   */
  public function getEnumValues()
  {
    return $this->enumValues;
  }
  /**
   * Optional. Index is used to encode Template messages. The value of index can
   * range between 1 and 2,147,483,647. Index must be unique within all fields
   * in a Template. (Nested Templates can reuse indexes). Once a Template is
   * defined, the index cannot be changed, because it identifies the field in
   * the actual storage format. Index is a mandatory field, but it is optional
   * for top level fields, and map/array "values" definitions.
   *
   * @param int $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * Optional. If the type is map, set map_items. map_items can refer to a
   * primitive field or a complex (record only) field. To specify a primitive
   * field, you only need to set name and type in the nested MetadataTemplate.
   * The recommended value for the name field is item, as this isn't used in the
   * actual payload.
   *
   * @param GoogleCloudDataplexV1AspectTypeMetadataTemplate $mapItems
   */
  public function setMapItems(GoogleCloudDataplexV1AspectTypeMetadataTemplate $mapItems)
  {
    $this->mapItems = $mapItems;
  }
  /**
   * @return GoogleCloudDataplexV1AspectTypeMetadataTemplate
   */
  public function getMapItems()
  {
    return $this->mapItems;
  }
  /**
   * Required. The name of the field.
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
   * Optional. Field definition. You must specify it if the type is record. It
   * defines the nested fields.
   *
   * @param GoogleCloudDataplexV1AspectTypeMetadataTemplate[] $recordFields
   */
  public function setRecordFields($recordFields)
  {
    $this->recordFields = $recordFields;
  }
  /**
   * @return GoogleCloudDataplexV1AspectTypeMetadataTemplate[]
   */
  public function getRecordFields()
  {
    return $this->recordFields;
  }
  /**
   * Required. The datatype of this field. The following values are
   * supported:Primitive types: string int bool double datetime. Must be of the
   * format RFC3339 UTC "Zulu" (Examples: "2014-10-02T15:01:23Z" and
   * "2014-10-02T15:01:23.045123456Z").Complex types: enum array map record
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
  /**
   * Optional. You can use type id if this definition of the field needs to be
   * reused later. The type id must be unique across the entire template. You
   * can only specify it if the field type is record.
   *
   * @param string $typeId
   */
  public function setTypeId($typeId)
  {
    $this->typeId = $typeId;
  }
  /**
   * @return string
   */
  public function getTypeId()
  {
    return $this->typeId;
  }
  /**
   * Optional. A reference to another field definition (not an inline
   * definition). The value must be equal to the value of an id field defined
   * elsewhere in the MetadataTemplate. Only fields with record type can refer
   * to other fields.
   *
   * @param string $typeRef
   */
  public function setTypeRef($typeRef)
  {
    $this->typeRef = $typeRef;
  }
  /**
   * @return string
   */
  public function getTypeRef()
  {
    return $this->typeRef;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1AspectTypeMetadataTemplate::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1AspectTypeMetadataTemplate');
