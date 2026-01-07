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

namespace Google\Service\Backupdr;

class WorkforceIdentityBasedManagementURI extends \Google\Model
{
  /**
   * Output only. First party Management URI for Google Identities.
   *
   * @var string
   */
  public $firstPartyManagementUri;
  /**
   * Output only. Third party Management URI for External Identity Providers.
   *
   * @var string
   */
  public $thirdPartyManagementUri;

  /**
   * Output only. First party Management URI for Google Identities.
   *
   * @param string $firstPartyManagementUri
   */
  public function setFirstPartyManagementUri($firstPartyManagementUri)
  {
    $this->firstPartyManagementUri = $firstPartyManagementUri;
  }
  /**
   * @return string
   */
  public function getFirstPartyManagementUri()
  {
    return $this->firstPartyManagementUri;
  }
  /**
   * Output only. Third party Management URI for External Identity Providers.
   *
   * @param string $thirdPartyManagementUri
   */
  public function setThirdPartyManagementUri($thirdPartyManagementUri)
  {
    $this->thirdPartyManagementUri = $thirdPartyManagementUri;
  }
  /**
   * @return string
   */
  public function getThirdPartyManagementUri()
  {
    return $this->thirdPartyManagementUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkforceIdentityBasedManagementURI::class, 'Google_Service_Backupdr_WorkforceIdentityBasedManagementURI');
