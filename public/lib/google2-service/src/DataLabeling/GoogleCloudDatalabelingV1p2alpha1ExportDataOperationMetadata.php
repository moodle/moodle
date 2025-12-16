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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1p2alpha1ExportDataOperationMetadata extends \Google\Collection
{
  protected $collection_key = 'partialFailures';
  /**
   * Output only. The name of annotated dataset in format
   * "projects/datasets/annotatedDatasets".
   *
   * @var string
   */
  public $annotatedDataset;
  /**
   * Output only. Timestamp when export dataset request was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The name of dataset to be exported. "projects/datasets"
   *
   * @var string
   */
  public $dataset;
  protected $partialFailuresType = GoogleRpcStatus::class;
  protected $partialFailuresDataType = 'array';

  /**
   * Output only. The name of annotated dataset in format
   * "projects/datasets/annotatedDatasets".
   *
   * @param string $annotatedDataset
   */
  public function setAnnotatedDataset($annotatedDataset)
  {
    $this->annotatedDataset = $annotatedDataset;
  }
  /**
   * @return string
   */
  public function getAnnotatedDataset()
  {
    return $this->annotatedDataset;
  }
  /**
   * Output only. Timestamp when export dataset request was created.
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
   * Output only. The name of dataset to be exported. "projects/datasets"
   *
   * @param string $dataset
   */
  public function setDataset($dataset)
  {
    $this->dataset = $dataset;
  }
  /**
   * @return string
   */
  public function getDataset()
  {
    return $this->dataset;
  }
  /**
   * Output only. Partial failures encountered. E.g. single files that couldn't
   * be read. Status details field will contain standard GCP error details.
   *
   * @param GoogleRpcStatus[] $partialFailures
   */
  public function setPartialFailures($partialFailures)
  {
    $this->partialFailures = $partialFailures;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getPartialFailures()
  {
    return $this->partialFailures;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1p2alpha1ExportDataOperationMetadata::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1p2alpha1ExportDataOperationMetadata');
