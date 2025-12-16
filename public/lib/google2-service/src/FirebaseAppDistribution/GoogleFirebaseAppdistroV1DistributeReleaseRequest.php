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

class GoogleFirebaseAppdistroV1DistributeReleaseRequest extends \Google\Collection
{
  protected $collection_key = 'testerEmails';
  /**
   * Optional. A list of group aliases (IDs) to be given access to this release.
   * A combined maximum of 999 `testerEmails` and `groupAliases` can be
   * specified in a single request.
   *
   * @var string[]
   */
  public $groupAliases;
  /**
   * Optional. A list of tester email addresses to be given access to this
   * release. A combined maximum of 999 `testerEmails` and `groupAliases` can be
   * specified in a single request.
   *
   * @var string[]
   */
  public $testerEmails;

  /**
   * Optional. A list of group aliases (IDs) to be given access to this release.
   * A combined maximum of 999 `testerEmails` and `groupAliases` can be
   * specified in a single request.
   *
   * @param string[] $groupAliases
   */
  public function setGroupAliases($groupAliases)
  {
    $this->groupAliases = $groupAliases;
  }
  /**
   * @return string[]
   */
  public function getGroupAliases()
  {
    return $this->groupAliases;
  }
  /**
   * Optional. A list of tester email addresses to be given access to this
   * release. A combined maximum of 999 `testerEmails` and `groupAliases` can be
   * specified in a single request.
   *
   * @param string[] $testerEmails
   */
  public function setTesterEmails($testerEmails)
  {
    $this->testerEmails = $testerEmails;
  }
  /**
   * @return string[]
   */
  public function getTesterEmails()
  {
    return $this->testerEmails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppdistroV1DistributeReleaseRequest::class, 'Google_Service_FirebaseAppDistribution_GoogleFirebaseAppdistroV1DistributeReleaseRequest');
