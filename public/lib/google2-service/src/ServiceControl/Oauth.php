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

namespace Google\Service\ServiceControl;

class Oauth extends \Google\Model
{
  /**
   * The optional OAuth client ID. This is the unique public identifier issued
   * by an authorization server to a registered client application. Empty string
   * is equivalent to no oauth client id. WARNING: This is for MCP tools/call
   * and tools/list authorization and not for general use.
   *
   * @var string
   */
  public $clientId;

  /**
   * The optional OAuth client ID. This is the unique public identifier issued
   * by an authorization server to a registered client application. Empty string
   * is equivalent to no oauth client id. WARNING: This is for MCP tools/call
   * and tools/list authorization and not for general use.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Oauth::class, 'Google_Service_ServiceControl_Oauth');
