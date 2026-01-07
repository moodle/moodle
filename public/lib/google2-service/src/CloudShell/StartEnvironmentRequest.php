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

namespace Google\Service\CloudShell;

class StartEnvironmentRequest extends \Google\Collection
{
  protected $collection_key = 'publicKeys';
  /**
   * The initial access token passed to the environment. If this is present and
   * valid, the environment will be pre-authenticated with gcloud so that the
   * user can run gcloud commands in Cloud Shell without having to log in. This
   * code can be updated later by calling AuthorizeEnvironment.
   *
   * @var string
   */
  public $accessToken;
  /**
   * Public keys that should be added to the environment before it is started.
   *
   * @var string[]
   */
  public $publicKeys;

  /**
   * The initial access token passed to the environment. If this is present and
   * valid, the environment will be pre-authenticated with gcloud so that the
   * user can run gcloud commands in Cloud Shell without having to log in. This
   * code can be updated later by calling AuthorizeEnvironment.
   *
   * @param string $accessToken
   */
  public function setAccessToken($accessToken)
  {
    $this->accessToken = $accessToken;
  }
  /**
   * @return string
   */
  public function getAccessToken()
  {
    return $this->accessToken;
  }
  /**
   * Public keys that should be added to the environment before it is started.
   *
   * @param string[] $publicKeys
   */
  public function setPublicKeys($publicKeys)
  {
    $this->publicKeys = $publicKeys;
  }
  /**
   * @return string[]
   */
  public function getPublicKeys()
  {
    return $this->publicKeys;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StartEnvironmentRequest::class, 'Google_Service_CloudShell_StartEnvironmentRequest');
