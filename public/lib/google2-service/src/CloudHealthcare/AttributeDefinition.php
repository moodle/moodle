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

namespace Google\Service\CloudHealthcare;

class AttributeDefinition extends \Google\Collection
{
  /**
   * No category specified. This option is invalid.
   */
  public const CATEGORY_CATEGORY_UNSPECIFIED = 'CATEGORY_UNSPECIFIED';
  /**
   * Specify this category when this attribute describes the properties of
   * resources. For example, data anonymity or data type.
   */
  public const CATEGORY_RESOURCE = 'RESOURCE';
  /**
   * Specify this category when this attribute describes the properties of
   * requests. For example, requester's role or requester's organization.
   */
  public const CATEGORY_REQUEST = 'REQUEST';
  protected $collection_key = 'consentDefaultValues';
  /**
   * Required. Possible values for the attribute. The number of allowed values
   * must not exceed 500. An empty list is invalid. The list can only be
   * expanded after creation.
   *
   * @var string[]
   */
  public $allowedValues;
  /**
   * Required. The category of the attribute. The value of this field cannot be
   * changed after creation.
   *
   * @var string
   */
  public $category;
  /**
   * Optional. Default values of the attribute in Consents. If no default values
   * are specified, it defaults to an empty value.
   *
   * @var string[]
   */
  public $consentDefaultValues;
  /**
   * Optional. Default value of the attribute in User data mappings. If no
   * default value is specified, it defaults to an empty value. This field is
   * only applicable to attributes of the category `RESOURCE`.
   *
   * @var string
   */
  public $dataMappingDefaultValue;
  /**
   * Optional. A description of the attribute.
   *
   * @var string
   */
  public $description;
  /**
   * Identifier. Resource name of the Attribute definition, of the form `project
   * s/{project_id}/locations/{location_id}/datasets/{dataset_id}/consentStores/
   * {consent_store_id}/attributeDefinitions/{attribute_definition_id}`. Cannot
   * be changed after creation.
   *
   * @var string
   */
  public $name;

  /**
   * Required. Possible values for the attribute. The number of allowed values
   * must not exceed 500. An empty list is invalid. The list can only be
   * expanded after creation.
   *
   * @param string[] $allowedValues
   */
  public function setAllowedValues($allowedValues)
  {
    $this->allowedValues = $allowedValues;
  }
  /**
   * @return string[]
   */
  public function getAllowedValues()
  {
    return $this->allowedValues;
  }
  /**
   * Required. The category of the attribute. The value of this field cannot be
   * changed after creation.
   *
   * Accepted values: CATEGORY_UNSPECIFIED, RESOURCE, REQUEST
   *
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Optional. Default values of the attribute in Consents. If no default values
   * are specified, it defaults to an empty value.
   *
   * @param string[] $consentDefaultValues
   */
  public function setConsentDefaultValues($consentDefaultValues)
  {
    $this->consentDefaultValues = $consentDefaultValues;
  }
  /**
   * @return string[]
   */
  public function getConsentDefaultValues()
  {
    return $this->consentDefaultValues;
  }
  /**
   * Optional. Default value of the attribute in User data mappings. If no
   * default value is specified, it defaults to an empty value. This field is
   * only applicable to attributes of the category `RESOURCE`.
   *
   * @param string $dataMappingDefaultValue
   */
  public function setDataMappingDefaultValue($dataMappingDefaultValue)
  {
    $this->dataMappingDefaultValue = $dataMappingDefaultValue;
  }
  /**
   * @return string
   */
  public function getDataMappingDefaultValue()
  {
    return $this->dataMappingDefaultValue;
  }
  /**
   * Optional. A description of the attribute.
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
   * Identifier. Resource name of the Attribute definition, of the form `project
   * s/{project_id}/locations/{location_id}/datasets/{dataset_id}/consentStores/
   * {consent_store_id}/attributeDefinitions/{attribute_definition_id}`. Cannot
   * be changed after creation.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttributeDefinition::class, 'Google_Service_CloudHealthcare_AttributeDefinition');
