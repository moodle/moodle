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

class GoogleCloudContentwarehouseV1DocumentLink extends \Google\Model
{
  /**
   * Unknown state of documentlink.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The documentlink has both source and target documents detected.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Target document is deleted, and mark the documentlink as soft-deleted.
   */
  public const STATE_SOFT_DELETED = 'SOFT_DELETED';
  /**
   * Output only. The time when the documentLink is created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Description of this document-link.
   *
   * @var string
   */
  public $description;
  /**
   * Name of this document-link. It is required that the parent derived form the
   * name to be consistent with the source document reference. Otherwise an
   * exception will be thrown. Format: projects/{project_number}/locations/{loca
   * tion}/documents/{source_document_id}/documentLinks/{document_link_id}.
   *
   * @var string
   */
  public $name;
  protected $sourceDocumentReferenceType = GoogleCloudContentwarehouseV1DocumentReference::class;
  protected $sourceDocumentReferenceDataType = '';
  /**
   * The state of the documentlink. If target node has been deleted, the link is
   * marked as invalid. Removing a source node will result in removal of all
   * associated links.
   *
   * @var string
   */
  public $state;
  protected $targetDocumentReferenceType = GoogleCloudContentwarehouseV1DocumentReference::class;
  protected $targetDocumentReferenceDataType = '';
  /**
   * Output only. The time when the documentLink is last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time when the documentLink is created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Description of this document-link.
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
   * Name of this document-link. It is required that the parent derived form the
   * name to be consistent with the source document reference. Otherwise an
   * exception will be thrown. Format: projects/{project_number}/locations/{loca
   * tion}/documents/{source_document_id}/documentLinks/{document_link_id}.
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
   * Document references of the source document.
   *
   * @param GoogleCloudContentwarehouseV1DocumentReference $sourceDocumentReference
   */
  public function setSourceDocumentReference(GoogleCloudContentwarehouseV1DocumentReference $sourceDocumentReference)
  {
    $this->sourceDocumentReference = $sourceDocumentReference;
  }
  /**
   * @return GoogleCloudContentwarehouseV1DocumentReference
   */
  public function getSourceDocumentReference()
  {
    return $this->sourceDocumentReference;
  }
  /**
   * The state of the documentlink. If target node has been deleted, the link is
   * marked as invalid. Removing a source node will result in removal of all
   * associated links.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, SOFT_DELETED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Document references of the target document.
   *
   * @param GoogleCloudContentwarehouseV1DocumentReference $targetDocumentReference
   */
  public function setTargetDocumentReference(GoogleCloudContentwarehouseV1DocumentReference $targetDocumentReference)
  {
    $this->targetDocumentReference = $targetDocumentReference;
  }
  /**
   * @return GoogleCloudContentwarehouseV1DocumentReference
   */
  public function getTargetDocumentReference()
  {
    return $this->targetDocumentReference;
  }
  /**
   * Output only. The time when the documentLink is last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1DocumentLink::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1DocumentLink');
