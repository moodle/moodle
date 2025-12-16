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

class SessionContext extends \Google\Model
{
  /**
   * @var string
   */
  public $authTime;
  /**
   * @var string
   */
  public $delegateUserId;
  /**
   * @var string
   */
  public $dusi;
  protected $imapSessionContextType = ImapSessionContext::class;
  protected $imapSessionContextDataType = '';
  /**
   * @var int
   */
  public $oauthLoginId;
  /**
   * @var string
   */
  public $oauthProjectId;

  /**
   * @param string
   */
  public function setAuthTime($authTime)
  {
    $this->authTime = $authTime;
  }
  /**
   * @return string
   */
  public function getAuthTime()
  {
    return $this->authTime;
  }
  /**
   * @param string
   */
  public function setDelegateUserId($delegateUserId)
  {
    $this->delegateUserId = $delegateUserId;
  }
  /**
   * @return string
   */
  public function getDelegateUserId()
  {
    return $this->delegateUserId;
  }
  /**
   * @param string
   */
  public function setDusi($dusi)
  {
    $this->dusi = $dusi;
  }
  /**
   * @return string
   */
  public function getDusi()
  {
    return $this->dusi;
  }
  /**
   * @param ImapSessionContext
   */
  public function setImapSessionContext(ImapSessionContext $imapSessionContext)
  {
    $this->imapSessionContext = $imapSessionContext;
  }
  /**
   * @return ImapSessionContext
   */
  public function getImapSessionContext()
  {
    return $this->imapSessionContext;
  }
  /**
   * @param int
   */
  public function setOauthLoginId($oauthLoginId)
  {
    $this->oauthLoginId = $oauthLoginId;
  }
  /**
   * @return int
   */
  public function getOauthLoginId()
  {
    return $this->oauthLoginId;
  }
  /**
   * @param string
   */
  public function setOauthProjectId($oauthProjectId)
  {
    $this->oauthProjectId = $oauthProjectId;
  }
  /**
   * @return string
   */
  public function getOauthProjectId()
  {
    return $this->oauthProjectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SessionContext::class, 'Google_Service_CloudSearch_SessionContext');
