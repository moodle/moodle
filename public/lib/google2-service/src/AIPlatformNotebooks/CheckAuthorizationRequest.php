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

namespace Google\Service\AIPlatformNotebooks;

class CheckAuthorizationRequest extends \Google\Model
{
  /**
   * Optional. The details of the OAuth authorization response. This may include
   * additional params such as dry_run, version_info, origin, propagate, etc.
   *
   * @var string[]
   */
  public $authorizationDetails;

  /**
   * Optional. The details of the OAuth authorization response. This may include
   * additional params such as dry_run, version_info, origin, propagate, etc.
   *
   * @param string[] $authorizationDetails
   */
  public function setAuthorizationDetails($authorizationDetails)
  {
    $this->authorizationDetails = $authorizationDetails;
  }
  /**
   * @return string[]
   */
  public function getAuthorizationDetails()
  {
    return $this->authorizationDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckAuthorizationRequest::class, 'Google_Service_AIPlatformNotebooks_CheckAuthorizationRequest');
