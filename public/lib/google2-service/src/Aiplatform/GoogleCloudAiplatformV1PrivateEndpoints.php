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

class GoogleCloudAiplatformV1PrivateEndpoints extends \Google\Model
{
  /**
   * Output only. Http(s) path to send explain requests.
   *
   * @var string
   */
  public $explainHttpUri;
  /**
   * Output only. Http(s) path to send health check requests.
   *
   * @var string
   */
  public $healthHttpUri;
  /**
   * Output only. Http(s) path to send prediction requests.
   *
   * @var string
   */
  public $predictHttpUri;
  /**
   * Output only. The name of the service attachment resource. Populated if
   * private service connect is enabled.
   *
   * @var string
   */
  public $serviceAttachment;

  /**
   * Output only. Http(s) path to send explain requests.
   *
   * @param string $explainHttpUri
   */
  public function setExplainHttpUri($explainHttpUri)
  {
    $this->explainHttpUri = $explainHttpUri;
  }
  /**
   * @return string
   */
  public function getExplainHttpUri()
  {
    return $this->explainHttpUri;
  }
  /**
   * Output only. Http(s) path to send health check requests.
   *
   * @param string $healthHttpUri
   */
  public function setHealthHttpUri($healthHttpUri)
  {
    $this->healthHttpUri = $healthHttpUri;
  }
  /**
   * @return string
   */
  public function getHealthHttpUri()
  {
    return $this->healthHttpUri;
  }
  /**
   * Output only. Http(s) path to send prediction requests.
   *
   * @param string $predictHttpUri
   */
  public function setPredictHttpUri($predictHttpUri)
  {
    $this->predictHttpUri = $predictHttpUri;
  }
  /**
   * @return string
   */
  public function getPredictHttpUri()
  {
    return $this->predictHttpUri;
  }
  /**
   * Output only. The name of the service attachment resource. Populated if
   * private service connect is enabled.
   *
   * @param string $serviceAttachment
   */
  public function setServiceAttachment($serviceAttachment)
  {
    $this->serviceAttachment = $serviceAttachment;
  }
  /**
   * @return string
   */
  public function getServiceAttachment()
  {
    return $this->serviceAttachment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PrivateEndpoints::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PrivateEndpoints');
