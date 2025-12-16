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

namespace Google\Service\NetworkManagement;

class CloudRunRevisionEndpoint extends \Google\Model
{
  /**
   * Output only. The URI of the Cloud Run service that the revision belongs to.
   * The format is: projects/{project}/locations/{location}/services/{service}
   *
   * @var string
   */
  public $serviceUri;
  /**
   * A [Cloud Run](https://cloud.google.com/run) [revision](https://cloud.google
   * .com/run/docs/reference/rest/v1/namespaces.revisions/get) URI. The format
   * is: projects/{project}/locations/{location}/revisions/{revision}
   *
   * @var string
   */
  public $uri;

  /**
   * Output only. The URI of the Cloud Run service that the revision belongs to.
   * The format is: projects/{project}/locations/{location}/services/{service}
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
  /**
   * A [Cloud Run](https://cloud.google.com/run) [revision](https://cloud.google
   * .com/run/docs/reference/rest/v1/namespaces.revisions/get) URI. The format
   * is: projects/{project}/locations/{location}/revisions/{revision}
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudRunRevisionEndpoint::class, 'Google_Service_NetworkManagement_CloudRunRevisionEndpoint');
