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

class GoogleFirebaseAppdistroV1Release extends \Google\Model
{
  /**
   * Output only. A signed link (which expires in one hour) to directly download
   * the app binary (IPA/APK/AAB) file.
   *
   * @var string
   */
  public $binaryDownloadUri;
  /**
   * Output only. Build version of the release. For an Android release, the
   * build version is the `versionCode`. For an iOS release, the build version
   * is the `CFBundleVersion`.
   *
   * @var string
   */
  public $buildVersion;
  /**
   * Output only. The time the release was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Display version of the release. For an Android release, the
   * display version is the `versionName`. For an iOS release, the display
   * version is the `CFBundleShortVersionString`.
   *
   * @var string
   */
  public $displayVersion;
  /**
   * Output only. The time the release will expire.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Output only. A link to the Firebase console displaying a single release.
   *
   * @var string
   */
  public $firebaseConsoleUri;
  /**
   * The name of the release resource. Format:
   * `projects/{project_number}/apps/{app}/releases/{release}`
   *
   * @var string
   */
  public $name;
  protected $releaseNotesType = GoogleFirebaseAppdistroV1ReleaseNotes::class;
  protected $releaseNotesDataType = '';
  /**
   * Output only. A link to the release in the tester web clip or Android app
   * that lets testers (which were granted access to the app) view release notes
   * and install the app onto their devices.
   *
   * @var string
   */
  public $testingUri;
  /**
   * Output only. The time the release was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. A signed link (which expires in one hour) to directly download
   * the app binary (IPA/APK/AAB) file.
   *
   * @param string $binaryDownloadUri
   */
  public function setBinaryDownloadUri($binaryDownloadUri)
  {
    $this->binaryDownloadUri = $binaryDownloadUri;
  }
  /**
   * @return string
   */
  public function getBinaryDownloadUri()
  {
    return $this->binaryDownloadUri;
  }
  /**
   * Output only. Build version of the release. For an Android release, the
   * build version is the `versionCode`. For an iOS release, the build version
   * is the `CFBundleVersion`.
   *
   * @param string $buildVersion
   */
  public function setBuildVersion($buildVersion)
  {
    $this->buildVersion = $buildVersion;
  }
  /**
   * @return string
   */
  public function getBuildVersion()
  {
    return $this->buildVersion;
  }
  /**
   * Output only. The time the release was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. Display version of the release. For an Android release, the
   * display version is the `versionName`. For an iOS release, the display
   * version is the `CFBundleShortVersionString`.
   *
   * @param string $displayVersion
   */
  public function setDisplayVersion($displayVersion)
  {
    $this->displayVersion = $displayVersion;
  }
  /**
   * @return string
   */
  public function getDisplayVersion()
  {
    return $this->displayVersion;
  }
  /**
   * Output only. The time the release will expire.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. A link to the Firebase console displaying a single release.
   *
   * @param string $firebaseConsoleUri
   */
  public function setFirebaseConsoleUri($firebaseConsoleUri)
  {
    $this->firebaseConsoleUri = $firebaseConsoleUri;
  }
  /**
   * @return string
   */
  public function getFirebaseConsoleUri()
  {
    return $this->firebaseConsoleUri;
  }
  /**
   * The name of the release resource. Format:
   * `projects/{project_number}/apps/{app}/releases/{release}`
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
   * Notes of the release.
   *
   * @param GoogleFirebaseAppdistroV1ReleaseNotes $releaseNotes
   */
  public function setReleaseNotes(GoogleFirebaseAppdistroV1ReleaseNotes $releaseNotes)
  {
    $this->releaseNotes = $releaseNotes;
  }
  /**
   * @return GoogleFirebaseAppdistroV1ReleaseNotes
   */
  public function getReleaseNotes()
  {
    return $this->releaseNotes;
  }
  /**
   * Output only. A link to the release in the tester web clip or Android app
   * that lets testers (which were granted access to the app) view release notes
   * and install the app onto their devices.
   *
   * @param string $testingUri
   */
  public function setTestingUri($testingUri)
  {
    $this->testingUri = $testingUri;
  }
  /**
   * @return string
   */
  public function getTestingUri()
  {
    return $this->testingUri;
  }
  /**
   * Output only. The time the release was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppdistroV1Release::class, 'Google_Service_FirebaseAppDistribution_GoogleFirebaseAppdistroV1Release');
