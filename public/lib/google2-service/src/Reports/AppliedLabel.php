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

namespace Google\Service\Reports;

class AppliedLabel extends \Google\Collection
{
  protected $collection_key = 'fieldValues';
  protected $fieldValuesType = FieldValue::class;
  protected $fieldValuesDataType = 'array';
  /**
   * Identifier of the label - Only the label id, not the full OnePlatform
   * resource name.
   *
   * @var string
   */
  public $id;
  protected $reasonType = Reason::class;
  protected $reasonDataType = '';
  /**
   * Title of the label
   *
   * @var string
   */
  public $title;

  /**
   * List of fields which are part of the label and have been set by the user.
   * If label has a field which was not set by the user, it would not be present
   * in this list.
   *
   * @param FieldValue[] $fieldValues
   */
  public function setFieldValues($fieldValues)
  {
    $this->fieldValues = $fieldValues;
  }
  /**
   * @return FieldValue[]
   */
  public function getFieldValues()
  {
    return $this->fieldValues;
  }
  /**
   * Identifier of the label - Only the label id, not the full OnePlatform
   * resource name.
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
   * The reason why the label was applied on the resource.
   *
   * @param Reason $reason
   */
  public function setReason(Reason $reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return Reason
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * Title of the label
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppliedLabel::class, 'Google_Service_Reports_AppliedLabel');
