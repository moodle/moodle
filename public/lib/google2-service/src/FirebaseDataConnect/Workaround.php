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

namespace Google\Service\FirebaseDataConnect;

class Workaround extends \Google\Model
{
  /**
   * Description of this workaround.
   *
   * @var string
   */
  public $description;
  /**
   * Why would this workaround address the error and warning.
   *
   * @var string
   */
  public $reason;
  /**
   * A suggested code snippet to fix the error and warning.
   *
   * @var string
   */
  public $replace;

  /**
   * Description of this workaround.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Why would this workaround address the error and warning.
   *
   * @param string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return string
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * A suggested code snippet to fix the error and warning.
   *
   * @param string $replace
   */
  public function setReplace($replace)
  {
    $this->replace = $replace;
  }
  /**
   * @return string
   */
  public function getReplace()
  {
    return $this->replace;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Workaround::class, 'Google_Service_FirebaseDataConnect_Workaround');
