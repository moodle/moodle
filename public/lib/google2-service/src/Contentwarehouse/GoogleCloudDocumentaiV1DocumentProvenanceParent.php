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

namespace Google\Service\Contentwarehouse;

class GoogleCloudDocumentaiV1DocumentProvenanceParent extends \Google\Model
{
  /**
   * The id of the parent provenance.
   *
   * @deprecated
   * @var int
   */
  public $id;
  /**
   * The index of the parent item in the corresponding item list (eg. list of
   * entities, properties within entities, etc.) in the parent revision.
   *
   * @var int
   */
  public $index;
  /**
   * The index of the index into current revision's parent_ids list.
   *
   * @var int
   */
  public $revision;

  /**
   * The id of the parent provenance.
   *
   * @deprecated
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The index of the parent item in the corresponding item list (eg. list of
   * entities, properties within entities, etc.) in the parent revision.
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
   * The index of the index into current revision's parent_ids list.
   *
   * @param int $revision
   */
  public function setRevision($revision)
  {
    $this->revision = $revision;
  }
  /**
   * @return int
   */
  public function getRevision()
  {
    return $this->revision;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentProvenanceParent::class, 'Google_Service_Contentwarehouse_GoogleCloudDocumentaiV1DocumentProvenanceParent');
