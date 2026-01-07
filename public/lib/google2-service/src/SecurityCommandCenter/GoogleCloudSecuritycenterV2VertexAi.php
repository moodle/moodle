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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2VertexAi extends \Google\Collection
{
  protected $collection_key = 'pipelines';
  protected $datasetsType = GoogleCloudSecuritycenterV2Dataset::class;
  protected $datasetsDataType = 'array';
  protected $pipelinesType = GoogleCloudSecuritycenterV2Pipeline::class;
  protected $pipelinesDataType = 'array';

  /**
   * Datasets associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2Dataset[] $datasets
   */
  public function setDatasets($datasets)
  {
    $this->datasets = $datasets;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Dataset[]
   */
  public function getDatasets()
  {
    return $this->datasets;
  }
  /**
   * Pipelines associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2Pipeline[] $pipelines
   */
  public function setPipelines($pipelines)
  {
    $this->pipelines = $pipelines;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Pipeline[]
   */
  public function getPipelines()
  {
    return $this->pipelines;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2VertexAi::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2VertexAi');
