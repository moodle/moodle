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

namespace Google\Service\ChromePolicy;

class GoogleChromePolicyVersionsV1PolicySchemaFieldDescription extends \Google\Collection
{
  protected $collection_key = 'requiredItems';
  /**
   * Output only. Client default if the policy is unset.
   *
   * @var array
   */
  public $defaultValue;
  /**
   * Deprecated. Use name and field_description instead. The description for the
   * field.
   *
   * @deprecated
   * @var string
   */
  public $description;
  /**
   * Output only. The name of the field for associated with this description.
   *
   * @var string
   */
  public $field;
  protected $fieldConstraintsType = GoogleChromePolicyVersionsV1FieldConstraints::class;
  protected $fieldConstraintsDataType = '';
  protected $fieldDependenciesType = GoogleChromePolicyVersionsV1PolicySchemaFieldDependencies::class;
  protected $fieldDependenciesDataType = 'array';
  /**
   * Output only. The description of the field.
   *
   * @var string
   */
  public $fieldDescription;
  /**
   * Output only. Any input constraints associated on the values for the field.
   *
   * @var string
   */
  public $inputConstraint;
  protected $knownValueDescriptionsType = GoogleChromePolicyVersionsV1PolicySchemaFieldKnownValueDescription::class;
  protected $knownValueDescriptionsDataType = 'array';
  /**
   * Output only. The name of the field.
   *
   * @var string
   */
  public $name;
  protected $nestedFieldDescriptionsType = GoogleChromePolicyVersionsV1PolicySchemaFieldDescription::class;
  protected $nestedFieldDescriptionsDataType = 'array';
  protected $requiredItemsType = GoogleChromePolicyVersionsV1PolicySchemaRequiredItems::class;
  protected $requiredItemsDataType = 'array';

  /**
   * Output only. Client default if the policy is unset.
   *
   * @param array $defaultValue
   */
  public function setDefaultValue($defaultValue)
  {
    $this->defaultValue = $defaultValue;
  }
  /**
   * @return array
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }
  /**
   * Deprecated. Use name and field_description instead. The description for the
   * field.
   *
   * @deprecated
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. The name of the field for associated with this description.
   *
   * @param string $field
   */
  public function setField($field)
  {
    $this->field = $field;
  }
  /**
   * @return string
   */
  public function getField()
  {
    return $this->field;
  }
  /**
   * Output only. Information on any input constraints associated on the values
   * for the field.
   *
   * @param GoogleChromePolicyVersionsV1FieldConstraints $fieldConstraints
   */
  public function setFieldConstraints(GoogleChromePolicyVersionsV1FieldConstraints $fieldConstraints)
  {
    $this->fieldConstraints = $fieldConstraints;
  }
  /**
   * @return GoogleChromePolicyVersionsV1FieldConstraints
   */
  public function getFieldConstraints()
  {
    return $this->fieldConstraints;
  }
  /**
   * Output only. Provides a list of fields and values. At least one of the
   * fields must have the corresponding value in order for this field to be
   * allowed to be set.
   *
   * @param GoogleChromePolicyVersionsV1PolicySchemaFieldDependencies[] $fieldDependencies
   */
  public function setFieldDependencies($fieldDependencies)
  {
    $this->fieldDependencies = $fieldDependencies;
  }
  /**
   * @return GoogleChromePolicyVersionsV1PolicySchemaFieldDependencies[]
   */
  public function getFieldDependencies()
  {
    return $this->fieldDependencies;
  }
  /**
   * Output only. The description of the field.
   *
   * @param string $fieldDescription
   */
  public function setFieldDescription($fieldDescription)
  {
    $this->fieldDescription = $fieldDescription;
  }
  /**
   * @return string
   */
  public function getFieldDescription()
  {
    return $this->fieldDescription;
  }
  /**
   * Output only. Any input constraints associated on the values for the field.
   *
   * @param string $inputConstraint
   */
  public function setInputConstraint($inputConstraint)
  {
    $this->inputConstraint = $inputConstraint;
  }
  /**
   * @return string
   */
  public function getInputConstraint()
  {
    return $this->inputConstraint;
  }
  /**
   * Output only. If the field has a set of known values, this field will
   * provide a description for these values.
   *
   * @param GoogleChromePolicyVersionsV1PolicySchemaFieldKnownValueDescription[] $knownValueDescriptions
   */
  public function setKnownValueDescriptions($knownValueDescriptions)
  {
    $this->knownValueDescriptions = $knownValueDescriptions;
  }
  /**
   * @return GoogleChromePolicyVersionsV1PolicySchemaFieldKnownValueDescription[]
   */
  public function getKnownValueDescriptions()
  {
    return $this->knownValueDescriptions;
  }
  /**
   * Output only. The name of the field.
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
   * Output only. Provides the description of the fields nested in this field,
   * if the field is a message type that defines multiple fields. Fields are
   * suggested to be displayed by the ordering in this list, not by field
   * number.
   *
   * @param GoogleChromePolicyVersionsV1PolicySchemaFieldDescription[] $nestedFieldDescriptions
   */
  public function setNestedFieldDescriptions($nestedFieldDescriptions)
  {
    $this->nestedFieldDescriptions = $nestedFieldDescriptions;
  }
  /**
   * @return GoogleChromePolicyVersionsV1PolicySchemaFieldDescription[]
   */
  public function getNestedFieldDescriptions()
  {
    return $this->nestedFieldDescriptions;
  }
  /**
   * Output only. Provides a list of fields that are required to be set if this
   * field has a certain value.
   *
   * @param GoogleChromePolicyVersionsV1PolicySchemaRequiredItems[] $requiredItems
   */
  public function setRequiredItems($requiredItems)
  {
    $this->requiredItems = $requiredItems;
  }
  /**
   * @return GoogleChromePolicyVersionsV1PolicySchemaRequiredItems[]
   */
  public function getRequiredItems()
  {
    return $this->requiredItems;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1PolicySchemaFieldDescription::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1PolicySchemaFieldDescription');
