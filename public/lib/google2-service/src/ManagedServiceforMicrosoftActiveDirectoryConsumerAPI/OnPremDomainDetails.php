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

namespace Google\Service\ManagedServiceforMicrosoftActiveDirectoryConsumerAPI;

class OnPremDomainDetails extends \Google\Model
{
  /**
   * Optional. Option to disable SID filtering.
   *
   * @var bool
   */
  public $disableSidFiltering;
  /**
   * Required. FQDN of the on-prem domain being migrated.
   *
   * @var string
   */
  public $domainName;

  /**
   * Optional. Option to disable SID filtering.
   *
   * @param bool $disableSidFiltering
   */
  public function setDisableSidFiltering($disableSidFiltering)
  {
    $this->disableSidFiltering = $disableSidFiltering;
  }
  /**
   * @return bool
   */
  public function getDisableSidFiltering()
  {
    return $this->disableSidFiltering;
  }
  /**
   * Required. FQDN of the on-prem domain being migrated.
   *
   * @param string $domainName
   */
  public function setDomainName($domainName)
  {
    $this->domainName = $domainName;
  }
  /**
   * @return string
   */
  public function getDomainName()
  {
    return $this->domainName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OnPremDomainDetails::class, 'Google_Service_ManagedServiceforMicrosoftActiveDirectoryConsumerAPI_OnPremDomainDetails');
