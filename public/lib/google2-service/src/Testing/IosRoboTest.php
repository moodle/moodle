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

namespace Google\Service\Testing;

class IosRoboTest extends \Google\Model
{
  /**
   * The bundle ID for the app-under-test. This is determined by examining the
   * application's "Info.plist" file.
   *
   * @var string
   */
  public $appBundleId;
  protected $appIpaType = FileReference::class;
  protected $appIpaDataType = '';
  protected $roboScriptType = FileReference::class;
  protected $roboScriptDataType = '';

  /**
   * The bundle ID for the app-under-test. This is determined by examining the
   * application's "Info.plist" file.
   *
   * @param string $appBundleId
   */
  public function setAppBundleId($appBundleId)
  {
    $this->appBundleId = $appBundleId;
  }
  /**
   * @return string
   */
  public function getAppBundleId()
  {
    return $this->appBundleId;
  }
  /**
   * Required. The ipa stored at this file should be used to run the test.
   *
   * @param FileReference $appIpa
   */
  public function setAppIpa(FileReference $appIpa)
  {
    $this->appIpa = $appIpa;
  }
  /**
   * @return FileReference
   */
  public function getAppIpa()
  {
    return $this->appIpa;
  }
  /**
   * An optional Roboscript to customize the crawl. See
   * https://firebase.google.com/docs/test-lab/android/robo-scripts-reference
   * for more information about Roboscripts. The maximum allowed file size of
   * the roboscript is 10MiB.
   *
   * @param FileReference $roboScript
   */
  public function setRoboScript(FileReference $roboScript)
  {
    $this->roboScript = $roboScript;
  }
  /**
   * @return FileReference
   */
  public function getRoboScript()
  {
    return $this->roboScript;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IosRoboTest::class, 'Google_Service_Testing_IosRoboTest');
