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

class ContactGroup extends \Google\Collection
{
  /**
   * Unspecified.
   */
  public const GROUP_TYPE_GROUP_TYPE_UNSPECIFIED = 'GROUP_TYPE_UNSPECIFIED';
  /**
   * User defined contact group.
   */
  public const GROUP_TYPE_USER_CONTACT_GROUP = 'USER_CONTACT_GROUP';
  /**
   * System defined contact group.
   */
  public const GROUP_TYPE_SYSTEM_CONTACT_GROUP = 'SYSTEM_CONTACT_GROUP';
  protected $collection_key = 'memberResourceNames';
  protected $clientDataType = GroupClientData::class;
  protected $clientDataDataType = 'array';
  /**
   * The [HTTP entity tag](https://en.wikipedia.org/wiki/HTTP_ETag) of the
   * resource. Used for web cache validation.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. The name translated and formatted in the viewer's account
   * locale or the `Accept-Language` HTTP header locale for system groups names.
   * Group names set by the owner are the same as name.
   *
   * @var string
   */
  public $formattedName;
  /**
   * Output only. The contact group type.
   *
   * @var string
   */
  public $groupType;
  /**
   * Output only. The total number of contacts in the group irrespective of max
   * members in specified in the request.
   *
   * @var int
   */
  public $memberCount;
  /**
   * Output only. The list of contact person resource names that are members of
   * the contact group. The field is only populated for GET requests and will
   * only return as many members as `maxMembers` in the get request.
   *
   * @var string[]
   */
  public $memberResourceNames;
  protected $metadataType = ContactGroupMetadata::class;
  protected $metadataDataType = '';
  /**
   * The contact group name set by the group owner or a system provided name for
   * system groups. For
   * [`contactGroups.create`](/people/api/rest/v1/contactGroups/create) or
   * [`contactGroups.update`](/people/api/rest/v1/contactGroups/update) the name
   * must be unique to the users contact groups. Attempting to create a group
   * with a duplicate name will return a HTTP 409 error.
   *
   * @var string
   */
  public $name;
  /**
   * The resource name for the contact group, assigned by the server. An ASCII
   * string, in the form of `contactGroups/{contact_group_id}`.
   *
   * @var string
   */
  public $resourceName;

  /**
   * The group's client data.
   *
   * @param GroupClientData[] $clientData
   */
  public function setClientData($clientData)
  {
    $this->clientData = $clientData;
  }
  /**
   * @return GroupClientData[]
   */
  public function getClientData()
  {
    return $this->clientData;
  }
  /**
   * The [HTTP entity tag](https://en.wikipedia.org/wiki/HTTP_ETag) of the
   * resource. Used for web cache validation.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. The name translated and formatted in the viewer's account
   * locale or the `Accept-Language` HTTP header locale for system groups names.
   * Group names set by the owner are the same as name.
   *
   * @param string $formattedName
   */
  public function setFormattedName($formattedName)
  {
    $this->formattedName = $formattedName;
  }
  /**
   * @return string
   */
  public function getFormattedName()
  {
    return $this->formattedName;
  }
  /**
   * Output only. The contact group type.
   *
   * Accepted values: GROUP_TYPE_UNSPECIFIED, USER_CONTACT_GROUP,
   * SYSTEM_CONTACT_GROUP
   *
   * @param self::GROUP_TYPE_* $groupType
   */
  public function setGroupType($groupType)
  {
    $this->groupType = $groupType;
  }
  /**
   * @return self::GROUP_TYPE_*
   */
  public function getGroupType()
  {
    return $this->groupType;
  }
  /**
   * Output only. The total number of contacts in the group irrespective of max
   * members in specified in the request.
   *
   * @param int $memberCount
   */
  public function setMemberCount($memberCount)
  {
    $this->memberCount = $memberCount;
  }
  /**
   * @return int
   */
  public function getMemberCount()
  {
    return $this->memberCount;
  }
  /**
   * Output only. The list of contact person resource names that are members of
   * the contact group. The field is only populated for GET requests and will
   * only return as many members as `maxMembers` in the get request.
   *
   * @param string[] $memberResourceNames
   */
  public function setMemberResourceNames($memberResourceNames)
  {
    $this->memberResourceNames = $memberResourceNames;
  }
  /**
   * @return string[]
   */
  public function getMemberResourceNames()
  {
    return $this->memberResourceNames;
  }
  /**
   * Output only. Metadata about the contact group.
   *
   * @param ContactGroupMetadata $metadata
   */
  public function setMetadata(ContactGroupMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return ContactGroupMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The contact group name set by the group owner or a system provided name for
   * system groups. For
   * [`contactGroups.create`](/people/api/rest/v1/contactGroups/create) or
   * [`contactGroups.update`](/people/api/rest/v1/contactGroups/update) the name
   * must be unique to the users contact groups. Attempting to create a group
   * with a duplicate name will return a HTTP 409 error.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The resource name for the contact group, assigned by the server. An ASCII
   * string, in the form of `contactGroups/{contact_group_id}`.
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContactGroup::class, 'Google_Service_PeopleService_ContactGroup');
