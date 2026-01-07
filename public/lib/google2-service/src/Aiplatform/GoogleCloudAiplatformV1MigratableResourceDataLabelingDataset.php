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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1MigratableResourceDataLabelingDataset extends \Google\Collection
{
  protected $collection_key = 'dataLabelingAnnotatedDatasets';
  protected $dataLabelingAnnotatedDatasetsType = GoogleCloudAiplatformV1MigratableResourceDataLabelingDatasetDataLabelingAnnotatedDataset::class;
  protected $dataLabelingAnnotatedDatasetsDataType = 'array';
  /**
   * Full resource name of data labeling Dataset. Format:
   * `projects/{project}/datasets/{dataset}`.
   *
   * @var string
   */
  public $dataset;
  /**
   * The Dataset's display name in datalabeling.googleapis.com.
   *
   * @var string
   */
  public $datasetDisplayName;

  /**
   * The migratable AnnotatedDataset in datalabeling.googleapis.com belongs to
   * the data labeling Dataset.
   *
   * @param GoogleCloudAiplatformV1MigratableResourceDataLabelingDatasetDataLabelingAnnotatedDataset[] $dataLabelingAnnotatedDatasets
   */
  public function setDataLabelingAnnotatedDatasets($dataLabelingAnnotatedDatasets)
  {
    $this->dataLabelingAnnotatedDatasets = $dataLabelingAnnotatedDatasets;
  }
  /**
   * @return GoogleCloudAiplatformV1MigratableResourceDataLabelingDatasetDataLabelingAnnotatedDataset[]
   */
  public function getDataLabelingAnnotatedDatasets()
  {
    return $this->dataLabelingAnnotatedDatasets;
  }
  /**
   * Full resource name of data labeling Dataset. Format:
   * `projects/{project}/datasets/{dataset}`.
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
   * The Dataset's display name in datalabeling.googleapis.com.
   *
   * @param string $datasetDisplayName
   */
  public function setDatasetDisplayName($datasetDisplayName)
  {
    $this->datasetDisplayName = $datasetDisplayName;
  }
  /**
   * @return string
   */
  public function getDatasetDisplayName()
  {
    return $this->datasetDisplayName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MigratableResourceDataLabelingDataset::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MigratableResourceDataLabelingDataset');
