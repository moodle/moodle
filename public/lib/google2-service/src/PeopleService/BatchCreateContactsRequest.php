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

class BatchCreateContactsRequest extends \Google\Collection
{
  protected $collection_key = 'sources';
  protected $contactsType = ContactToCreate::class;
  protected $contactsDataType = 'array';
  /**
   * Required. A field mask to restrict which fields on each person are returned
   * in the response. Multiple fields can be specified by separating them with
   * commas. If read mask is left empty, the post-mutate-get is skipped and no
   * data will be returned in the response. Valid values are: * addresses *
   * ageRanges * biographies * birthdays * calendarUrls * clientData *
   * coverPhotos * emailAddresses * events * externalIds * genders * imClients *
   * interests * locales * locations * memberships * metadata * miscKeywords *
   * names * nicknames * occupations * organizations * phoneNumbers * photos *
   * relations * sipAddresses * skills * urls * userDefined
   *
   * @var string
   */
  public $readMask;
  /**
   * Optional. A mask of what source types to return in the post mutate read.
   * Defaults to READ_SOURCE_TYPE_CONTACT and READ_SOURCE_TYPE_PROFILE if not
   * set.
   *
   * @var string[]
   */
  public $sources;

  /**
   * Required. The contact to create. Allows up to 200 contacts in a single
   * request.
   *
   * @param ContactToCreate[] $contacts
   */
  public function setContacts($contacts)
  {
    $this->contacts = $contacts;
  }
  /**
   * @return ContactToCreate[]
   */
  public function getContacts()
  {
    return $this->contacts;
  }
  /**
   * Required. A field mask to restrict which fields on each person are returned
   * in the response. Multiple fields can be specified by separating them with
   * commas. If read mask is left empty, the post-mutate-get is skipped and no
   * data will be returned in the response. Valid values are: * addresses *
   * ageRanges * biographies * birthdays * calendarUrls * clientData *
   * coverPhotos * emailAddresses * events * externalIds * genders * imClients *
   * interests * locales * locations * memberships * metadata * miscKeywords *
   * names * nicknames * occupations * organizations * phoneNumbers * photos *
   * relations * sipAddresses * skills * urls * userDefined
   *
   * @param string $readMask
   */
  public function setReadMask($readMask)
  {
    $this->readMask = $readMask;
  }
  /**
   * @return string
   */
  public function getReadMask()
  {
    return $this->readMask;
  }
  /**
   * Optional. A mask of what source types to return in the post mutate read.
   * Defaults to READ_SOURCE_TYPE_CONTACT and READ_SOURCE_TYPE_PROFILE if not
   * set.
   *
   * @param string[] $sources
   */
  public function setSources($sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return string[]
   */
  public function getSources()
  {
    return $this->sources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchCreateContactsRequest::class, 'Google_Service_PeopleService_BatchCreateContactsRequest');
