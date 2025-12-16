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

namespace Google\Service\BigQueryConnectionService;

class ConnectorConfigurationEndpoint extends \Google\Model
{
  /**
   * Host and port in a format of `hostname:port` as defined in
   * https://www.ietf.org/rfc/rfc3986.html#section-3.2.2 and
   * https://www.ietf.org/rfc/rfc3986.html#section-3.2.3.
   *
   * @var string
   */
  public $hostPort;

  /**
   * Host and port in a format of `hostname:port` as defined in
   * https://www.ietf.org/rfc/rfc3986.html#section-3.2.2 and
   * https://www.ietf.org/rfc/rfc3986.html#section-3.2.3.
   *
   * @param string $hostPort
   */
  public function setHostPort($hostPort)
  {
    $this->hostPort = $hostPort;
  }
  /**
   * @return string
   */
  public function getHostPort()
  {
    return $this->hostPort;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConnectorConfigurationEndpoint::class, 'Google_Service_BigQueryConnectionService_ConnectorConfigurationEndpoint');
