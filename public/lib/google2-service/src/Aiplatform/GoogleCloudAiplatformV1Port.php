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

class GoogleCloudAiplatformV1Port extends \Google\Model
{
  /**
   * The number of the port to expose on the pod's IP address. Must be a valid
   * port number, between 1 and 65535 inclusive.
   *
   * @var int
   */
  public $containerPort;

  /**
   * The number of the port to expose on the pod's IP address. Must be a valid
   * port number, between 1 and 65535 inclusive.
   *
   * @param int $containerPort
   */
  public function setContainerPort($containerPort)
  {
    $this->containerPort = $containerPort;
  }
  /**
   * @return int
   */
  public function getContainerPort()
  {
    return $this->containerPort;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Port::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Port');
