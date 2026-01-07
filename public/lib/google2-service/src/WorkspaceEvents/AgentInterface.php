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

namespace Google\Service\WorkspaceEvents;

class AgentInterface extends \Google\Model
{
  /**
   * The transport supported this url. This is an open form string, to be easily
   * extended for many transport protocols. The core ones officially supported
   * are JSONRPC, GRPC and HTTP+JSON.
   *
   * @var string
   */
  public $transport;
  /**
   * The url this interface is found at.
   *
   * @var string
   */
  public $url;

  /**
   * The transport supported this url. This is an open form string, to be easily
   * extended for many transport protocols. The core ones officially supported
   * are JSONRPC, GRPC and HTTP+JSON.
   *
   * @param string $transport
   */
  public function setTransport($transport)
  {
    $this->transport = $transport;
  }
  /**
   * @return string
   */
  public function getTransport()
  {
    return $this->transport;
  }
  /**
   * The url this interface is found at.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgentInterface::class, 'Google_Service_WorkspaceEvents_AgentInterface');
