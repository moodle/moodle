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

namespace Google\Service\Analytics;

class AccountTicket extends \Google\Model
{
  protected $accountType = Account::class;
  protected $accountDataType = '';
  /**
   * Account ticket ID used to access the account ticket.
   *
   * @var string
   */
  public $id;
  /**
   * Resource type for account ticket.
   *
   * @var string
   */
  public $kind;
  protected $profileType = Profile::class;
  protected $profileDataType = '';
  /**
   * Redirect URI where the user will be sent after accepting Terms of Service.
   * Must be configured in APIs console as a callback URL.
   *
   * @var string
   */
  public $redirectUri;
  protected $webpropertyType = Webproperty::class;
  protected $webpropertyDataType = '';

  /**
   * Account for this ticket.
   *
   * @param Account $account
   */
  public function setAccount(Account $account)
  {
    $this->account = $account;
  }
  /**
   * @return Account
   */
  public function getAccount()
  {
    return $this->account;
  }
  /**
   * Account ticket ID used to access the account ticket.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Resource type for account ticket.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * View (Profile) for the account.
   *
   * @param Profile $profile
   */
  public function setProfile(Profile $profile)
  {
    $this->profile = $profile;
  }
  /**
   * @return Profile
   */
  public function getProfile()
  {
    return $this->profile;
  }
  /**
   * Redirect URI where the user will be sent after accepting Terms of Service.
   * Must be configured in APIs console as a callback URL.
   *
   * @param string $redirectUri
   */
  public function setRedirectUri($redirectUri)
  {
    $this->redirectUri = $redirectUri;
  }
  /**
   * @return string
   */
  public function getRedirectUri()
  {
    return $this->redirectUri;
  }
  /**
   * Web property for the account.
   *
   * @param Webproperty $webproperty
   */
  public function setWebproperty(Webproperty $webproperty)
  {
    $this->webproperty = $webproperty;
  }
  /**
   * @return Webproperty
   */
  public function getWebproperty()
  {
    return $this->webproperty;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountTicket::class, 'Google_Service_Analytics_AccountTicket');
