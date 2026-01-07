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

class GoogleCloudDocumentaiUiv1beta3BatchUpdateDocumentsMetadata extends \Google\Collection
{
  protected $collection_key = 'individualBatchUpdateStatuses';
  protected $commonMetadataType = GoogleCloudDocumentaiUiv1beta3CommonOperationMetadata::class;
  protected $commonMetadataDataType = '';
  protected $individualBatchUpdateStatusesType = GoogleCloudDocumentaiUiv1beta3BatchUpdateDocumentsMetadataIndividualBatchUpdateStatus::class;
  protected $individualBatchUpdateStatusesDataType = 'array';

  /**
   * The basic metadata of the long-running operation.
   *
   * @param GoogleCloudDocumentaiUiv1beta3CommonOperationMetadata $commonMetadata
   */
  public function setCommonMetadata(GoogleCloudDocumentaiUiv1beta3CommonOperationMetadata $commonMetadata)
  {
    $this->commonMetadata = $commonMetadata;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3CommonOperationMetadata
   */
  public function getCommonMetadata()
  {
    return $this->commonMetadata;
  }
  /**
   * The list of response details of each document.
   *
   * @param GoogleCloudDocumentaiUiv1beta3BatchUpdateDocumentsMetadataIndividualBatchUpdateStatus[] $individualBatchUpdateStatuses
   */
  public function setIndividualBatchUpdateStatuses($individualBatchUpdateStatuses)
  {
    $this->individualBatchUpdateStatuses = $individualBatchUpdateStatuses;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3BatchUpdateDocumentsMetadataIndividualBatchUpdateStatus[]
   */
  public function getIndividualBatchUpdateStatuses()
  {
    return $this->individualBatchUpdateStatuses;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiUiv1beta3BatchUpdateDocumentsMetadata::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3BatchUpdateDocumentsMetadata');
