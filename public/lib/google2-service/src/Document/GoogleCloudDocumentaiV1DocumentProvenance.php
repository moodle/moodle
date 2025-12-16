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

class GoogleCloudDocumentaiV1DocumentProvenance extends \Google\Collection
{
  /**
   * Operation type unspecified. If no operation is specified a provenance entry
   * is simply used to match against a `parent`.
   */
  public const TYPE_OPERATION_TYPE_UNSPECIFIED = 'OPERATION_TYPE_UNSPECIFIED';
  /**
   * Add an element.
   */
  public const TYPE_ADD = 'ADD';
  /**
   * Remove an element identified by `parent`.
   */
  public const TYPE_REMOVE = 'REMOVE';
  /**
   * Updates any fields within the given provenance scope of the message. It
   * overwrites the fields rather than replacing them. Use this when you want to
   * update a field value of an entity without also updating all the child
   * properties.
   */
  public const TYPE_UPDATE = 'UPDATE';
  /**
   * Currently unused. Replace an element identified by `parent`.
   */
  public const TYPE_REPLACE = 'REPLACE';
  /**
   * Deprecated. Request human review for the element identified by `parent`.
   *
   * @deprecated
   */
  public const TYPE_EVAL_REQUESTED = 'EVAL_REQUESTED';
  /**
   * Deprecated. Element is reviewed and approved at human review, confidence
   * will be set to 1.0.
   *
   * @deprecated
   */
  public const TYPE_EVAL_APPROVED = 'EVAL_APPROVED';
  /**
   * Deprecated. Element is skipped in the validation process.
   *
   * @deprecated
   */
  public const TYPE_EVAL_SKIPPED = 'EVAL_SKIPPED';
  protected $collection_key = 'parents';
  /**
   * The Id of this operation. Needs to be unique within the scope of the
   * revision.
   *
   * @deprecated
   * @var int
   */
  public $id;
  protected $parentsType = GoogleCloudDocumentaiV1DocumentProvenanceParent::class;
  protected $parentsDataType = 'array';
  /**
   * The index of the revision that produced this element.
   *
   * @deprecated
   * @var int
   */
  public $revision;
  /**
   * The type of provenance operation.
   *
   * @var string
   */
  public $type;

  /**
   * The Id of this operation. Needs to be unique within the scope of the
   * revision.
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
   * References to the original elements that are replaced.
   *
   * @param GoogleCloudDocumentaiV1DocumentProvenanceParent[] $parents
   */
  public function setParents($parents)
  {
    $this->parents = $parents;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentProvenanceParent[]
   */
  public function getParents()
  {
    return $this->parents;
  }
  /**
   * The index of the revision that produced this element.
   *
   * @deprecated
   * @param int $revision
   */
  public function setRevision($revision)
  {
    $this->revision = $revision;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getRevision()
  {
    return $this->revision;
  }
  /**
   * The type of provenance operation.
   *
   * Accepted values: OPERATION_TYPE_UNSPECIFIED, ADD, REMOVE, UPDATE, REPLACE,
   * EVAL_REQUESTED, EVAL_APPROVED, EVAL_SKIPPED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentProvenance::class, 'Google_Service_Document_GoogleCloudDocumentaiV1DocumentProvenance');
