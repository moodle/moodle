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

class ProfileMetadata extends \Google\Collection
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
  protected $collection_key = 'userTypes';
  /**
   * Output only. The profile object type.
   *
   * @var string
   */
  public $objectType;
  /**
   * Output only. The user types.
   *
   * @var string[]
   */
  public $userTypes;

  /**
   * Output only. The profile object type.
   *
   * Accepted values: OBJECT_TYPE_UNSPECIFIED, PERSON, PAGE
   *
   * @param self::OBJECT_TYPE_* $objectType
   */
  public function setObjectType($objectType)
  {
    $this->objectType = $objectType;
  }
  /**
   * @return self::OBJECT_TYPE_*
   */
  public function getObjectType()
  {
    return $this->objectType;
  }
  /**
   * Output only. The user types.
   *
   * @param string[] $userTypes
   */
  public function setUserTypes($userTypes)
  {
    $this->userTypes = $userTypes;
  }
  /**
   * @return string[]
   */
  public function getUserTypes()
  {
    return $this->userTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProfileMetadata::class, 'Google_Service_PeopleService_ProfileMetadata');
