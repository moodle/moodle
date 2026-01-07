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

class GoogleCloudDocumentaiUiv1beta3BatchMoveDocumentsMetadata extends \Google\Collection
{
  /**
   * Default value if the enum is not set.
   */
  public const DEST_DATASET_TYPE_DATASET_SPLIT_TYPE_UNSPECIFIED = 'DATASET_SPLIT_TYPE_UNSPECIFIED';
  /**
   * Identifies the train documents.
   */
  public const DEST_DATASET_TYPE_DATASET_SPLIT_TRAIN = 'DATASET_SPLIT_TRAIN';
  /**
   * Identifies the test documents.
   */
  public const DEST_DATASET_TYPE_DATASET_SPLIT_TEST = 'DATASET_SPLIT_TEST';
  /**
   * Identifies the unassigned documents.
   */
  public const DEST_DATASET_TYPE_DATASET_SPLIT_UNASSIGNED = 'DATASET_SPLIT_UNASSIGNED';
  /**
   * Default value if the enum is not set.
   */
  public const DEST_SPLIT_TYPE_DATASET_SPLIT_TYPE_UNSPECIFIED = 'DATASET_SPLIT_TYPE_UNSPECIFIED';
  /**
   * Identifies the train documents.
   */
  public const DEST_SPLIT_TYPE_DATASET_SPLIT_TRAIN = 'DATASET_SPLIT_TRAIN';
  /**
   * Identifies the test documents.
   */
  public const DEST_SPLIT_TYPE_DATASET_SPLIT_TEST = 'DATASET_SPLIT_TEST';
  /**
   * Identifies the unassigned documents.
   */
  public const DEST_SPLIT_TYPE_DATASET_SPLIT_UNASSIGNED = 'DATASET_SPLIT_UNASSIGNED';
  protected $collection_key = 'individualBatchMoveStatuses';
  protected $commonMetadataType = GoogleCloudDocumentaiUiv1beta3CommonOperationMetadata::class;
  protected $commonMetadataDataType = '';
  /**
   * The destination dataset split type.
   *
   * @deprecated
   * @var string
   */
  public $destDatasetType;
  /**
   * The destination dataset split type.
   *
   * @var string
   */
  public $destSplitType;
  protected $individualBatchMoveStatusesType = GoogleCloudDocumentaiUiv1beta3BatchMoveDocumentsMetadataIndividualBatchMoveStatus::class;
  protected $individualBatchMoveStatusesDataType = 'array';

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
   * The destination dataset split type.
   *
   * Accepted values: DATASET_SPLIT_TYPE_UNSPECIFIED, DATASET_SPLIT_TRAIN,
   * DATASET_SPLIT_TEST, DATASET_SPLIT_UNASSIGNED
   *
   * @deprecated
   * @param self::DEST_DATASET_TYPE_* $destDatasetType
   */
  public function setDestDatasetType($destDatasetType)
  {
    $this->destDatasetType = $destDatasetType;
  }
  /**
   * @deprecated
   * @return self::DEST_DATASET_TYPE_*
   */
  public function getDestDatasetType()
  {
    return $this->destDatasetType;
  }
  /**
   * The destination dataset split type.
   *
   * Accepted values: DATASET_SPLIT_TYPE_UNSPECIFIED, DATASET_SPLIT_TRAIN,
   * DATASET_SPLIT_TEST, DATASET_SPLIT_UNASSIGNED
   *
   * @param self::DEST_SPLIT_TYPE_* $destSplitType
   */
  public function setDestSplitType($destSplitType)
  {
    $this->destSplitType = $destSplitType;
  }
  /**
   * @return self::DEST_SPLIT_TYPE_*
   */
  public function getDestSplitType()
  {
    return $this->destSplitType;
  }
  /**
   * The list of response details of each document.
   *
   * @param GoogleCloudDocumentaiUiv1beta3BatchMoveDocumentsMetadataIndividualBatchMoveStatus[] $individualBatchMoveStatuses
   */
  public function setIndividualBatchMoveStatuses($individualBatchMoveStatuses)
  {
    $this->individualBatchMoveStatuses = $individualBatchMoveStatuses;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3BatchMoveDocumentsMetadataIndividualBatchMoveStatus[]
   */
  public function getIndividualBatchMoveStatuses()
  {
    return $this->individualBatchMoveStatuses;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiUiv1beta3BatchMoveDocumentsMetadata::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3BatchMoveDocumentsMetadata');
