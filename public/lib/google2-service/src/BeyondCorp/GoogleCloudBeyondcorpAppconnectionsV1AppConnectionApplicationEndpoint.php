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

namespace Google\Service\BeyondCorp;

class GoogleCloudBeyondcorpAppconnectionsV1AppConnectionApplicationEndpoint extends \Google\Model
{
  /**
   * Required. Hostname or IP address of the remote application endpoint.
   *
   * @var string
   */
  public $host;
  /**
   * Required. Port of the remote application endpoint.
   *
   * @var int
   */
  public $port;

  /**
   * Required. Hostname or IP address of the remote application endpoint.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * Required. Port of the remote application endpoint.
   *
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBeyondcorpAppconnectionsV1AppConnectionApplicationEndpoint::class, 'Google_Service_BeyondCorp_GoogleCloudBeyondcorpAppconnectionsV1AppConnectionApplicationEndpoint');
