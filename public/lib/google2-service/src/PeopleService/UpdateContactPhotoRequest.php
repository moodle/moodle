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

class UpdateContactPhotoRequest extends \Google\Collection
{
  protected $collection_key = 'sources';
  /**
   * Optional. A field mask to restrict which fields on the person are returned.
   * Multiple fields can be specified by separating them with commas. Defaults
   * to empty if not set, which will skip the post mutate get. Valid values are:
   * * addresses * ageRanges * biographies * birthdays * calendarUrls *
   * clientData * coverPhotos * emailAddresses * events * externalIds * genders
   * * imClients * interests * locales * locations * memberships * metadata *
   * miscKeywords * names * nicknames * occupations * organizations *
   * phoneNumbers * photos * relations * sipAddresses * skills * urls *
   * userDefined
   *
   * @var string
   */
  public $personFields;
  /**
   * Required. Raw photo bytes
   *
   * @var string
   */
  public $photoBytes;
  /**
   * Optional. A mask of what source types to return. Defaults to
   * READ_SOURCE_TYPE_CONTACT and READ_SOURCE_TYPE_PROFILE if not set.
   *
   * @var string[]
   */
  public $sources;

  /**
   * Optional. A field mask to restrict which fields on the person are returned.
   * Multiple fields can be specified by separating them with commas. Defaults
   * to empty if not set, which will skip the post mutate get. Valid values are:
   * * addresses * ageRanges * biographies * birthdays * calendarUrls *
   * clientData * coverPhotos * emailAddresses * events * externalIds * genders
   * * imClients * interests * locales * locations * memberships * metadata *
   * miscKeywords * names * nicknames * occupations * organizations *
   * phoneNumbers * photos * relations * sipAddresses * skills * urls *
   * userDefined
   *
   * @param string $personFields
   */
  public function setPersonFields($personFields)
  {
    $this->personFields = $personFields;
  }
  /**
   * @return string
   */
  public function getPersonFields()
  {
    return $this->personFields;
  }
  /**
   * Required. Raw photo bytes
   *
   * @param string $photoBytes
   */
  public function setPhotoBytes($photoBytes)
  {
    $this->photoBytes = $photoBytes;
  }
  /**
   * @return string
   */
  public function getPhotoBytes()
  {
    return $this->photoBytes;
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
class_alias(UpdateContactPhotoRequest::class, 'Google_Service_PeopleService_UpdateContactPhotoRequest');
