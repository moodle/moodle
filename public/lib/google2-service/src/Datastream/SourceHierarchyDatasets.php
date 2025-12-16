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

namespace Google\Service\Datastream;

class SourceHierarchyDatasets extends \Google\Model
{
  protected $datasetTemplateType = DatasetTemplate::class;
  protected $datasetTemplateDataType = '';
  /**
   * Optional. The project id of the BigQuery dataset. If not specified, the
   * project will be inferred from the stream resource.
   *
   * @var string
   */
  public $projectId;

  /**
   * The dataset template to use for dynamic dataset creation.
   *
   * @param DatasetTemplate $datasetTemplate
   */
  public function setDatasetTemplate(DatasetTemplate $datasetTemplate)
  {
    $this->datasetTemplate = $datasetTemplate;
  }
  /**
   * @return DatasetTemplate
   */
  public function getDatasetTemplate()
  {
    return $this->datasetTemplate;
  }
  /**
   * Optional. The project id of the BigQuery dataset. If not specified, the
   * project will be inferred from the stream resource.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceHierarchyDatasets::class, 'Google_Service_Datastream_SourceHierarchyDatasets');
