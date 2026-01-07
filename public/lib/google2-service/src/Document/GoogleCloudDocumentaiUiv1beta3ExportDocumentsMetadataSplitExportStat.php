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

class GoogleCloudDocumentaiUiv1beta3ExportDocumentsMetadataSplitExportStat extends \Google\Model
{
  /**
   * Default value if the enum is not set.
   */
  public const SPLIT_TYPE_DATASET_SPLIT_TYPE_UNSPECIFIED = 'DATASET_SPLIT_TYPE_UNSPECIFIED';
  /**
   * Identifies the train documents.
   */
  public const SPLIT_TYPE_DATASET_SPLIT_TRAIN = 'DATASET_SPLIT_TRAIN';
  /**
   * Identifies the test documents.
   */
  public const SPLIT_TYPE_DATASET_SPLIT_TEST = 'DATASET_SPLIT_TEST';
  /**
   * Identifies the unassigned documents.
   */
  public const SPLIT_TYPE_DATASET_SPLIT_UNASSIGNED = 'DATASET_SPLIT_UNASSIGNED';
  /**
   * The dataset split type.
   *
   * @var string
   */
  public $splitType;
  /**
   * Total number of documents with the given dataset split type to be exported.
   *
   * @var int
   */
  public $totalDocumentCount;

  /**
   * The dataset split type.
   *
   * Accepted values: DATASET_SPLIT_TYPE_UNSPECIFIED, DATASET_SPLIT_TRAIN,
   * DATASET_SPLIT_TEST, DATASET_SPLIT_UNASSIGNED
   *
   * @param self::SPLIT_TYPE_* $splitType
   */
  public function setSplitType($splitType)
  {
    $this->splitType = $splitType;
  }
  /**
   * @return self::SPLIT_TYPE_*
   */
  public function getSplitType()
  {
    return $this->splitType;
  }
  /**
   * Total number of documents with the given dataset split type to be exported.
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
class_alias(GoogleCloudDocumentaiUiv1beta3ExportDocumentsMetadataSplitExportStat::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3ExportDocumentsMetadataSplitExportStat');
