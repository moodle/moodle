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

class GoogleCloudAiplatformV1PredictSchemata extends \Google\Model
{
  /**
   * Immutable. Points to a YAML file stored on Google Cloud Storage describing
   * the format of a single instance, which are used in
   * PredictRequest.instances, ExplainRequest.instances and
   * BatchPredictionJob.input_config. The schema is defined as an OpenAPI 3.0.2
   * [Schema Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). AutoML Models
   * always have this field populated by Vertex AI. Note: The URI given on
   * output will be immutable and probably different, including the URI scheme,
   * than the one given on input. The output URI will point to a location where
   * the user only has a read access.
   *
   * @var string
   */
  public $instanceSchemaUri;
  /**
   * Immutable. Points to a YAML file stored on Google Cloud Storage describing
   * the parameters of prediction and explanation via PredictRequest.parameters,
   * ExplainRequest.parameters and BatchPredictionJob.model_parameters. The
   * schema is defined as an OpenAPI 3.0.2 [Schema
   * Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). AutoML Models
   * always have this field populated by Vertex AI, if no parameters are
   * supported, then it is set to an empty string. Note: The URI given on output
   * will be immutable and probably different, including the URI scheme, than
   * the one given on input. The output URI will point to a location where the
   * user only has a read access.
   *
   * @var string
   */
  public $parametersSchemaUri;
  /**
   * Immutable. Points to a YAML file stored on Google Cloud Storage describing
   * the format of a single prediction produced by this Model, which are
   * returned via PredictResponse.predictions, ExplainResponse.explanations, and
   * BatchPredictionJob.output_config. The schema is defined as an OpenAPI 3.0.2
   * [Schema Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). AutoML Models
   * always have this field populated by Vertex AI. Note: The URI given on
   * output will be immutable and probably different, including the URI scheme,
   * than the one given on input. The output URI will point to a location where
   * the user only has a read access.
   *
   * @var string
   */
  public $predictionSchemaUri;

  /**
   * Immutable. Points to a YAML file stored on Google Cloud Storage describing
   * the format of a single instance, which are used in
   * PredictRequest.instances, ExplainRequest.instances and
   * BatchPredictionJob.input_config. The schema is defined as an OpenAPI 3.0.2
   * [Schema Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). AutoML Models
   * always have this field populated by Vertex AI. Note: The URI given on
   * output will be immutable and probably different, including the URI scheme,
   * than the one given on input. The output URI will point to a location where
   * the user only has a read access.
   *
   * @param string $instanceSchemaUri
   */
  public function setInstanceSchemaUri($instanceSchemaUri)
  {
    $this->instanceSchemaUri = $instanceSchemaUri;
  }
  /**
   * @return string
   */
  public function getInstanceSchemaUri()
  {
    return $this->instanceSchemaUri;
  }
  /**
   * Immutable. Points to a YAML file stored on Google Cloud Storage describing
   * the parameters of prediction and explanation via PredictRequest.parameters,
   * ExplainRequest.parameters and BatchPredictionJob.model_parameters. The
   * schema is defined as an OpenAPI 3.0.2 [Schema
   * Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). AutoML Models
   * always have this field populated by Vertex AI, if no parameters are
   * supported, then it is set to an empty string. Note: The URI given on output
   * will be immutable and probably different, including the URI scheme, than
   * the one given on input. The output URI will point to a location where the
   * user only has a read access.
   *
   * @param string $parametersSchemaUri
   */
  public function setParametersSchemaUri($parametersSchemaUri)
  {
    $this->parametersSchemaUri = $parametersSchemaUri;
  }
  /**
   * @return string
   */
  public function getParametersSchemaUri()
  {
    return $this->parametersSchemaUri;
  }
  /**
   * Immutable. Points to a YAML file stored on Google Cloud Storage describing
   * the format of a single prediction produced by this Model, which are
   * returned via PredictResponse.predictions, ExplainResponse.explanations, and
   * BatchPredictionJob.output_config. The schema is defined as an OpenAPI 3.0.2
   * [Schema Object](https://github.com/OAI/OpenAPI-
   * Specification/blob/main/versions/3.0.2.md#schemaObject). AutoML Models
   * always have this field populated by Vertex AI. Note: The URI given on
   * output will be immutable and probably different, including the URI scheme,
   * than the one given on input. The output URI will point to a location where
   * the user only has a read access.
   *
   * @param string $predictionSchemaUri
   */
  public function setPredictionSchemaUri($predictionSchemaUri)
  {
    $this->predictionSchemaUri = $predictionSchemaUri;
  }
  /**
   * @return string
   */
  public function getPredictionSchemaUri()
  {
    return $this->predictionSchemaUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PredictSchemata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PredictSchemata');
