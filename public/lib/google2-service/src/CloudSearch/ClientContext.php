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

namespace Google\Service\CloudSearch;

class ClientContext extends \Google\Model
{
  /**
   * @var string
   */
  public $clientOperationId;
  /**
   * @var string
   */
  public $clientType;
  protected $sessionContextType = SessionContext::class;
  protected $sessionContextDataType = '';
  /**
   * @var string
   */
  public $userIp;

  /**
   * @param string
   */
  public function setClientOperationId($clientOperationId)
  {
    $this->clientOperationId = $clientOperationId;
  }
  /**
   * @return string
   */
  public function getClientOperationId()
  {
    return $this->clientOperationId;
  }
  /**
   * @param string
   */
  public function setClientType($clientType)
  {
    $this->clientType = $clientType;
  }
  /**
   * @return string
   */
  public function getClientType()
  {
    return $this->clientType;
  }
  /**
   * @param SessionContext
   */
  public function setSessionContext(SessionContext $sessionContext)
  {
    $this->sessionContext = $sessionContext;
  }
  /**
   * @return SessionContext
   */
  public function getSessionContext()
  {
    return $this->sessionContext;
  }
  /**
   * @param string
   */
  public function setUserIp($userIp)
  {
    $this->userIp = $userIp;
  }
  /**
   * @return string
   */
  public function getUserIp()
  {
    return $this->userIp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClientContext::class, 'Google_Service_CloudSearch_ClientContext');
