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

class PersonMetadata extends \Google\Collection
{
  /**
   * Unspecified.
   */
  public const OBJECT_TYPE_OBJECT_TYPE_UNSPECIFIED = 'OBJECT_TYPE_UNSPECIFIED';
  /**
   * Person.
   */
  public const OBJECT_TYPE_PERSON = 'PERSON';
  /**
   * [Currents Page.](https://workspace.google.com/products/currents/)
   */
  public const OBJECT_TYPE_PAGE = 'PAGE';
  protected $collection_key = 'sources';
  /**
   * Output only. True if the person resource has been deleted. Populated only
   * for `people.connections.list` and `otherContacts.list` sync requests.
   *
   * @var bool
   */
  public $deleted;
  /**
   * Output only. Resource names of people linked to this resource.
   *
   * @var string[]
   */
  public $linkedPeopleResourceNames;
  /**
   * Output only. **DEPRECATED** (Please use
   * `person.metadata.sources.profileMetadata.objectType` instead) The type of
   * the person object.
   *
   * @deprecated
   * @var string
   */
  public $objectType;
  /**
   * Output only. Any former resource names this person has had. Populated only
   * for `people.connections.list` requests that include a sync token. The
   * resource name may change when adding or removing fields that link a contact
   * and profile such as a verified email, verified phone number, or profile
   * URL.
   *
   * @var string[]
   */
  public $previousResourceNames;
  protected $sourcesType = Source::class;
  protected $sourcesDataType = 'array';

  /**
   * Output only. True if the person resource has been deleted. Populated only
   * for `people.connections.list` and `otherContacts.list` sync requests.
   *
   * @param bool $deleted
   */
  public function setDeleted($deleted)
  {
    $this->deleted = $deleted;
  }
  /**
   * @return bool
   */
  public function getDeleted()
  {
    return $this->deleted;
  }
  /**
   * Output only. Resource names of people linked to this resource.
   *
   * @param string[] $linkedPeopleResourceNames
   */
  public function setLinkedPeopleResourceNames($linkedPeopleResourceNames)
  {
    $this->linkedPeopleResourceNames = $linkedPeopleResourceNames;
  }
  /**
   * @return string[]
   */
  public function getLinkedPeopleResourceNames()
  {
    return $this->linkedPeopleResourceNames;
  }
  /**
   * Output only. **DEPRECATED** (Please use
   * `person.metadata.sources.profileMetadata.objectType` instead) The type of
   * the person object.
   *
   * Accepted values: OBJECT_TYPE_UNSPECIFIED, PERSON, PAGE
   *
   * @deprecated
   * @param self::OBJECT_TYPE_* $objectType
   */
  public function setObjectType($objectType)
  {
    $this->objectType = $objectType;
  }
  /**
   * @deprecated
   * @return self::OBJECT_TYPE_*
   */
  public function getObjectType()
  {
    return $this->objectType;
  }
  /**
   * Output only. Any former resource names this person has had. Populated only
   * for `people.connections.list` requests that include a sync token. The
   * resource name may change when adding or removing fields that link a contact
   * and profile such as a verified email, verified phone number, or profile
   * URL.
   *
   * @param string[] $previousResourceNames
   */
  public function setPreviousResourceNames($previousResourceNames)
  {
    $this->previousResourceNames = $previousResourceNames;
  }
  /**
   * @return string[]
   */
  public function getPreviousResourceNames()
  {
    return $this->previousResourceNames;
  }
  /**
   * The sources of data for the person.
   *
   * @param Source[] $sources
   */
  public function setSources($sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return Source[]
   */
  public function getSources()
  {
    return $this->sources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PersonMetadata::class, 'Google_Service_PeopleService_PersonMetadata');
