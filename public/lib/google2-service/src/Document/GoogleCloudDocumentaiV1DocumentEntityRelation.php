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

class GoogleCloudDocumentaiV1DocumentEntityRelation extends \Google\Model
{
  /**
   * Object entity id.
   *
   * @var string
   */
  public $objectId;
  /**
   * Relationship description.
   *
   * @var string
   */
  public $relation;
  /**
   * Subject entity id.
   *
   * @var string
   */
  public $subjectId;

  /**
   * Object entity id.
   *
   * @param string $objectId
   */
  public function setObjectId($objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return string
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
  /**
   * Relationship description.
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
   * Subject entity id.
   *
   * @param string $subjectId
   */
  public function setSubjectId($subjectId)
  {
    $this->subjectId = $subjectId;
  }
  /**
   * @return string
   */
  public function getSubjectId()
  {
    return $this->subjectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentEntityRelation::class, 'Google_Service_Document_GoogleCloudDocumentaiV1DocumentEntityRelation');
