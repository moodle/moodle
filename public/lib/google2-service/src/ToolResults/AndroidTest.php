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

class AndroidTest extends \Google\Model
{
  protected $androidAppInfoType = AndroidAppInfo::class;
  protected $androidAppInfoDataType = '';
  protected $androidInstrumentationTestType = AndroidInstrumentationTest::class;
  protected $androidInstrumentationTestDataType = '';
  protected $androidRoboTestType = AndroidRoboTest::class;
  protected $androidRoboTestDataType = '';
  protected $androidTestLoopType = AndroidTestLoop::class;
  protected $androidTestLoopDataType = '';
  protected $testTimeoutType = Duration::class;
  protected $testTimeoutDataType = '';

  /**
   * Information about the application under test.
   *
   * @param AndroidAppInfo $androidAppInfo
   */
  public function setAndroidAppInfo(AndroidAppInfo $androidAppInfo)
  {
    $this->androidAppInfo = $androidAppInfo;
  }
  /**
   * @return AndroidAppInfo
   */
  public function getAndroidAppInfo()
  {
    return $this->androidAppInfo;
  }
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
   * An Android test loop.
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
   * Max time a test is allowed to run before it is automatically cancelled.
   *
   * @param Duration $testTimeout
   */
  public function setTestTimeout(Duration $testTimeout)
  {
    $this->testTimeout = $testTimeout;
  }
  /**
   * @return Duration
   */
  public function getTestTimeout()
  {
    return $this->testTimeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AndroidTest::class, 'Google_Service_ToolResults_AndroidTest');
