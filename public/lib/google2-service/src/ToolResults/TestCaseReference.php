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

class TestCaseReference extends \Google\Model
{
  /**
   * The name of the class.
   *
   * @var string
   */
  public $className;
  /**
   * The name of the test case. Required.
   *
   * @var string
   */
  public $name;
  /**
   * The name of the test suite to which this test case belongs.
   *
   * @var string
   */
  public $testSuiteName;

  /**
   * The name of the class.
   *
   * @param string $className
   */
  public function setClassName($className)
  {
    $this->className = $className;
  }
  /**
   * @return string
   */
  public function getClassName()
  {
    return $this->className;
  }
  /**
   * The name of the test case. Required.
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
   * The name of the test suite to which this test case belongs.
   *
   * @param string $testSuiteName
   */
  public function setTestSuiteName($testSuiteName)
  {
    $this->testSuiteName = $testSuiteName;
  }
  /**
   * @return string
   */
  public function getTestSuiteName()
  {
    return $this->testSuiteName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestCaseReference::class, 'Google_Service_ToolResults_TestCaseReference');
