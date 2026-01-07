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

class CopyOtherContactToMyContactsGroupRequest extends \Google\Collection
{
  protected $collection_key = 'sources';
  /**
   * Required. A field mask to restrict which fields are copied into the new
   * contact. Valid values are: * emailAddresses * names * phoneNumbers
   *
   * @var string
   */
  public $copyMask;
  /**
   * Optional. A field mask to restrict which fields on the person are returned.
   * Multiple fields can be specified by separating them with commas. Defaults
   * to the copy mask with metadata and membership fields if not set. Valid
   * values are: * addresses * ageRanges * biographies * birthdays *
   * calendarUrls * clientData * coverPhotos * emailAddresses * events *
   * externalIds * genders * imClients * interests * locales * locations *
   * memberships * metadata * miscKeywords * names * nicknames * occupations *
   * organizations * phoneNumbers * photos * relations * sipAddresses * skills *
   * urls * userDefined
   *
   * @var string
   */
  public $readMask;
  /**
   * Optional. A mask of what source types to return. Defaults to
   * READ_SOURCE_TYPE_CONTACT and READ_SOURCE_TYPE_PROFILE if not set.
   *
   * @var string[]
   */
  public $sources;

  /**
   * Required. A field mask to restrict which fields are copied into the new
   * contact. Valid values are: * emailAddresses * names * phoneNumbers
   *
   * @param string $copyMask
   */
  public function setCopyMask($copyMask)
  {
    $this->copyMask = $copyMask;
  }
  /**
   * @return string
   */
  public function getCopyMask()
  {
    return $this->copyMask;
  }
  /**
   * Optional. A field mask to restrict which fields on the person are returned.
   * Multiple fields can be specified by separating them with commas. Defaults
   * to the copy mask with metadata and membership fields if not set. Valid
   * values are: * addresses * ageRanges * biographies * birthdays *
   * calendarUrls * clientData * coverPhotos * emailAddresses * events *
   * externalIds * genders * imClients * interests * locales * locations *
   * memberships * metadata * miscKeywords * names * nicknames * occupations *
   * organizations * phoneNumbers * photos * relations * sipAddresses * skills *
   * urls * userDefined
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
   * Optional. A mask of what source types to return. Defaults to
   * READ_SOURCE_TYPE_CONTACT and READ_SOURCE_TYPE_PROFILE if not set.
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
class_alias(CopyOtherContactToMyContactsGroupRequest::class, 'Google_Service_PeopleService_CopyOtherContactToMyContactsGroupRequest');
