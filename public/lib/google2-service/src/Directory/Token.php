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

namespace Google\Service\Directory;

class Token extends \Google\Collection
{
  protected $collection_key = 'scopes';
  /**
   * Whether the application is registered with Google. The value is `true` if
   * the application has an anonymous Client ID.
   *
   * @var bool
   */
  public $anonymous;
  /**
   * The Client ID of the application the token is issued to.
   *
   * @var string
   */
  public $clientId;
  /**
   * The displayable name of the application the token is issued to.
   *
   * @var string
   */
  public $displayText;
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The type of the API resource. This is always `admin#directory#token`.
   *
   * @var string
   */
  public $kind;
  /**
   * Whether the token is issued to an installed application. The value is
   * `true` if the application is installed to a desktop or mobile device.
   *
   * @var bool
   */
  public $nativeApp;
  /**
   * A list of authorization scopes the application is granted.
   *
   * @var string[]
   */
  public $scopes;
  /**
   * The unique ID of the user that issued the token.
   *
   * @var string
   */
  public $userKey;

  /**
   * Whether the application is registered with Google. The value is `true` if
   * the application has an anonymous Client ID.
   *
   * @param bool $anonymous
   */
  public function setAnonymous($anonymous)
  {
    $this->anonymous = $anonymous;
  }
  /**
   * @return bool
   */
  public function getAnonymous()
  {
    return $this->anonymous;
  }
  /**
   * The Client ID of the application the token is issued to.
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
  /**
   * The displayable name of the application the token is issued to.
   *
   * @param string $displayText
   */
  public function setDisplayText($displayText)
  {
    $this->displayText = $displayText;
  }
  /**
   * @return string
   */
  public function getDisplayText()
  {
    return $this->displayText;
  }
  /**
   * ETag of the resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The type of the API resource. This is always `admin#directory#token`.
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
   * Whether the token is issued to an installed application. The value is
   * `true` if the application is installed to a desktop or mobile device.
   *
   * @param bool $nativeApp
   */
  public function setNativeApp($nativeApp)
  {
    $this->nativeApp = $nativeApp;
  }
  /**
   * @return bool
   */
  public function getNativeApp()
  {
    return $this->nativeApp;
  }
  /**
   * A list of authorization scopes the application is granted.
   *
   * @param string[] $scopes
   */
  public function setScopes($scopes)
  {
    $this->scopes = $scopes;
  }
  /**
   * @return string[]
   */
  public function getScopes()
  {
    return $this->scopes;
  }
  /**
   * The unique ID of the user that issued the token.
   *
   * @param string $userKey
   */
  public function setUserKey($userKey)
  {
    $this->userKey = $userKey;
  }
  /**
   * @return string
   */
  public function getUserKey()
  {
    return $this->userKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Token::class, 'Google_Service_Directory_Token');
