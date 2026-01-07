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

class GoogleCloudDocumentaiV1beta3DocumentId extends \Google\Model
{
  protected $gcsManagedDocIdType = GoogleCloudDocumentaiV1beta3DocumentIdGCSManagedDocumentId::class;
  protected $gcsManagedDocIdDataType = '';
  protected $revisionRefType = GoogleCloudDocumentaiV1beta3RevisionRef::class;
  protected $revisionRefDataType = '';
  protected $unmanagedDocIdType = GoogleCloudDocumentaiV1beta3DocumentIdUnmanagedDocumentId::class;
  protected $unmanagedDocIdDataType = '';

  /**
   * A document id within user-managed Cloud Storage.
   *
   * @param GoogleCloudDocumentaiV1beta3DocumentIdGCSManagedDocumentId $gcsManagedDocId
   */
  public function setGcsManagedDocId(GoogleCloudDocumentaiV1beta3DocumentIdGCSManagedDocumentId $gcsManagedDocId)
  {
    $this->gcsManagedDocId = $gcsManagedDocId;
  }
  /**
   * @return GoogleCloudDocumentaiV1beta3DocumentIdGCSManagedDocumentId
   */
  public function getGcsManagedDocId()
  {
    return $this->gcsManagedDocId;
  }
  /**
   * Points to a specific revision of the document if set.
   *
   * @param GoogleCloudDocumentaiV1beta3RevisionRef $revisionRef
   */
  public function setRevisionRef(GoogleCloudDocumentaiV1beta3RevisionRef $revisionRef)
  {
    $this->revisionRef = $revisionRef;
  }
  /**
   * @return GoogleCloudDocumentaiV1beta3RevisionRef
   */
  public function getRevisionRef()
  {
    return $this->revisionRef;
  }
  /**
   * A document id within unmanaged dataset.
   *
   * @param GoogleCloudDocumentaiV1beta3DocumentIdUnmanagedDocumentId $unmanagedDocId
   */
  public function setUnmanagedDocId(GoogleCloudDocumentaiV1beta3DocumentIdUnmanagedDocumentId $unmanagedDocId)
  {
    $this->unmanagedDocId = $unmanagedDocId;
  }
  /**
   * @return GoogleCloudDocumentaiV1beta3DocumentIdUnmanagedDocumentId
   */
  public function getUnmanagedDocId()
  {
    return $this->unmanagedDocId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1beta3DocumentId::class, 'Google_Service_Document_GoogleCloudDocumentaiV1beta3DocumentId');
