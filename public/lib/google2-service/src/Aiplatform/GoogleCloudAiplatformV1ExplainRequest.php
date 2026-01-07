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

class GoogleCloudAiplatformV1ExplainRequest extends \Google\Collection
{
  protected $collection_key = 'instances';
  /**
   * If specified, this ExplainRequest will be served by the chosen
   * DeployedModel, overriding Endpoint.traffic_split.
   *
   * @var string
   */
  public $deployedModelId;
  protected $explanationSpecOverrideType = GoogleCloudAiplatformV1ExplanationSpecOverride::class;
  protected $explanationSpecOverrideDataType = '';
  /**
   * Required. The instances that are the input to the explanation call. A
   * DeployedModel may have an upper limit on the number of instances it
   * supports per request, and when it is exceeded the explanation call errors
   * in case of AutoML Models, or, in case of customer created Models, the
   * behaviour is as documented by that Model. The schema of any single instance
   * may be specified via Endpoint's DeployedModels' Model's PredictSchemata's
   * instance_schema_uri.
   *
   * @var array[]
   */
  public $instances;
  /**
   * The parameters that govern the prediction. The schema of the parameters may
   * be specified via Endpoint's DeployedModels' Model's PredictSchemata's
   * parameters_schema_uri.
   *
   * @var array
   */
  public $parameters;

  /**
   * If specified, this ExplainRequest will be served by the chosen
   * DeployedModel, overriding Endpoint.traffic_split.
   *
   * @param string $deployedModelId
   */
  public function setDeployedModelId($deployedModelId)
  {
    $this->deployedModelId = $deployedModelId;
  }
  /**
   * @return string
   */
  public function getDeployedModelId()
  {
    return $this->deployedModelId;
  }
  /**
   * If specified, overrides the explanation_spec of the DeployedModel. Can be
   * used for explaining prediction results with different configurations, such
   * as: - Explaining top-5 predictions results as opposed to top-1; -
   * Increasing path count or step count of the attribution methods to reduce
   * approximate errors; - Using different baselines for explaining the
   * prediction results.
   *
   * @param GoogleCloudAiplatformV1ExplanationSpecOverride $explanationSpecOverride
   */
  public function setExplanationSpecOverride(GoogleCloudAiplatformV1ExplanationSpecOverride $explanationSpecOverride)
  {
    $this->explanationSpecOverride = $explanationSpecOverride;
  }
  /**
   * @return GoogleCloudAiplatformV1ExplanationSpecOverride
   */
  public function getExplanationSpecOverride()
  {
    return $this->explanationSpecOverride;
  }
  /**
   * Required. The instances that are the input to the explanation call. A
   * DeployedModel may have an upper limit on the number of instances it
   * supports per request, and when it is exceeded the explanation call errors
   * in case of AutoML Models, or, in case of customer created Models, the
   * behaviour is as documented by that Model. The schema of any single instance
   * may be specified via Endpoint's DeployedModels' Model's PredictSchemata's
   * instance_schema_uri.
   *
   * @param array[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return array[]
   */
  public function getInstances()
  {
    return $this->instances;
  }
  /**
   * The parameters that govern the prediction. The schema of the parameters may
   * be specified via Endpoint's DeployedModels' Model's PredictSchemata's
   * parameters_schema_uri.
   *
   * @param array $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return array
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExplainRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExplainRequest');
