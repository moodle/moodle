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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1RouteMap extends \Google\Model
{
  /**
   * HTTP path on the container to send health checkss to. AI Platform
   * Prediction intermittently sends GET requests to this path on the
   * container's IP address and port to check that the container is healthy.
   * Read more about [health checks](/ai-platform/prediction/docs/custom-
   * container-requirements#checks). For example, if you set this field to
   * `/bar`, then AI Platform Prediction intermittently sends a GET request to
   * the `/bar` path on the port of your container specified by the first value
   * of Version.container.ports. If you don't specify this field, it defaults to
   * the following value: /v1/models/ MODEL/versions/VERSION The placeholders in
   * this value are replaced as follows: * MODEL: The name of the parent Model.
   * This does not include the "projects/PROJECT_ID/models/" prefix that the API
   * returns in output; it is the bare model name, as provided to
   * projects.models.create. * VERSION: The name of the model version. This does
   * not include the "projects/PROJECT_ID /models/MODEL/versions/" prefix that
   * the API returns in output; it is the bare version name, as provided to
   * projects.models.versions.create.
   *
   * @var string
   */
  public $health;
  /**
   * HTTP path on the container to send prediction requests to. AI Platform
   * Prediction forwards requests sent using projects.predict to this path on
   * the container's IP address and port. AI Platform Prediction then returns
   * the container's response in the API response. For example, if you set this
   * field to `/foo`, then when AI Platform Prediction receives a prediction
   * request, it forwards the request body in a POST request to the `/foo` path
   * on the port of your container specified by the first value of
   * Version.container.ports. If you don't specify this field, it defaults to
   * the following value: /v1/models/MODEL/versions/VERSION:predict The
   * placeholders in this value are replaced as follows: * MODEL: The name of
   * the parent Model. This does not include the "projects/PROJECT_ID/models/"
   * prefix that the API returns in output; it is the bare model name, as
   * provided to projects.models.create. * VERSION: The name of the model
   * version. This does not include the
   * "projects/PROJECT_ID/models/MODEL/versions/" prefix that the API returns in
   * output; it is the bare version name, as provided to
   * projects.models.versions.create.
   *
   * @var string
   */
  public $predict;

  /**
   * HTTP path on the container to send health checkss to. AI Platform
   * Prediction intermittently sends GET requests to this path on the
   * container's IP address and port to check that the container is healthy.
   * Read more about [health checks](/ai-platform/prediction/docs/custom-
   * container-requirements#checks). For example, if you set this field to
   * `/bar`, then AI Platform Prediction intermittently sends a GET request to
   * the `/bar` path on the port of your container specified by the first value
   * of Version.container.ports. If you don't specify this field, it defaults to
   * the following value: /v1/models/ MODEL/versions/VERSION The placeholders in
   * this value are replaced as follows: * MODEL: The name of the parent Model.
   * This does not include the "projects/PROJECT_ID/models/" prefix that the API
   * returns in output; it is the bare model name, as provided to
   * projects.models.create. * VERSION: The name of the model version. This does
   * not include the "projects/PROJECT_ID /models/MODEL/versions/" prefix that
   * the API returns in output; it is the bare version name, as provided to
   * projects.models.versions.create.
   *
   * @param string $health
   */
  public function setHealth($health)
  {
    $this->health = $health;
  }
  /**
   * @return string
   */
  public function getHealth()
  {
    return $this->health;
  }
  /**
   * HTTP path on the container to send prediction requests to. AI Platform
   * Prediction forwards requests sent using projects.predict to this path on
   * the container's IP address and port. AI Platform Prediction then returns
   * the container's response in the API response. For example, if you set this
   * field to `/foo`, then when AI Platform Prediction receives a prediction
   * request, it forwards the request body in a POST request to the `/foo` path
   * on the port of your container specified by the first value of
   * Version.container.ports. If you don't specify this field, it defaults to
   * the following value: /v1/models/MODEL/versions/VERSION:predict The
   * placeholders in this value are replaced as follows: * MODEL: The name of
   * the parent Model. This does not include the "projects/PROJECT_ID/models/"
   * prefix that the API returns in output; it is the bare model name, as
   * provided to projects.models.create. * VERSION: The name of the model
   * version. This does not include the
   * "projects/PROJECT_ID/models/MODEL/versions/" prefix that the API returns in
   * output; it is the bare version name, as provided to
   * projects.models.versions.create.
   *
   * @param string $predict
   */
  public function setPredict($predict)
  {
    $this->predict = $predict;
  }
  /**
   * @return string
   */
  public function getPredict()
  {
    return $this->predict;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1RouteMap::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1RouteMap');
