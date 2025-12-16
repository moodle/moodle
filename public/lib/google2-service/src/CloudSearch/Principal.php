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

namespace Google\Service\CloudSearch;

class Principal extends \Google\Model
{
  /**
   * This principal is a group identified using an external identity. The name
   * field must specify the group resource name with this format:
   * identitysources/{source_id}/groups/{ID}
   *
   * @var string
   */
  public $groupResourceName;
  protected $gsuitePrincipalType = GSuitePrincipal::class;
  protected $gsuitePrincipalDataType = '';
  /**
   * This principal is a user identified using an external identity. The name
   * field must specify the user resource name with this format:
   * identitysources/{source_id}/users/{ID}
   *
   * @var string
   */
  public $userResourceName;

  /**
   * This principal is a group identified using an external identity. The name
   * field must specify the group resource name with this format:
   * identitysources/{source_id}/groups/{ID}
   *
   * @param string $groupResourceName
   */
  public function setGroupResourceName($groupResourceName)
  {
    $this->groupResourceName = $groupResourceName;
  }
  /**
   * @return string
   */
  public function getGroupResourceName()
  {
    return $this->groupResourceName;
  }
  /**
   * This principal is a Google Workspace user, group or domain.
   *
   * @param GSuitePrincipal $gsuitePrincipal
   */
  public function setGsuitePrincipal(GSuitePrincipal $gsuitePrincipal)
  {
    $this->gsuitePrincipal = $gsuitePrincipal;
  }
  /**
   * @return GSuitePrincipal
   */
  public function getGsuitePrincipal()
  {
    return $this->gsuitePrincipal;
  }
  /**
   * This principal is a user identified using an external identity. The name
   * field must specify the user resource name with this format:
   * identitysources/{source_id}/users/{ID}
   *
   * @param string $userResourceName
   */
  public function setUserResourceName($userResourceName)
  {
    $this->userResourceName = $userResourceName;
  }
  /**
   * @return string
   */
  public function getUserResourceName()
  {
    return $this->userResourceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Principal::class, 'Google_Service_CloudSearch_Principal');
