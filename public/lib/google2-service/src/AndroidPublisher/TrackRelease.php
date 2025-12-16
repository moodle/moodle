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

namespace Google\Service\AndroidPublisher;

class TrackRelease extends \Google\Collection
{
  /**
   * Unspecified status.
   */
  public const STATUS_statusUnspecified = 'statusUnspecified';
  /**
   * The release's APKs are not being served to users.
   */
  public const STATUS_draft = 'draft';
  /**
   * The release's APKs are being served to a fraction of users, determined by
   * 'user_fraction'.
   */
  public const STATUS_inProgress = 'inProgress';
  /**
   * The release's APKs will no longer be served to users. Users who already
   * have these APKs are unaffected.
   */
  public const STATUS_halted = 'halted';
  /**
   * The release will have no further changes. Its APKs are being served to all
   * users, unless they are eligible to APKs of a more recent release.
   */
  public const STATUS_completed = 'completed';
  protected $collection_key = 'versionCodes';
  protected $countryTargetingType = CountryTargeting::class;
  protected $countryTargetingDataType = '';
  /**
   * In-app update priority of the release. All newly added APKs in the release
   * will be considered at this priority. Can take values in the range [0, 5],
   * with 5 the highest priority. Defaults to 0. in_app_update_priority can not
   * be updated once the release is rolled out. See
   * https://developer.android.com/guide/playcore/in-app-updates.
   *
   * @var int
   */
  public $inAppUpdatePriority;
  /**
   * The release name. Not required to be unique. If not set, the name is
   * generated from the APK's version_name. If the release contains multiple
   * APKs, the name is generated from the date.
   *
   * @var string
   */
  public $name;
  protected $releaseNotesType = LocalizedText::class;
  protected $releaseNotesDataType = 'array';
  /**
   * The status of the release.
   *
   * @var string
   */
  public $status;
  /**
   * Fraction of users who are eligible for a staged release. 0 < fraction < 1.
   * Can only be set when status is "inProgress" or "halted".
   *
   * @var 
   */
  public $userFraction;
  /**
   * Version codes of all APKs in the release. Must include version codes to
   * retain from previous releases.
   *
   * @var string[]
   */
  public $versionCodes;

  /**
   * Restricts a release to a specific set of countries. Note this is only
   * allowed to be set for inProgress releases in the production track.
   *
   * @param CountryTargeting $countryTargeting
   */
  public function setCountryTargeting(CountryTargeting $countryTargeting)
  {
    $this->countryTargeting = $countryTargeting;
  }
  /**
   * @return CountryTargeting
   */
  public function getCountryTargeting()
  {
    return $this->countryTargeting;
  }
  /**
   * In-app update priority of the release. All newly added APKs in the release
   * will be considered at this priority. Can take values in the range [0, 5],
   * with 5 the highest priority. Defaults to 0. in_app_update_priority can not
   * be updated once the release is rolled out. See
   * https://developer.android.com/guide/playcore/in-app-updates.
   *
   * @param int $inAppUpdatePriority
   */
  public function setInAppUpdatePriority($inAppUpdatePriority)
  {
    $this->inAppUpdatePriority = $inAppUpdatePriority;
  }
  /**
   * @return int
   */
  public function getInAppUpdatePriority()
  {
    return $this->inAppUpdatePriority;
  }
  /**
   * The release name. Not required to be unique. If not set, the name is
   * generated from the APK's version_name. If the release contains multiple
   * APKs, the name is generated from the date.
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
   * A description of what is new in this release.
   *
   * @param LocalizedText[] $releaseNotes
   */
  public function setReleaseNotes($releaseNotes)
  {
    $this->releaseNotes = $releaseNotes;
  }
  /**
   * @return LocalizedText[]
   */
  public function getReleaseNotes()
  {
    return $this->releaseNotes;
  }
  /**
   * The status of the release.
   *
   * Accepted values: statusUnspecified, draft, inProgress, halted, completed
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  public function setUserFraction($userFraction)
  {
    $this->userFraction = $userFraction;
  }
  public function getUserFraction()
  {
    return $this->userFraction;
  }
  /**
   * Version codes of all APKs in the release. Must include version codes to
   * retain from previous releases.
   *
   * @param string[] $versionCodes
   */
  public function setVersionCodes($versionCodes)
  {
    $this->versionCodes = $versionCodes;
  }
  /**
   * @return string[]
   */
  public function getVersionCodes()
  {
    return $this->versionCodes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TrackRelease::class, 'Google_Service_AndroidPublisher_TrackRelease');
