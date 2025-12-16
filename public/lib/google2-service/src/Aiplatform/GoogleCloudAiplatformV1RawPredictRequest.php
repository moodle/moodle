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

class GoogleCloudAiplatformV1RawPredictRequest extends \Google\Model
{
  protected $httpBodyType = GoogleApiHttpBody::class;
  protected $httpBodyDataType = '';

  /**
   * The prediction input. Supports HTTP headers and arbitrary data payload. A
   * DeployedModel may have an upper limit on the number of instances it
   * supports per request. When this limit it is exceeded for an AutoML model,
   * the RawPredict method returns an error. When this limit is exceeded for a
   * custom-trained model, the behavior varies depending on the model. You can
   * specify the schema for each instance in the
   * predict_schemata.instance_schema_uri field when you create a Model. This
   * schema applies when you deploy the `Model` as a `DeployedModel` to an
   * Endpoint and use the `RawPredict` method.
   *
   * @param GoogleApiHttpBody $httpBody
   */
  public function setHttpBody(GoogleApiHttpBody $httpBody)
  {
    $this->httpBody = $httpBody;
  }
  /**
   * @return GoogleApiHttpBody
   */
  public function getHttpBody()
  {
    return $this->httpBody;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RawPredictRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RawPredictRequest');
