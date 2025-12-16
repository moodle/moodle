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

class IdentityServiceUserConfig extends \Google\Model
{
  /**
   * Required. The location of the subtree in the LDAP directory to search for
   * user entries.
   *
   * @var string
   */
  public $baseDn;
  /**
   * Optional. Filter to apply when searching for the user. This can be used to
   * further restrict the user accounts which are allowed to login. This
   * defaults to "(objectClass=User)".
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. Determines which attribute to use as the user's identity after
   * they are authenticated. This is distinct from the loginAttribute field to
   * allow users to login with a username, but then have their actual identifier
   * be an email address or full Distinguished Name (DN). For example, setting
   * loginAttribute to "sAMAccountName" and identifierAttribute to
   * "userPrincipalName" would allow a user to login as "bsmith", but actual
   * RBAC policies for the user would be written as "bsmith@example.com". Using
   * "userPrincipalName" is recommended since this will be unique for each user.
   * This defaults to "userPrincipalName".
   *
   * @var string
   */
  public $idAttribute;
  /**
   * Optional. The name of the attribute which matches against the input
   * username. This is used to find the user in the LDAP database e.g. "(=)" and
   * is combined with the optional filter field. This defaults to
   * "userPrincipalName".
   *
   * @var string
   */
  public $loginAttribute;

  /**
   * Required. The location of the subtree in the LDAP directory to search for
   * user entries.
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
   * Optional. Filter to apply when searching for the user. This can be used to
   * further restrict the user accounts which are allowed to login. This
   * defaults to "(objectClass=User)".
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
   * Optional. Determines which attribute to use as the user's identity after
   * they are authenticated. This is distinct from the loginAttribute field to
   * allow users to login with a username, but then have their actual identifier
   * be an email address or full Distinguished Name (DN). For example, setting
   * loginAttribute to "sAMAccountName" and identifierAttribute to
   * "userPrincipalName" would allow a user to login as "bsmith", but actual
   * RBAC policies for the user would be written as "bsmith@example.com". Using
   * "userPrincipalName" is recommended since this will be unique for each user.
   * This defaults to "userPrincipalName".
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
  /**
   * Optional. The name of the attribute which matches against the input
   * username. This is used to find the user in the LDAP database e.g. "(=)" and
   * is combined with the optional filter field. This defaults to
   * "userPrincipalName".
   *
   * @param string $loginAttribute
   */
  public function setLoginAttribute($loginAttribute)
  {
    $this->loginAttribute = $loginAttribute;
  }
  /**
   * @return string
   */
  public function getLoginAttribute()
  {
    return $this->loginAttribute;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentityServiceUserConfig::class, 'Google_Service_GKEHub_IdentityServiceUserConfig');
