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

class AndroidTestLoop extends \Google\Collection
{
  protected $collection_key = 'scenarios';
  protected $appApkType = FileReference::class;
  protected $appApkDataType = '';
  protected $appBundleType = AppBundle::class;
  protected $appBundleDataType = '';
  /**
   * The java package for the application under test. The default is determined
   * by examining the application's manifest.
   *
   * @var string
   */
  public $appPackageId;
  /**
   * The list of scenario labels that should be run during the test. The
   * scenario labels should map to labels defined in the application's manifest.
   * For example, player_experience and com.google.test.loops.player_experience
   * add all of the loops labeled in the manifest with the
   * com.google.test.loops.player_experience name to the execution. Scenarios
   * can also be specified in the scenarios field.
   *
   * @var string[]
   */
  public $scenarioLabels;
  /**
   * The list of scenarios that should be run during the test. The default is
   * all test loops, derived from the application's manifest.
   *
   * @var int[]
   */
  public $scenarios;

  /**
   * The APK for the application under test.
   *
   * @param FileReference $appApk
   */
  public function setAppApk(FileReference $appApk)
  {
    $this->appApk = $appApk;
  }
  /**
   * @return FileReference
   */
  public function getAppApk()
  {
    return $this->appApk;
  }
  /**
   * A multi-apk app bundle for the application under test.
   *
   * @param AppBundle $appBundle
   */
  public function setAppBundle(AppBundle $appBundle)
  {
    $this->appBundle = $appBundle;
  }
  /**
   * @return AppBundle
   */
  public function getAppBundle()
  {
    return $this->appBundle;
  }
  /**
   * The java package for the application under test. The default is determined
   * by examining the application's manifest.
   *
   * @param string $appPackageId
   */
  public function setAppPackageId($appPackageId)
  {
    $this->appPackageId = $appPackageId;
  }
  /**
   * @return string
   */
  public function getAppPackageId()
  {
    return $this->appPackageId;
  }
  /**
   * The list of scenario labels that should be run during the test. The
   * scenario labels should map to labels defined in the application's manifest.
   * For example, player_experience and com.google.test.loops.player_experience
   * add all of the loops labeled in the manifest with the
   * com.google.test.loops.player_experience name to the execution. Scenarios
   * can also be specified in the scenarios field.
   *
   * @param string[] $scenarioLabels
   */
  public function setScenarioLabels($scenarioLabels)
  {
    $this->scenarioLabels = $scenarioLabels;
  }
  /**
   * @return string[]
   */
  public function getScenarioLabels()
  {
    return $this->scenarioLabels;
  }
  /**
   * The list of scenarios that should be run during the test. The default is
   * all test loops, derived from the application's manifest.
   *
   * @param int[] $scenarios
   */
  public function setScenarios($scenarios)
  {
    $this->scenarios = $scenarios;
  }
  /**
   * @return int[]
   */
  public function getScenarios()
  {
    return $this->scenarios;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AndroidTestLoop::class, 'Google_Service_Testing_AndroidTestLoop');
