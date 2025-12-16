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

namespace Google\Service\GKEHub;

class IdentityServiceGroupConfig extends \Google\Model
{
  /**
   * Required. The location of the subtree in the LDAP directory to search for
   * group entries.
   *
   * @var string
   */
  public $baseDn;
  /**
   * Optional. Optional filter to be used when searching for groups a user
   * belongs to. This can be used to explicitly match only certain groups in
   * order to reduce the amount of groups returned for each user. This defaults
   * to "(objectClass=Group)".
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. The identifying name of each group a user belongs to. For
   * example, if this is set to "distinguishedName" then RBACs and other group
   * expectations should be written as full DNs. This defaults to
   * "distinguishedName".
   *
   * @var string
   */
  public $idAttribute;

  /**
   * Required. The location of the subtree in the LDAP directory to search for
   * group entries.
   *
   * @param string $baseDn
   */
  public function setBaseDn($baseDn)
  {
    $this->baseDn = $baseDn;
  }
  /**
   * @return string
   */
  public function getBaseDn()
  {
    return $this->baseDn;
  }
  /**
   * Optional. Optional filter to be used when searching for groups a user
   * belongs to. This can be used to explicitly match only certain groups in
   * order to reduce the amount of groups returned for each user. This defaults
   * to "(objectClass=Group)".
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Optional. The identifying name of each group a user belongs to. For
   * example, if this is set to "distinguishedName" then RBACs and other group
   * expectations should be written as full DNs. This defaults to
   * "distinguishedName".
   *
   * @param string $idAttribute
   */
  public function setIdAttribute($idAttribute)
  {
    $this->idAttribute = $idAttribute;
  }
  /**
   * @return string
   */
  public function getIdAttribute()
  {
    return $this->idAttribute;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentityServiceGroupConfig::class, 'Google_Service_GKEHub_IdentityServiceGroupConfig');
