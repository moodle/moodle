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

class GoogleCloudAiplatformV1MigratableResourceMlEngineModelVersion extends \Google\Model
{
  /**
   * The ml.googleapis.com endpoint that this model Version currently lives in.
   * Example values: * ml.googleapis.com * us-centrall-ml.googleapis.com *
   * europe-west4-ml.googleapis.com * asia-east1-ml.googleapis.com
   *
   * @var string
   */
  public $endpoint;
  /**
   * Full resource name of ml engine model Version. Format:
   * `projects/{project}/models/{model}/versions/{version}`.
   *
   * @var string
   */
  public $version;

  /**
   * The ml.googleapis.com endpoint that this model Version currently lives in.
   * Example values: * ml.googleapis.com * us-centrall-ml.googleapis.com *
   * europe-west4-ml.googleapis.com * asia-east1-ml.googleapis.com
   *
   * @param string $endpoint
   */
  public function setEndpoint($endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return string
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
  /**
   * Full resource name of ml engine model Version. Format:
   * `projects/{project}/models/{model}/versions/{version}`.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MigratableResourceMlEngineModelVersion::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MigratableResourceMlEngineModelVersion');
