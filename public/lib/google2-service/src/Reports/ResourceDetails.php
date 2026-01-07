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

class ResourceDetails extends \Google\Collection
{
  protected $collection_key = 'appliedLabels';
  protected $appliedLabelsType = AppliedLabel::class;
  protected $appliedLabelsDataType = 'array';
  /**
   * Identifier of the resource.
   *
   * @var string
   */
  public $id;
  /**
   * Defines relationship of the resource to the events
   *
   * @var string
   */
  public $relation;
  /**
   * Title of the resource. For instance, in case of a drive document, this
   * would be the title of the document. In case of an email, this would be the
   * subject.
   *
   * @var string
   */
  public $title;
  /**
   * Type of the resource - document, email, chat message
   *
   * @var string
   */
  public $type;

  /**
   * List of labels applied on the resource
   *
   * @param AppliedLabel[] $appliedLabels
   */
  public function setAppliedLabels($appliedLabels)
  {
    $this->appliedLabels = $appliedLabels;
  }
  /**
   * @return AppliedLabel[]
   */
  public function getAppliedLabels()
  {
    return $this->appliedLabels;
  }
  /**
   * Identifier of the resource.
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
   * Defines relationship of the resource to the events
   *
   * @param string $relation
   */
  public function setRelation($relation)
  {
    $this->relation = $relation;
  }
  /**
   * @return string
   */
  public function getRelation()
  {
    return $this->relation;
  }
  /**
   * Title of the resource. For instance, in case of a drive document, this
   * would be the title of the document. In case of an email, this would be the
   * subject.
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
   * Type of the resource - document, email, chat message
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
class_alias(ResourceDetails::class, 'Google_Service_Reports_ResourceDetails');
