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

class GoogleCloudAiplatformV1EvaluationRunDataSource extends \Google\Model
{
  protected $bigqueryRequestSetType = GoogleCloudAiplatformV1BigQueryRequestSet::class;
  protected $bigqueryRequestSetDataType = '';
  /**
   * The EvaluationSet resource name. Format:
   * `projects/{project}/locations/{location}/evaluationSets/{evaluation_set}`
   *
   * @var string
   */
  public $evaluationSet;

  /**
   * Evaluation data in bigquery.
   *
   * @param GoogleCloudAiplatformV1BigQueryRequestSet $bigqueryRequestSet
   */
  public function setBigqueryRequestSet(GoogleCloudAiplatformV1BigQueryRequestSet $bigqueryRequestSet)
  {
    $this->bigqueryRequestSet = $bigqueryRequestSet;
  }
  /**
   * @return GoogleCloudAiplatformV1BigQueryRequestSet
   */
  public function getBigqueryRequestSet()
  {
    return $this->bigqueryRequestSet;
  }
  /**
   * The EvaluationSet resource name. Format:
   * `projects/{project}/locations/{location}/evaluationSets/{evaluation_set}`
   *
   * @param string $evaluationSet
   */
  public function setEvaluationSet($evaluationSet)
  {
    $this->evaluationSet = $evaluationSet;
  }
  /**
   * @return string
   */
  public function getEvaluationSet()
  {
    return $this->evaluationSet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EvaluationRunDataSource::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EvaluationRunDataSource');
