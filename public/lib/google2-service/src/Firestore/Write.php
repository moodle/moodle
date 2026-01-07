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

namespace Google\Service\Firestore;

class Write extends \Google\Collection
{
  protected $collection_key = 'updateTransforms';
  protected $currentDocumentType = Precondition::class;
  protected $currentDocumentDataType = '';
  /**
   * A document name to delete. In the format:
   * `projects/{project_id}/databases/{database_id}/documents/{document_path}`.
   *
   * @var string
   */
  public $delete;
  protected $transformType = DocumentTransform::class;
  protected $transformDataType = '';
  protected $updateType = Document::class;
  protected $updateDataType = '';
  protected $updateMaskType = DocumentMask::class;
  protected $updateMaskDataType = '';
  protected $updateTransformsType = FieldTransform::class;
  protected $updateTransformsDataType = 'array';

  /**
   * An optional precondition on the document. The write will fail if this is
   * set and not met by the target document.
   *
   * @param Precondition $currentDocument
   */
  public function setCurrentDocument(Precondition $currentDocument)
  {
    $this->currentDocument = $currentDocument;
  }
  /**
   * @return Precondition
   */
  public function getCurrentDocument()
  {
    return $this->currentDocument;
  }
  /**
   * A document name to delete. In the format:
   * `projects/{project_id}/databases/{database_id}/documents/{document_path}`.
   *
   * @param string $delete
   */
  public function setDelete($delete)
  {
    $this->delete = $delete;
  }
  /**
   * @return string
   */
  public function getDelete()
  {
    return $this->delete;
  }
  /**
   * Applies a transformation to a document.
   *
   * @param DocumentTransform $transform
   */
  public function setTransform(DocumentTransform $transform)
  {
    $this->transform = $transform;
  }
  /**
   * @return DocumentTransform
   */
  public function getTransform()
  {
    return $this->transform;
  }
  /**
   * A document to write.
   *
   * @param Document $update
   */
  public function setUpdate(Document $update)
  {
    $this->update = $update;
  }
  /**
   * @return Document
   */
  public function getUpdate()
  {
    return $this->update;
  }
  /**
   * The fields to update in this write. This field can be set only when the
   * operation is `update`. If the mask is not set for an `update` and the
   * document exists, any existing data will be overwritten. If the mask is set
   * and the document on the server has fields not covered by the mask, they are
   * left unchanged. Fields referenced in the mask, but not present in the input
   * document, are deleted from the document on the server. The field paths in
   * this mask must not contain a reserved field name.
   *
   * @param DocumentMask $updateMask
   */
  public function setUpdateMask(DocumentMask $updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return DocumentMask
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
  /**
   * The transforms to perform after update. This field can be set only when the
   * operation is `update`. If present, this write is equivalent to performing
   * `update` and `transform` to the same document atomically and in order.
   *
   * @param FieldTransform[] $updateTransforms
   */
  public function setUpdateTransforms($updateTransforms)
  {
    $this->updateTransforms = $updateTransforms;
  }
  /**
   * @return FieldTransform[]
   */
  public function getUpdateTransforms()
  {
    return $this->updateTransforms;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Write::class, 'Google_Service_Firestore_Write');
