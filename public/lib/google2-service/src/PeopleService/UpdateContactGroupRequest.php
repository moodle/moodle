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

class UpdateContactGroupRequest extends \Google\Model
{
  protected $contactGroupType = ContactGroup::class;
  protected $contactGroupDataType = '';
  /**
   * Optional. A field mask to restrict which fields on the group are returned.
   * Defaults to `metadata`, `groupType`, and `name` if not set or set to empty.
   * Valid fields are: * clientData * groupType * memberCount * metadata * name
   *
   * @var string
   */
  public $readGroupFields;
  /**
   * Optional. A field mask to restrict which fields on the group are updated.
   * Multiple fields can be specified by separating them with commas. Defaults
   * to `name` if not set or set to empty. Updated fields are replaced. Valid
   * values are: * clientData * name
   *
   * @var string
   */
  public $updateGroupFields;

  /**
   * Required. The contact group to update.
   *
   * @param ContactGroup $contactGroup
   */
  public function setContactGroup(ContactGroup $contactGroup)
  {
    $this->contactGroup = $contactGroup;
  }
  /**
   * @return ContactGroup
   */
  public function getContactGroup()
  {
    return $this->contactGroup;
  }
  /**
   * Optional. A field mask to restrict which fields on the group are returned.
   * Defaults to `metadata`, `groupType`, and `name` if not set or set to empty.
   * Valid fields are: * clientData * groupType * memberCount * metadata * name
   *
   * @param string $readGroupFields
   */
  public function setReadGroupFields($readGroupFields)
  {
    $this->readGroupFields = $readGroupFields;
  }
  /**
   * @return string
   */
  public function getReadGroupFields()
  {
    return $this->readGroupFields;
  }
  /**
   * Optional. A field mask to restrict which fields on the group are updated.
   * Multiple fields can be specified by separating them with commas. Defaults
   * to `name` if not set or set to empty. Updated fields are replaced. Valid
   * values are: * clientData * name
   *
   * @param string $updateGroupFields
   */
  public function setUpdateGroupFields($updateGroupFields)
  {
    $this->updateGroupFields = $updateGroupFields;
  }
  /**
   * @return string
   */
  public function getUpdateGroupFields()
  {
    return $this->updateGroupFields;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateContactGroupRequest::class, 'Google_Service_PeopleService_UpdateContactGroupRequest');
