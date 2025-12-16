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

namespace Google\Service\ToolResults;

class AndroidInstrumentationTest extends \Google\Collection
{
  protected $collection_key = 'testTargets';
  /**
   * The java package for the test to be executed. Required
   *
   * @var string
   */
  public $testPackageId;
  /**
   * The InstrumentationTestRunner class. Required
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
   * The flag indicates whether Android Test Orchestrator will be used to run
   * test or not.
   *
   * @var bool
   */
  public $useOrchestrator;

  /**
   * The java package for the test to be executed. Required
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
   * The InstrumentationTestRunner class. Required
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
  /**
   * The flag indicates whether Android Test Orchestrator will be used to run
   * test or not.
   *
   * @param bool $useOrchestrator
   */
  public function setUseOrchestrator($useOrchestrator)
  {
    $this->useOrchestrator = $useOrchestrator;
  }
  /**
   * @return bool
   */
  public function getUseOrchestrator()
  {
    return $this->useOrchestrator;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AndroidInstrumentationTest::class, 'Google_Service_ToolResults_AndroidInstrumentationTest');
