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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1HostingService extends \Google\Model
{
  /**
   * Optional. The URI of the service implemented by the plugin developer, used
   * to invoke the plugin's functionality. This information is only required for
   * user defined plugins.
   *
   * @var string
   */
  public $serviceUri;

  /**
   * Optional. The URI of the service implemented by the plugin developer, used
   * to invoke the plugin's functionality. This information is only required for
   * user defined plugins.
   *
   * @param string $serviceUri
   */
  public function setServiceUri($serviceUri)
  {
    $this->serviceUri = $serviceUri;
  }
  /**
   * @return string
   */
  public function getServiceUri()
  {
    return $this->serviceUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1HostingService::class, 'Google_Service_APIhub_GoogleCloudApihubV1HostingService');
