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

namespace Google\Service\PeopleService;

class ContactGroupMembership extends \Google\Model
{
  /**
   * Output only. The contact group ID for the contact group membership.
   *
   * @deprecated
   * @var string
   */
  public $contactGroupId;
  /**
   * The resource name for the contact group, assigned by the server. An ASCII
   * string, in the form of `contactGroups/{contact_group_id}`. Only
   * contact_group_resource_name can be used for modifying memberships. Any
   * contact group membership can be removed, but only user group or
   * "myContacts" or "starred" system groups memberships can be added. A contact
   * must always have at least one contact group membership.
   *
   * @var string
   */
  public $contactGroupResourceName;

  /**
   * Output only. The contact group ID for the contact group membership.
   *
   * @deprecated
   * @param string $contactGroupId
   */
  public function setContactGroupId($contactGroupId)
  {
    $this->contactGroupId = $contactGroupId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getContactGroupId()
  {
    return $this->contactGroupId;
  }
  /**
   * The resource name for the contact group, assigned by the server. An ASCII
   * string, in the form of `contactGroups/{contact_group_id}`. Only
   * contact_group_resource_name can be used for modifying memberships. Any
   * contact group membership can be removed, but only user group or
   * "myContacts" or "starred" system groups memberships can be added. A contact
   * must always have at least one contact group membership.
   *
   * @param string $contactGroupResourceName
   */
  public function setContactGroupResourceName($contactGroupResourceName)
  {
    $this->contactGroupResourceName = $contactGroupResourceName;
  }
  /**
   * @return string
   */
  public function getContactGroupResourceName()
  {
    return $this->contactGroupResourceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContactGroupMembership::class, 'Google_Service_PeopleService_ContactGroupMembership');
