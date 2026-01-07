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

class GoogleCloudDocumentaiUiv1beta3ResyncDatasetMetadataIndividualDocumentResyncStatus extends \Google\Model
{
  /**
   * Default value.
   */
  public const DOCUMENT_INCONSISTENCY_TYPE_DOCUMENT_INCONSISTENCY_TYPE_UNSPECIFIED = 'DOCUMENT_INCONSISTENCY_TYPE_UNSPECIFIED';
  /**
   * The document proto is invalid.
   */
  public const DOCUMENT_INCONSISTENCY_TYPE_DOCUMENT_INCONSISTENCY_TYPE_INVALID_DOCPROTO = 'DOCUMENT_INCONSISTENCY_TYPE_INVALID_DOCPROTO';
  /**
   * Indexed docproto metadata is mismatched.
   */
  public const DOCUMENT_INCONSISTENCY_TYPE_DOCUMENT_INCONSISTENCY_TYPE_MISMATCHED_METADATA = 'DOCUMENT_INCONSISTENCY_TYPE_MISMATCHED_METADATA';
  /**
   * The page image or thumbnails are missing.
   */
  public const DOCUMENT_INCONSISTENCY_TYPE_DOCUMENT_INCONSISTENCY_TYPE_NO_PAGE_IMAGE = 'DOCUMENT_INCONSISTENCY_TYPE_NO_PAGE_IMAGE';
  protected $documentIdType = GoogleCloudDocumentaiUiv1beta3DocumentId::class;
  protected $documentIdDataType = '';
  /**
   * The type of document inconsistency.
   *
   * @var string
   */
  public $documentInconsistencyType;
  protected $statusType = GoogleRpcStatus::class;
  protected $statusDataType = '';

  /**
   * The document identifier.
   *
   * @param GoogleCloudDocumentaiUiv1beta3DocumentId $documentId
   */
  public function setDocumentId(GoogleCloudDocumentaiUiv1beta3DocumentId $documentId)
  {
    $this->documentId = $documentId;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3DocumentId
   */
  public function getDocumentId()
  {
    return $this->documentId;
  }
  /**
   * The type of document inconsistency.
   *
   * Accepted values: DOCUMENT_INCONSISTENCY_TYPE_UNSPECIFIED,
   * DOCUMENT_INCONSISTENCY_TYPE_INVALID_DOCPROTO,
   * DOCUMENT_INCONSISTENCY_TYPE_MISMATCHED_METADATA,
   * DOCUMENT_INCONSISTENCY_TYPE_NO_PAGE_IMAGE
   *
   * @param self::DOCUMENT_INCONSISTENCY_TYPE_* $documentInconsistencyType
   */
  public function setDocumentInconsistencyType($documentInconsistencyType)
  {
    $this->documentInconsistencyType = $documentInconsistencyType;
  }
  /**
   * @return self::DOCUMENT_INCONSISTENCY_TYPE_*
   */
  public function getDocumentInconsistencyType()
  {
    return $this->documentInconsistencyType;
  }
  /**
   * The status of resyncing the document with regards to the detected
   * inconsistency. Empty if ResyncDatasetRequest.validate_only is `true`.
   *
   * @param GoogleRpcStatus $status
   */
  public function setStatus(GoogleRpcStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiUiv1beta3ResyncDatasetMetadataIndividualDocumentResyncStatus::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3ResyncDatasetMetadataIndividualDocumentResyncStatus');
