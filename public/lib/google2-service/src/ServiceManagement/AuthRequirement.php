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

namespace Google\Service\ServiceManagement;

class AuthRequirement extends \Google\Model
{
  /**
   * NOTE: This will be deprecated soon, once AuthProvider.audiences is
   * implemented and accepted in all the runtime components. The list of JWT
   * [audiences](https://tools.ietf.org/html/draft-ietf-oauth-json-web-
   * token-32#section-4.1.3). that are allowed to access. A JWT containing any
   * of these audiences will be accepted. When this setting is absent, only JWTs
   * with audience "https://Service_name/API_name" will be accepted. For
   * example, if no audiences are in the setting, LibraryService API will only
   * accept JWTs with the following audience "https://library-
   * example.googleapis.com/google.example.library.v1.LibraryService". Example:
   * audiences: bookstore_android.apps.googleusercontent.com,
   * bookstore_web.apps.googleusercontent.com
   *
   * @var string
   */
  public $audiences;
  /**
   * id from authentication provider. Example: provider_id: bookstore_auth
   *
   * @var string
   */
  public $providerId;

  /**
   * NOTE: This will be deprecated soon, once AuthProvider.audiences is
   * implemented and accepted in all the runtime components. The list of JWT
   * [audiences](https://tools.ietf.org/html/draft-ietf-oauth-json-web-
   * token-32#section-4.1.3). that are allowed to access. A JWT containing any
   * of these audiences will be accepted. When this setting is absent, only JWTs
   * with audience "https://Service_name/API_name" will be accepted. For
   * example, if no audiences are in the setting, LibraryService API will only
   * accept JWTs with the following audience "https://library-
   * example.googleapis.com/google.example.library.v1.LibraryService". Example:
   * audiences: bookstore_android.apps.googleusercontent.com,
   * bookstore_web.apps.googleusercontent.com
   *
   * @param string $audiences
   */
  public function setAudiences($audiences)
  {
    $this->audiences = $audiences;
  }
  /**
   * @return string
   */
  public function getAudiences()
  {
    return $this->audiences;
  }
  /**
   * id from authentication provider. Example: provider_id: bookstore_auth
   *
   * @param string $providerId
   */
  public function setProviderId($providerId)
  {
    $this->providerId = $providerId;
  }
  /**
   * @return string
   */
  public function getProviderId()
  {
    return $this->providerId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthRequirement::class, 'Google_Service_ServiceManagement_AuthRequirement');
