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

namespace Google\Service\FirebaseHosting;

class HttpUpdate extends \Google\Model
{
  protected $checkErrorType = Status::class;
  protected $checkErrorDataType = '';
  /**
   * Output only. A text string to serve at the path.
   *
   * @var string
   */
  public $desired;
  /**
   * Output only. Whether Hosting was able to find the required file contents on
   * the specified path during its last check.
   *
   * @var string
   */
  public $discovered;
  /**
   * Output only. The last time Hosting systems checked for the file contents.
   *
   * @var string
   */
  public $lastCheckTime;
  /**
   * Output only. The path to the file.
   *
   * @var string
   */
  public $path;

  /**
   * Output only. An error encountered during the last contents check. If null,
   * the check completed successfully.
   *
   * @param Status $checkError
   */
  public function setCheckError(Status $checkError)
  {
    $this->checkError = $checkError;
  }
  /**
   * @return Status
   */
  public function getCheckError()
  {
    return $this->checkError;
  }
  /**
   * Output only. A text string to serve at the path.
   *
   * @param string $desired
   */
  public function setDesired($desired)
  {
    $this->desired = $desired;
  }
  /**
   * @return string
   */
  public function getDesired()
  {
    return $this->desired;
  }
  /**
   * Output only. Whether Hosting was able to find the required file contents on
   * the specified path during its last check.
   *
   * @param string $discovered
   */
  public function setDiscovered($discovered)
  {
    $this->discovered = $discovered;
  }
  /**
   * @return string
   */
  public function getDiscovered()
  {
    return $this->discovered;
  }
  /**
   * Output only. The last time Hosting systems checked for the file contents.
   *
   * @param string $lastCheckTime
   */
  public function setLastCheckTime($lastCheckTime)
  {
    $this->lastCheckTime = $lastCheckTime;
  }
  /**
   * @return string
   */
  public function getLastCheckTime()
  {
    return $this->lastCheckTime;
  }
  /**
   * Output only. The path to the file.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpUpdate::class, 'Google_Service_FirebaseHosting_HttpUpdate');
