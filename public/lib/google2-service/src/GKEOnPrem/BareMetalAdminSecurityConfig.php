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

namespace Google\Service\GKEOnPrem;

class BareMetalAdminSecurityConfig extends \Google\Model
{
  protected $authorizationType = Authorization::class;
  protected $authorizationDataType = '';

  /**
   * Configures user access to the admin cluster.
   *
   * @param Authorization $authorization
   */
  public function setAuthorization(Authorization $authorization)
  {
    $this->authorization = $authorization;
  }
  /**
   * @return Authorization
   */
  public function getAuthorization()
  {
    return $this->authorization;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalAdminSecurityConfig::class, 'Google_Service_GKEOnPrem_BareMetalAdminSecurityConfig');
