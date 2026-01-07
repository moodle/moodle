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

namespace Google\Service\CloudSearch;

class PropertyDefinition extends \Google\Model
{
  protected $booleanPropertyOptionsType = BooleanPropertyOptions::class;
  protected $booleanPropertyOptionsDataType = '';
  protected $datePropertyOptionsType = DatePropertyOptions::class;
  protected $datePropertyOptionsDataType = '';
  protected $displayOptionsType = PropertyDisplayOptions::class;
  protected $displayOptionsDataType = '';
  protected $doublePropertyOptionsType = DoublePropertyOptions::class;
  protected $doublePropertyOptionsDataType = '';
  protected $enumPropertyOptionsType = EnumPropertyOptions::class;
  protected $enumPropertyOptionsDataType = '';
  protected $htmlPropertyOptionsType = HtmlPropertyOptions::class;
  protected $htmlPropertyOptionsDataType = '';
  protected $integerPropertyOptionsType = IntegerPropertyOptions::class;
  protected $integerPropertyOptionsDataType = '';
  /**
   * Indicates that the property can be used for generating facets. Cannot be
   * true for properties whose type is object. IsReturnable must be true to set
   * this option. Only supported for boolean, enum, integer, and text
   * properties.
   *
   * @var bool
   */
  public $isFacetable;
  /**
   * Indicates that multiple values are allowed for the property. For example, a
   * document only has one description but can have multiple comments. Cannot be
   * true for properties whose type is a boolean. If set to false, properties
   * that contain more than one value cause the indexing request for that item
   * to be rejected.
   *
   * @var bool
   */
  public $isRepeatable;
  /**
   * Indicates that the property identifies data that should be returned in
   * search results via the Query API. If set to *true*, indicates that Query
   * API users can use matching property fields in results. However, storing
   * fields requires more space allocation and uses more bandwidth for search
   * queries, which impacts performance over large datasets. Set to *true* here
   * only if the field is needed for search results. Cannot be true for
   * properties whose type is an object.
   *
   * @var bool
   */
  public $isReturnable;
  /**
   * Indicates that the property can be used for sorting. Cannot be true for
   * properties that are repeatable. Cannot be true for properties whose type is
   * object. IsReturnable must be true to set this option. Only supported for
   * boolean, date, double, integer, and timestamp properties.
   *
   * @var bool
   */
  public $isSortable;
  /**
   * Indicates that the property can be used for generating query suggestions.
   *
   * @var bool
   */
  public $isSuggestable;
  /**
   * Indicates that users can perform wildcard search for this property. Only
   * supported for Text properties. IsReturnable must be true to set this
   * option. In a given datasource maximum of 5 properties can be marked as
   * is_wildcard_searchable. For more details, see [Define object
   * properties](https://developers.google.com/cloud-search/docs/guides/schema-
   * guide#properties)
   *
   * @var bool
   */
  public $isWildcardSearchable;
  /**
   * The name of the property. Item indexing requests sent to the Indexing API
   * should set the property name equal to this value. For example, if name is
   * *subject_line*, then indexing requests for document items with subject
   * fields should set the name for that field equal to *subject_line*. Use the
   * name as the identifier for the object property. Once registered as a
   * property for an object, you cannot re-use this name for another property
   * within that object. The name must start with a letter and can only contain
   * letters (A-Z, a-z) or numbers (0-9). The maximum length is 256 characters.
   *
   * @var string
   */
  public $name;
  protected $objectPropertyOptionsType = ObjectPropertyOptions::class;
  protected $objectPropertyOptionsDataType = '';
  protected $textPropertyOptionsType = TextPropertyOptions::class;
  protected $textPropertyOptionsDataType = '';
  protected $timestampPropertyOptionsType = TimestampPropertyOptions::class;
  protected $timestampPropertyOptionsDataType = '';

  /**
   * @param BooleanPropertyOptions $booleanPropertyOptions
   */
  public function setBooleanPropertyOptions(BooleanPropertyOptions $booleanPropertyOptions)
  {
    $this->booleanPropertyOptions = $booleanPropertyOptions;
  }
  /**
   * @return BooleanPropertyOptions
   */
  public function getBooleanPropertyOptions()
  {
    return $this->booleanPropertyOptions;
  }
  /**
   * @param DatePropertyOptions $datePropertyOptions
   */
  public function setDatePropertyOptions(DatePropertyOptions $datePropertyOptions)
  {
    $this->datePropertyOptions = $datePropertyOptions;
  }
  /**
   * @return DatePropertyOptions
   */
  public function getDatePropertyOptions()
  {
    return $this->datePropertyOptions;
  }
  /**
   * The options that determine how the property is displayed in the Cloud
   * Search results page if it's specified to be displayed in the object's
   * display options.
   *
   * @param PropertyDisplayOptions $displayOptions
   */
  public function setDisplayOptions(PropertyDisplayOptions $displayOptions)
  {
    $this->displayOptions = $displayOptions;
  }
  /**
   * @return PropertyDisplayOptions
   */
  public function getDisplayOptions()
  {
    return $this->displayOptions;
  }
  /**
   * @param DoublePropertyOptions $doublePropertyOptions
   */
  public function setDoublePropertyOptions(DoublePropertyOptions $doublePropertyOptions)
  {
    $this->doublePropertyOptions = $doublePropertyOptions;
  }
  /**
   * @return DoublePropertyOptions
   */
  public function getDoublePropertyOptions()
  {
    return $this->doublePropertyOptions;
  }
  /**
   * @param EnumPropertyOptions $enumPropertyOptions
   */
  public function setEnumPropertyOptions(EnumPropertyOptions $enumPropertyOptions)
  {
    $this->enumPropertyOptions = $enumPropertyOptions;
  }
  /**
   * @return EnumPropertyOptions
   */
  public function getEnumPropertyOptions()
  {
    return $this->enumPropertyOptions;
  }
  /**
   * @param HtmlPropertyOptions $htmlPropertyOptions
   */
  public function setHtmlPropertyOptions(HtmlPropertyOptions $htmlPropertyOptions)
  {
    $this->htmlPropertyOptions = $htmlPropertyOptions;
  }
  /**
   * @return HtmlPropertyOptions
   */
  public function getHtmlPropertyOptions()
  {
    return $this->htmlPropertyOptions;
  }
  /**
   * @param IntegerPropertyOptions $integerPropertyOptions
   */
  public function setIntegerPropertyOptions(IntegerPropertyOptions $integerPropertyOptions)
  {
    $this->integerPropertyOptions = $integerPropertyOptions;
  }
  /**
   * @return IntegerPropertyOptions
   */
  public function getIntegerPropertyOptions()
  {
    return $this->integerPropertyOptions;
  }
  /**
   * Indicates that the property can be used for generating facets. Cannot be
   * true for properties whose type is object. IsReturnable must be true to set
   * this option. Only supported for boolean, enum, integer, and text
   * properties.
   *
   * @param bool $isFacetable
   */
  public function setIsFacetable($isFacetable)
  {
    $this->isFacetable = $isFacetable;
  }
  /**
   * @return bool
   */
  public function getIsFacetable()
  {
    return $this->isFacetable;
  }
  /**
   * Indicates that multiple values are allowed for the property. For example, a
   * document only has one description but can have multiple comments. Cannot be
   * true for properties whose type is a boolean. If set to false, properties
   * that contain more than one value cause the indexing request for that item
   * to be rejected.
   *
   * @param bool $isRepeatable
   */
  public function setIsRepeatable($isRepeatable)
  {
    $this->isRepeatable = $isRepeatable;
  }
  /**
   * @return bool
   */
  public function getIsRepeatable()
  {
    return $this->isRepeatable;
  }
  /**
   * Indicates that the property identifies data that should be returned in
   * search results via the Query API. If set to *true*, indicates that Query
   * API users can use matching property fields in results. However, storing
   * fields requires more space allocation and uses more bandwidth for search
   * queries, which impacts performance over large datasets. Set to *true* here
   * only if the field is needed for search results. Cannot be true for
   * properties whose type is an object.
   *
   * @param bool $isReturnable
   */
  public function setIsReturnable($isReturnable)
  {
    $this->isReturnable = $isReturnable;
  }
  /**
   * @return bool
   */
  public function getIsReturnable()
  {
    return $this->isReturnable;
  }
  /**
   * Indicates that the property can be used for sorting. Cannot be true for
   * properties that are repeatable. Cannot be true for properties whose type is
   * object. IsReturnable must be true to set this option. Only supported for
   * boolean, date, double, integer, and timestamp properties.
   *
   * @param bool $isSortable
   */
  public function setIsSortable($isSortable)
  {
    $this->isSortable = $isSortable;
  }
  /**
   * @return bool
   */
  public function getIsSortable()
  {
    return $this->isSortable;
  }
  /**
   * Indicates that the property can be used for generating query suggestions.
   *
   * @param bool $isSuggestable
   */
  public function setIsSuggestable($isSuggestable)
  {
    $this->isSuggestable = $isSuggestable;
  }
  /**
   * @return bool
   */
  public function getIsSuggestable()
  {
    return $this->isSuggestable;
  }
  /**
   * Indicates that users can perform wildcard search for this property. Only
   * supported for Text properties. IsReturnable must be true to set this
   * option. In a given datasource maximum of 5 properties can be marked as
   * is_wildcard_searchable. For more details, see [Define object
   * properties](https://developers.google.com/cloud-search/docs/guides/schema-
   * guide#properties)
   *
   * @param bool $isWildcardSearchable
   */
  public function setIsWildcardSearchable($isWildcardSearchable)
  {
    $this->isWildcardSearchable = $isWildcardSearchable;
  }
  /**
   * @return bool
   */
  public function getIsWildcardSearchable()
  {
    return $this->isWildcardSearchable;
  }
  /**
   * The name of the property. Item indexing requests sent to the Indexing API
   * should set the property name equal to this value. For example, if name is
   * *subject_line*, then indexing requests for document items with subject
   * fields should set the name for that field equal to *subject_line*. Use the
   * name as the identifier for the object property. Once registered as a
   * property for an object, you cannot re-use this name for another property
   * within that object. The name must start with a letter and can only contain
   * letters (A-Z, a-z) or numbers (0-9). The maximum length is 256 characters.
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
   * @param ObjectPropertyOptions $objectPropertyOptions
   */
  public function setObjectPropertyOptions(ObjectPropertyOptions $objectPropertyOptions)
  {
    $this->objectPropertyOptions = $objectPropertyOptions;
  }
  /**
   * @return ObjectPropertyOptions
   */
  public function getObjectPropertyOptions()
  {
    return $this->objectPropertyOptions;
  }
  /**
   * @param TextPropertyOptions $textPropertyOptions
   */
  public function setTextPropertyOptions(TextPropertyOptions $textPropertyOptions)
  {
    $this->textPropertyOptions = $textPropertyOptions;
  }
  /**
   * @return TextPropertyOptions
   */
  public function getTextPropertyOptions()
  {
    return $this->textPropertyOptions;
  }
  /**
   * @param TimestampPropertyOptions $timestampPropertyOptions
   */
  public function setTimestampPropertyOptions(TimestampPropertyOptions $timestampPropertyOptions)
  {
    $this->timestampPropertyOptions = $timestampPropertyOptions;
  }
  /**
   * @return TimestampPropertyOptions
   */
  public function getTimestampPropertyOptions()
  {
    return $this->timestampPropertyOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PropertyDefinition::class, 'Google_Service_CloudSearch_PropertyDefinition');
