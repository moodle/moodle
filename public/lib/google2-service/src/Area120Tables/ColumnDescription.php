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

namespace Google\Service\Area120Tables;

class ColumnDescription extends \Google\Collection
{
  protected $collection_key = 'labels';
  /**
   * Data type of the column Supported types are auto_id, boolean, boolean_list,
   * creator, create_timestamp, date, dropdown, location, integer, integer_list,
   * number, number_list, person, person_list, tags, check_list, text,
   * text_list, update_timestamp, updater, relationship, file_attachment_list.
   * These types directly map to the column types supported on Tables website.
   *
   * @var string
   */
  public $dataType;
  protected $dateDetailsType = DateDetails::class;
  protected $dateDetailsDataType = '';
  /**
   * Internal id for a column.
   *
   * @var string
   */
  public $id;
  protected $labelsType = LabeledItem::class;
  protected $labelsDataType = 'array';
  protected $lookupDetailsType = LookupDetails::class;
  protected $lookupDetailsDataType = '';
  /**
   * Optional. Indicates whether or not multiple values are allowed for array
   * types where such a restriction is possible.
   *
   * @var bool
   */
  public $multipleValuesDisallowed;
  /**
   * column name
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Indicates that values for the column cannot be set by the user.
   *
   * @var bool
   */
  public $readonly;
  protected $relationshipDetailsType = RelationshipDetails::class;
  protected $relationshipDetailsDataType = '';

  /**
   * Data type of the column Supported types are auto_id, boolean, boolean_list,
   * creator, create_timestamp, date, dropdown, location, integer, integer_list,
   * number, number_list, person, person_list, tags, check_list, text,
   * text_list, update_timestamp, updater, relationship, file_attachment_list.
   * These types directly map to the column types supported on Tables website.
   *
   * @param string $dataType
   */
  public function setDataType($dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return string
   */
  public function getDataType()
  {
    return $this->dataType;
  }
  /**
   * Optional. Additional details about a date column.
   *
   * @param DateDetails $dateDetails
   */
  public function setDateDetails(DateDetails $dateDetails)
  {
    $this->dateDetails = $dateDetails;
  }
  /**
   * @return DateDetails
   */
  public function getDateDetails()
  {
    return $this->dateDetails;
  }
  /**
   * Internal id for a column.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Optional. Range of labeled values for the column. Some columns like tags
   * and drop-downs limit the values to a set of possible values. We return the
   * range of values in such cases to help clients implement better user data
   * validation.
   *
   * @param LabeledItem[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return LabeledItem[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. Indicates that this is a lookup column whose value is derived
   * from the relationship column specified in the details. Lookup columns can
   * not be updated directly. To change the value you must update the associated
   * relationship column.
   *
   * @param LookupDetails $lookupDetails
   */
  public function setLookupDetails(LookupDetails $lookupDetails)
  {
    $this->lookupDetails = $lookupDetails;
  }
  /**
   * @return LookupDetails
   */
  public function getLookupDetails()
  {
    return $this->lookupDetails;
  }
  /**
   * Optional. Indicates whether or not multiple values are allowed for array
   * types where such a restriction is possible.
   *
   * @param bool $multipleValuesDisallowed
   */
  public function setMultipleValuesDisallowed($multipleValuesDisallowed)
  {
    $this->multipleValuesDisallowed = $multipleValuesDisallowed;
  }
  /**
   * @return bool
   */
  public function getMultipleValuesDisallowed()
  {
    return $this->multipleValuesDisallowed;
  }
  /**
   * column name
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
   * Optional. Indicates that values for the column cannot be set by the user.
   *
   * @param bool $readonly
   */
  public function setReadonly($readonly)
  {
    $this->readonly = $readonly;
  }
  /**
   * @return bool
   */
  public function getReadonly()
  {
    return $this->readonly;
  }
  /**
   * Optional. Additional details about a relationship column. Specified when
   * data_type is relationship.
   *
   * @param RelationshipDetails $relationshipDetails
   */
  public function setRelationshipDetails(RelationshipDetails $relationshipDetails)
  {
    $this->relationshipDetails = $relationshipDetails;
  }
  /**
   * @return RelationshipDetails
   */
  public function getRelationshipDetails()
  {
    return $this->relationshipDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ColumnDescription::class, 'Google_Service_Area120Tables_ColumnDescription');
