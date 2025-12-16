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

namespace Google\Service\DriveActivity;

class AppliedLabelChangeDetail extends \Google\Collection
{
  protected $collection_key = 'types';
  protected $fieldChangesType = FieldValueChange::class;
  protected $fieldChangesDataType = 'array';
  /**
   * The Label name representing the Label that changed. This name always
   * contains the revision of the Label that was used when this Action occurred.
   * The format is `labels/id@revision`.
   *
   * @var string
   */
  public $label;
  /**
   * The human-readable title of the label that changed.
   *
   * @var string
   */
  public $title;
  /**
   * The types of changes made to the Label on the Target.
   *
   * @var string[]
   */
  public $types;

  /**
   * Field Changes. Only present if `types` contains
   * `LABEL_FIELD_VALUE_CHANGED`.
   *
   * @param FieldValueChange[] $fieldChanges
   */
  public function setFieldChanges($fieldChanges)
  {
    $this->fieldChanges = $fieldChanges;
  }
  /**
   * @return FieldValueChange[]
   */
  public function getFieldChanges()
  {
    return $this->fieldChanges;
  }
  /**
   * The Label name representing the Label that changed. This name always
   * contains the revision of the Label that was used when this Action occurred.
   * The format is `labels/id@revision`.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * The human-readable title of the label that changed.
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
   * The types of changes made to the Label on the Target.
   *
   * @param string[] $types
   */
  public function setTypes($types)
  {
    $this->types = $types;
  }
  /**
   * @return string[]
   */
  public function getTypes()
  {
    return $this->types;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppliedLabelChangeDetail::class, 'Google_Service_DriveActivity_AppliedLabelChangeDetail');
