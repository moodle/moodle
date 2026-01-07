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

namespace Google\Service\DataManager;

class UserData extends \Google\Collection
{
  protected $collection_key = 'userIdentifiers';
  protected $userIdentifiersType = UserIdentifier::class;
  protected $userIdentifiersDataType = 'array';

  /**
   * Required. The identifiers for the user. It's possible to provide multiple
   * instances of the same type of data (for example, multiple email addresses).
   * To increase the likelihood of a match, provide as many identifiers as
   * possible. At most 10 `userIdentifiers` can be provided in a single
   * AudienceMember or Event.
   *
   * @param UserIdentifier[] $userIdentifiers
   */
  public function setUserIdentifiers($userIdentifiers)
  {
    $this->userIdentifiers = $userIdentifiers;
  }
  /**
   * @return UserIdentifier[]
   */
  public function getUserIdentifiers()
  {
    return $this->userIdentifiers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserData::class, 'Google_Service_DataManager_UserData');
