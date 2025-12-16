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

class TestSpecification extends \Google\Model
{
  protected $androidInstrumentationTestType = AndroidInstrumentationTest::class;
  protected $androidInstrumentationTestDataType = '';
  protected $androidRoboTestType = AndroidRoboTest::class;
  protected $androidRoboTestDataType = '';
  protected $androidTestLoopType = AndroidTestLoop::class;
  protected $androidTestLoopDataType = '';
  /**
   * Disables performance metrics recording. May reduce test latency.
   *
   * @var bool
   */
  public $disablePerformanceMetrics;
  /**
   * Disables video recording. May reduce test latency.
   *
   * @var bool
   */
  public $disableVideoRecording;
  protected $iosRoboTestType = IosRoboTest::class;
  protected $iosRoboTestDataType = '';
  protected $iosTestLoopType = IosTestLoop::class;
  protected $iosTestLoopDataType = '';
  protected $iosTestSetupType = IosTestSetup::class;
  protected $iosTestSetupDataType = '';
  protected $iosXcTestType = IosXcTest::class;
  protected $iosXcTestDataType = '';
  protected $testSetupType = TestSetup::class;
  protected $testSetupDataType = '';
  /**
   * Max time a test execution is allowed to run before it is automatically
   * cancelled. The default value is 5 min.
   *
   * @var string
   */
  public $testTimeout;

  /**
   * An Android instrumentation test.
   *
   * @param AndroidInstrumentationTest $androidInstrumentationTest
   */
  public function setAndroidInstrumentationTest(AndroidInstrumentationTest $androidInstrumentationTest)
  {
    $this->androidInstrumentationTest = $androidInstrumentationTest;
  }
  /**
   * @return AndroidInstrumentationTest
   */
  public function getAndroidInstrumentationTest()
  {
    return $this->androidInstrumentationTest;
  }
  /**
   * An Android robo test.
   *
   * @param AndroidRoboTest $androidRoboTest
   */
  public function setAndroidRoboTest(AndroidRoboTest $androidRoboTest)
  {
    $this->androidRoboTest = $androidRoboTest;
  }
  /**
   * @return AndroidRoboTest
   */
  public function getAndroidRoboTest()
  {
    return $this->androidRoboTest;
  }
  /**
   * An Android Application with a Test Loop.
   *
   * @param AndroidTestLoop $androidTestLoop
   */
  public function setAndroidTestLoop(AndroidTestLoop $androidTestLoop)
  {
    $this->androidTestLoop = $androidTestLoop;
  }
  /**
   * @return AndroidTestLoop
   */
  public function getAndroidTestLoop()
  {
    return $this->androidTestLoop;
  }
  /**
   * Disables performance metrics recording. May reduce test latency.
   *
   * @param bool $disablePerformanceMetrics
   */
  public function setDisablePerformanceMetrics($disablePerformanceMetrics)
  {
    $this->disablePerformanceMetrics = $disablePerformanceMetrics;
  }
  /**
   * @return bool
   */
  public function getDisablePerformanceMetrics()
  {
    return $this->disablePerformanceMetrics;
  }
  /**
   * Disables video recording. May reduce test latency.
   *
   * @param bool $disableVideoRecording
   */
  public function setDisableVideoRecording($disableVideoRecording)
  {
    $this->disableVideoRecording = $disableVideoRecording;
  }
  /**
   * @return bool
   */
  public function getDisableVideoRecording()
  {
    return $this->disableVideoRecording;
  }
  /**
   * An iOS Robo test.
   *
   * @param IosRoboTest $iosRoboTest
   */
  public function setIosRoboTest(IosRoboTest $iosRoboTest)
  {
    $this->iosRoboTest = $iosRoboTest;
  }
  /**
   * @return IosRoboTest
   */
  public function getIosRoboTest()
  {
    return $this->iosRoboTest;
  }
  /**
   * An iOS application with a test loop.
   *
   * @param IosTestLoop $iosTestLoop
   */
  public function setIosTestLoop(IosTestLoop $iosTestLoop)
  {
    $this->iosTestLoop = $iosTestLoop;
  }
  /**
   * @return IosTestLoop
   */
  public function getIosTestLoop()
  {
    return $this->iosTestLoop;
  }
  /**
   * Test setup requirements for iOS.
   *
   * @param IosTestSetup $iosTestSetup
   */
  public function setIosTestSetup(IosTestSetup $iosTestSetup)
  {
    $this->iosTestSetup = $iosTestSetup;
  }
  /**
   * @return IosTestSetup
   */
  public function getIosTestSetup()
  {
    return $this->iosTestSetup;
  }
  /**
   * An iOS XCTest, via an .xctestrun file.
   *
   * @param IosXcTest $iosXcTest
   */
  public function setIosXcTest(IosXcTest $iosXcTest)
  {
    $this->iosXcTest = $iosXcTest;
  }
  /**
   * @return IosXcTest
   */
  public function getIosXcTest()
  {
    return $this->iosXcTest;
  }
  /**
   * Test setup requirements for Android e.g. files to install, bootstrap
   * scripts.
   *
   * @param TestSetup $testSetup
   */
  public function setTestSetup(TestSetup $testSetup)
  {
    $this->testSetup = $testSetup;
  }
  /**
   * @return TestSetup
   */
  public function getTestSetup()
  {
    return $this->testSetup;
  }
  /**
   * Max time a test execution is allowed to run before it is automatically
   * cancelled. The default value is 5 min.
   *
   * @param string $testTimeout
   */
  public function setTestTimeout($testTimeout)
  {
    $this->testTimeout = $testTimeout;
  }
  /**
   * @return string
   */
  public function getTestTimeout()
  {
    return $this->testTimeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestSpecification::class, 'Google_Service_Testing_TestSpecification');
