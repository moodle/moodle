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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaControlRedirectAction extends \Google\Model
{
  /**
   * Required. The URI to which the shopper will be redirected. Required. URI
   * must have length equal or less than 2000 characters. Otherwise an INVALID
   * ARGUMENT error is thrown.
   *
   * @var string
   */
  public $redirectUri;

  /**
   * Required. The URI to which the shopper will be redirected. Required. URI
   * must have length equal or less than 2000 characters. Otherwise an INVALID
   * ARGUMENT error is thrown.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaControlRedirectAction::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaControlRedirectAction');
