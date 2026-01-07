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

namespace Google\Service\AndroidManagement;

class GenerateEnterpriseUpgradeUrlRequest extends \Google\Collection
{
  protected $collection_key = 'allowedDomains';
  /**
   * Optional. Email address used to prefill the admin field of the enterprise
   * signup form as part of the upgrade process. This value is a hint only and
   * can be altered by the user. Personal email addresses are not allowed. If
   * allowedDomains is non-empty then this must belong to one of the
   * allowedDomains.
   *
   * @var string
   */
  public $adminEmail;
  /**
   * Optional. A list of domains that are permitted for the admin email. The IT
   * admin cannot enter an email address with a domain name that is not in this
   * list. Subdomains of domains in this list are not allowed but can be allowed
   * by adding a second entry which has *. prefixed to the domain name (e.g.
   * *.example.com). If the field is not present or is an empty list then the IT
   * admin is free to use any valid domain name. Personal email domains are not
   * allowed.
   *
   * @var string[]
   */
  public $allowedDomains;

  /**
   * Optional. Email address used to prefill the admin field of the enterprise
   * signup form as part of the upgrade process. This value is a hint only and
   * can be altered by the user. Personal email addresses are not allowed. If
   * allowedDomains is non-empty then this must belong to one of the
   * allowedDomains.
   *
   * @param string $adminEmail
   */
  public function setAdminEmail($adminEmail)
  {
    $this->adminEmail = $adminEmail;
  }
  /**
   * @return string
   */
  public function getAdminEmail()
  {
    return $this->adminEmail;
  }
  /**
   * Optional. A list of domains that are permitted for the admin email. The IT
   * admin cannot enter an email address with a domain name that is not in this
   * list. Subdomains of domains in this list are not allowed but can be allowed
   * by adding a second entry which has *. prefixed to the domain name (e.g.
   * *.example.com). If the field is not present or is an empty list then the IT
   * admin is free to use any valid domain name. Personal email domains are not
   * allowed.
   *
   * @param string[] $allowedDomains
   */
  public function setAllowedDomains($allowedDomains)
  {
    $this->allowedDomains = $allowedDomains;
  }
  /**
   * @return string[]
   */
  public function getAllowedDomains()
  {
    return $this->allowedDomains;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateEnterpriseUpgradeUrlRequest::class, 'Google_Service_AndroidManagement_GenerateEnterpriseUpgradeUrlRequest');
