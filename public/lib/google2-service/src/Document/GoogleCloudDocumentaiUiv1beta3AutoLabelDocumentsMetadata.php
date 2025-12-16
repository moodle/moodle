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

class GoogleCloudDocumentaiUiv1beta3AutoLabelDocumentsMetadata extends \Google\Collection
{
  protected $collection_key = 'individualAutoLabelStatuses';
  protected $commonMetadataType = GoogleCloudDocumentaiUiv1beta3CommonOperationMetadata::class;
  protected $commonMetadataDataType = '';
  protected $individualAutoLabelStatusesType = GoogleCloudDocumentaiUiv1beta3AutoLabelDocumentsMetadataIndividualAutoLabelStatus::class;
  protected $individualAutoLabelStatusesDataType = 'array';
  /**
   * Total number of the auto-labeling documents.
   *
   * @var int
   */
  public $totalDocumentCount;

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
   * The list of individual auto-labeling statuses of the dataset documents.
   *
   * @param GoogleCloudDocumentaiUiv1beta3AutoLabelDocumentsMetadataIndividualAutoLabelStatus[] $individualAutoLabelStatuses
   */
  public function setIndividualAutoLabelStatuses($individualAutoLabelStatuses)
  {
    $this->individualAutoLabelStatuses = $individualAutoLabelStatuses;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3AutoLabelDocumentsMetadataIndividualAutoLabelStatus[]
   */
  public function getIndividualAutoLabelStatuses()
  {
    return $this->individualAutoLabelStatuses;
  }
  /**
   * Total number of the auto-labeling documents.
   *
   * @param int $totalDocumentCount
   */
  public function setTotalDocumentCount($totalDocumentCount)
  {
    $this->totalDocumentCount = $totalDocumentCount;
  }
  /**
   * @return int
   */
  public function getTotalDocumentCount()
  {
    return $this->totalDocumentCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiUiv1beta3AutoLabelDocumentsMetadata::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3AutoLabelDocumentsMetadata');
