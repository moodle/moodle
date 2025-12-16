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

namespace Google\Service\Dataproc;

class SparkRuntimeInfo extends \Google\Model
{
  /**
   * @var string
   */
  public $javaHome;
  /**
   * @var string
   */
  public $javaVersion;
  /**
   * @var string
   */
  public $scalaVersion;

  /**
   * @param string $javaHome
   */
  public function setJavaHome($javaHome)
  {
    $this->javaHome = $javaHome;
  }
  /**
   * @return string
   */
  public function getJavaHome()
  {
    return $this->javaHome;
  }
  /**
   * @param string $javaVersion
   */
  public function setJavaVersion($javaVersion)
  {
    $this->javaVersion = $javaVersion;
  }
  /**
   * @return string
   */
  public function getJavaVersion()
  {
    return $this->javaVersion;
  }
  /**
   * @param string $scalaVersion
   */
  public function setScalaVersion($scalaVersion)
  {
    $this->scalaVersion = $scalaVersion;
  }
  /**
   * @return string
   */
  public function getScalaVersion()
  {
    return $this->scalaVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SparkRuntimeInfo::class, 'Google_Service_Dataproc_SparkRuntimeInfo');
