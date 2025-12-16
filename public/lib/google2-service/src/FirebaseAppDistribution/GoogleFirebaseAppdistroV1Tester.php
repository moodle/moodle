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

namespace Google\Service\FirebaseAppDistribution;

class GoogleFirebaseAppdistroV1Tester extends \Google\Collection
{
  protected $collection_key = 'groups';
  /**
   * The name of the tester associated with the Google account used to accept
   * the tester invitation.
   *
   * @var string
   */
  public $displayName;
  /**
   * The resource names of the groups this tester belongs to.
   *
   * @var string[]
   */
  public $groups;
  /**
   * Output only. The time the tester was last active. This is the most recent
   * time the tester installed one of the apps. If they've never installed one
   * or if the release no longer exists, this is the time the tester was added
   * to the project.
   *
   * @var string
   */
  public $lastActivityTime;
  /**
   * The name of the tester resource. Format:
   * `projects/{project_number}/testers/{email_address}`
   *
   * @var string
   */
  public $name;

  /**
   * The name of the tester associated with the Google account used to accept
   * the tester invitation.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The resource names of the groups this tester belongs to.
   *
   * @param string[] $groups
   */
  public function setGroups($groups)
  {
    $this->groups = $groups;
  }
  /**
   * @return string[]
   */
  public function getGroups()
  {
    return $this->groups;
  }
  /**
   * Output only. The time the tester was last active. This is the most recent
   * time the tester installed one of the apps. If they've never installed one
   * or if the release no longer exists, this is the time the tester was added
   * to the project.
   *
   * @param string $lastActivityTime
   */
  public function setLastActivityTime($lastActivityTime)
  {
    $this->lastActivityTime = $lastActivityTime;
  }
  /**
   * @return string
   */
  public function getLastActivityTime()
  {
    return $this->lastActivityTime;
  }
  /**
   * The name of the tester resource. Format:
   * `projects/{project_number}/testers/{email_address}`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppdistroV1Tester::class, 'Google_Service_FirebaseAppDistribution_GoogleFirebaseAppdistroV1Tester');
