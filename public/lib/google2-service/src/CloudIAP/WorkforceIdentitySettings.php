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

namespace Google\Service\CloudIAP;

class WorkforceIdentitySettings extends \Google\Collection
{
  protected $collection_key = 'workforcePools';
  protected $oauth2Type = OAuth2::class;
  protected $oauth2DataType = '';
  /**
   * The workforce pool resources. Only one workforce pool is accepted.
   *
   * @var string[]
   */
  public $workforcePools;

  /**
   * OAuth 2.0 settings for IAP to perform OIDC flow with workforce identity
   * federation services.
   *
   * @param OAuth2 $oauth2
   */
  public function setOauth2(OAuth2 $oauth2)
  {
    $this->oauth2 = $oauth2;
  }
  /**
   * @return OAuth2
   */
  public function getOauth2()
  {
    return $this->oauth2;
  }
  /**
   * The workforce pool resources. Only one workforce pool is accepted.
   *
   * @param string[] $workforcePools
   */
  public function setWorkforcePools($workforcePools)
  {
    $this->workforcePools = $workforcePools;
  }
  /**
   * @return string[]
   */
  public function getWorkforcePools()
  {
    return $this->workforcePools;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkforceIdentitySettings::class, 'Google_Service_CloudIAP_WorkforceIdentitySettings');
