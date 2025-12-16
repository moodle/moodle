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

class GoogleCloudAiplatformV1SchemaTrainingjobDefinitionSeq2SeqPlusForecastingMetadata extends \Google\Model
{
  /**
   * BigQuery destination uri for exported evaluated examples.
   *
   * @var string
   */
  public $evaluatedDataItemsBigqueryUri;
  /**
   * Output only. The actual training cost of the model, expressed in milli node
   * hours, i.e. 1,000 value in this field means 1 node hour. Guaranteed to not
   * exceed the train budget.
   *
   * @var string
   */
  public $trainCostMilliNodeHours;

  /**
   * BigQuery destination uri for exported evaluated examples.
   *
   * @param string $evaluatedDataItemsBigqueryUri
   */
  public function setEvaluatedDataItemsBigqueryUri($evaluatedDataItemsBigqueryUri)
  {
    $this->evaluatedDataItemsBigqueryUri = $evaluatedDataItemsBigqueryUri;
  }
  /**
   * @return string
   */
  public function getEvaluatedDataItemsBigqueryUri()
  {
    return $this->evaluatedDataItemsBigqueryUri;
  }
  /**
   * Output only. The actual training cost of the model, expressed in milli node
   * hours, i.e. 1,000 value in this field means 1 node hour. Guaranteed to not
   * exceed the train budget.
   *
   * @param string $trainCostMilliNodeHours
   */
  public function setTrainCostMilliNodeHours($trainCostMilliNodeHours)
  {
    $this->trainCostMilliNodeHours = $trainCostMilliNodeHours;
  }
  /**
   * @return string
   */
  public function getTrainCostMilliNodeHours()
  {
    return $this->trainCostMilliNodeHours;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionSeq2SeqPlusForecastingMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTrainingjobDefinitionSeq2SeqPlusForecastingMetadata');
