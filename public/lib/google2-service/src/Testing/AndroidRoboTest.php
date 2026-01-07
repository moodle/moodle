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

class AndroidRoboTest extends \Google\Collection
{
  /**
   * This means that the server should choose the mode. Recommended.
   */
  public const ROBO_MODE_ROBO_MODE_UNSPECIFIED = 'ROBO_MODE_UNSPECIFIED';
  /**
   * Runs Robo in UIAutomator-only mode without app resigning
   */
  public const ROBO_MODE_ROBO_VERSION_1 = 'ROBO_VERSION_1';
  /**
   * Deprecated: Use ROBO_VERSION_1 instead for all use cases. Runs Robo in
   * standard Espresso with UIAutomator fallback
   *
   * @deprecated
   */
  public const ROBO_MODE_ROBO_VERSION_2 = 'ROBO_VERSION_2';
  protected $collection_key = 'startingIntents';
  protected $appApkType = FileReference::class;
  protected $appApkDataType = '';
  protected $appBundleType = AppBundle::class;
  protected $appBundleDataType = '';
  /**
   * The initial activity that should be used to start the app.
   *
   * @var string
   */
  public $appInitialActivity;
  /**
   * The java package for the application under test. The default value is
   * determined by examining the application's manifest.
   *
   * @var string
   */
  public $appPackageId;
  /**
   * The max depth of the traversal stack Robo can explore. Needs to be at least
   * 2 to make Robo explore the app beyond the first activity. Default is 50.
   *
   * @deprecated
   * @var int
   */
  public $maxDepth;
  /**
   * The max number of steps Robo can execute. Default is no limit.
   *
   * @deprecated
   * @var int
   */
  public $maxSteps;
  protected $roboDirectivesType = RoboDirective::class;
  protected $roboDirectivesDataType = 'array';
  /**
   * The mode in which Robo should run. Most clients should allow the server to
   * populate this field automatically.
   *
   * @var string
   */
  public $roboMode;
  protected $roboScriptType = FileReference::class;
  protected $roboScriptDataType = '';
  protected $startingIntentsType = RoboStartingIntent::class;
  protected $startingIntentsDataType = 'array';

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
   * The initial activity that should be used to start the app.
   *
   * @param string $appInitialActivity
   */
  public function setAppInitialActivity($appInitialActivity)
  {
    $this->appInitialActivity = $appInitialActivity;
  }
  /**
   * @return string
   */
  public function getAppInitialActivity()
  {
    return $this->appInitialActivity;
  }
  /**
   * The java package for the application under test. The default value is
   * determined by examining the application's manifest.
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
   * The max depth of the traversal stack Robo can explore. Needs to be at least
   * 2 to make Robo explore the app beyond the first activity. Default is 50.
   *
   * @deprecated
   * @param int $maxDepth
   */
  public function setMaxDepth($maxDepth)
  {
    $this->maxDepth = $maxDepth;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getMaxDepth()
  {
    return $this->maxDepth;
  }
  /**
   * The max number of steps Robo can execute. Default is no limit.
   *
   * @deprecated
   * @param int $maxSteps
   */
  public function setMaxSteps($maxSteps)
  {
    $this->maxSteps = $maxSteps;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getMaxSteps()
  {
    return $this->maxSteps;
  }
  /**
   * A set of directives Robo should apply during the crawl. This allows users
   * to customize the crawl. For example, the username and password for a test
   * account can be provided.
   *
   * @param RoboDirective[] $roboDirectives
   */
  public function setRoboDirectives($roboDirectives)
  {
    $this->roboDirectives = $roboDirectives;
  }
  /**
   * @return RoboDirective[]
   */
  public function getRoboDirectives()
  {
    return $this->roboDirectives;
  }
  /**
   * The mode in which Robo should run. Most clients should allow the server to
   * populate this field automatically.
   *
   * Accepted values: ROBO_MODE_UNSPECIFIED, ROBO_VERSION_1, ROBO_VERSION_2
   *
   * @param self::ROBO_MODE_* $roboMode
   */
  public function setRoboMode($roboMode)
  {
    $this->roboMode = $roboMode;
  }
  /**
   * @return self::ROBO_MODE_*
   */
  public function getRoboMode()
  {
    return $this->roboMode;
  }
  /**
   * A JSON file with a sequence of actions Robo should perform as a prologue
   * for the crawl.
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
  /**
   * The intents used to launch the app for the crawl. If none are provided,
   * then the main launcher activity is launched. If some are provided, then
   * only those provided are launched (the main launcher activity must be
   * provided explicitly).
   *
   * @param RoboStartingIntent[] $startingIntents
   */
  public function setStartingIntents($startingIntents)
  {
    $this->startingIntents = $startingIntents;
  }
  /**
   * @return RoboStartingIntent[]
   */
  public function getStartingIntents()
  {
    return $this->startingIntents;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AndroidRoboTest::class, 'Google_Service_Testing_AndroidRoboTest');
