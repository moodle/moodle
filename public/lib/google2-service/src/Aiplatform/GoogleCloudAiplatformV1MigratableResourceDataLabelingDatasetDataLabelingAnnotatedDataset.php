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

class GoogleCloudAiplatformV1MigratableResourceDataLabelingDatasetDataLabelingAnnotatedDataset extends \Google\Model
{
  /**
   * Full resource name of data labeling AnnotatedDataset. Format: `projects/{pr
   * oject}/datasets/{dataset}/annotatedDatasets/{annotated_dataset}`.
   *
   * @var string
   */
  public $annotatedDataset;
  /**
   * The AnnotatedDataset's display name in datalabeling.googleapis.com.
   *
   * @var string
   */
  public $annotatedDatasetDisplayName;

  /**
   * Full resource name of data labeling AnnotatedDataset. Format: `projects/{pr
   * oject}/datasets/{dataset}/annotatedDatasets/{annotated_dataset}`.
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
   * The AnnotatedDataset's display name in datalabeling.googleapis.com.
   *
   * @param string $annotatedDatasetDisplayName
   */
  public function setAnnotatedDatasetDisplayName($annotatedDatasetDisplayName)
  {
    $this->annotatedDatasetDisplayName = $annotatedDatasetDisplayName;
  }
  /**
   * @return string
   */
  public function getAnnotatedDatasetDisplayName()
  {
    return $this->annotatedDatasetDisplayName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MigratableResourceDataLabelingDatasetDataLabelingAnnotatedDataset::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MigratableResourceDataLabelingDatasetDataLabelingAnnotatedDataset');
