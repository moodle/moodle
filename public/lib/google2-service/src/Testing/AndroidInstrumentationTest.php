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

class AndroidInstrumentationTest extends \Google\Collection
{
  /**
   * Default value: the server will choose the mode. Currently implies that the
   * test will run without the orchestrator. In the future, all instrumentation
   * tests will be run with the orchestrator. Using the orchestrator is highly
   * encouraged because of all the benefits it offers.
   */
  public const ORCHESTRATOR_OPTION_ORCHESTRATOR_OPTION_UNSPECIFIED = 'ORCHESTRATOR_OPTION_UNSPECIFIED';
  /**
   * Run test using orchestrator. ** Only compatible with AndroidJUnitRunner
   * version 1.1 or higher! ** Recommended.
   */
  public const ORCHESTRATOR_OPTION_USE_ORCHESTRATOR = 'USE_ORCHESTRATOR';
  /**
   * Run test without using orchestrator.
   */
  public const ORCHESTRATOR_OPTION_DO_NOT_USE_ORCHESTRATOR = 'DO_NOT_USE_ORCHESTRATOR';
  protected $collection_key = 'testTargets';
  protected $appApkType = FileReference::class;
  protected $appApkDataType = '';
  protected $appBundleType = AppBundle::class;
  protected $appBundleDataType = '';
  /**
   * The java package for the application under test. The default value is
   * determined by examining the application's manifest.
   *
   * @var string
   */
  public $appPackageId;
  /**
   * The option of whether running each test within its own invocation of
   * instrumentation with Android Test Orchestrator or not. ** Orchestrator is
   * only compatible with AndroidJUnitRunner version 1.1 or higher! **
   * Orchestrator offers the following benefits: - No shared state - Crashes are
   * isolated - Logs are scoped per test See for more information about Android
   * Test Orchestrator. If not set, the test will be run without the
   * orchestrator.
   *
   * @var string
   */
  public $orchestratorOption;
  protected $shardingOptionType = ShardingOption::class;
  protected $shardingOptionDataType = '';
  protected $testApkType = FileReference::class;
  protected $testApkDataType = '';
  /**
   * The java package for the test to be executed. The default value is
   * determined by examining the application's manifest.
   *
   * @var string
   */
  public $testPackageId;
  /**
   * The InstrumentationTestRunner class. The default value is determined by
   * examining the application's manifest.
   *
   * @var string
   */
  public $testRunnerClass;
  /**
   * Each target must be fully qualified with the package name or class name, in
   * one of these formats: - "package package_name" - "class
   * package_name.class_name" - "class package_name.class_name#method_name" If
   * empty, all targets in the module will be run.
   *
   * @var string[]
   */
  public $testTargets;

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
   * The option of whether running each test within its own invocation of
   * instrumentation with Android Test Orchestrator or not. ** Orchestrator is
   * only compatible with AndroidJUnitRunner version 1.1 or higher! **
   * Orchestrator offers the following benefits: - No shared state - Crashes are
   * isolated - Logs are scoped per test See for more information about Android
   * Test Orchestrator. If not set, the test will be run without the
   * orchestrator.
   *
   * Accepted values: ORCHESTRATOR_OPTION_UNSPECIFIED, USE_ORCHESTRATOR,
   * DO_NOT_USE_ORCHESTRATOR
   *
   * @param self::ORCHESTRATOR_OPTION_* $orchestratorOption
   */
  public function setOrchestratorOption($orchestratorOption)
  {
    $this->orchestratorOption = $orchestratorOption;
  }
  /**
   * @return self::ORCHESTRATOR_OPTION_*
   */
  public function getOrchestratorOption()
  {
    return $this->orchestratorOption;
  }
  /**
   * The option to run tests in multiple shards in parallel.
   *
   * @param ShardingOption $shardingOption
   */
  public function setShardingOption(ShardingOption $shardingOption)
  {
    $this->shardingOption = $shardingOption;
  }
  /**
   * @return ShardingOption
   */
  public function getShardingOption()
  {
    return $this->shardingOption;
  }
  /**
   * Required. The APK containing the test code to be executed.
   *
   * @param FileReference $testApk
   */
  public function setTestApk(FileReference $testApk)
  {
    $this->testApk = $testApk;
  }
  /**
   * @return FileReference
   */
  public function getTestApk()
  {
    return $this->testApk;
  }
  /**
   * The java package for the test to be executed. The default value is
   * determined by examining the application's manifest.
   *
   * @param string $testPackageId
   */
  public function setTestPackageId($testPackageId)
  {
    $this->testPackageId = $testPackageId;
  }
  /**
   * @return string
   */
  public function getTestPackageId()
  {
    return $this->testPackageId;
  }
  /**
   * The InstrumentationTestRunner class. The default value is determined by
   * examining the application's manifest.
   *
   * @param string $testRunnerClass
   */
  public function setTestRunnerClass($testRunnerClass)
  {
    $this->testRunnerClass = $testRunnerClass;
  }
  /**
   * @return string
   */
  public function getTestRunnerClass()
  {
    return $this->testRunnerClass;
  }
  /**
   * Each target must be fully qualified with the package name or class name, in
   * one of these formats: - "package package_name" - "class
   * package_name.class_name" - "class package_name.class_name#method_name" If
   * empty, all targets in the module will be run.
   *
   * @param string[] $testTargets
   */
  public function setTestTargets($testTargets)
  {
    $this->testTargets = $testTargets;
  }
  /**
   * @return string[]
   */
  public function getTestTargets()
  {
    return $this->testTargets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AndroidInstrumentationTest::class, 'Google_Service_Testing_AndroidInstrumentationTest');
