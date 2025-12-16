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

namespace Google\Service\AndroidPublisher;

class AppEdit extends \Google\Model
{
  /**
   * Output only. The time (as seconds since Epoch) at which the edit will
   * expire and will be no longer valid for use.
   *
   * @var string
   */
  public $expiryTimeSeconds;
  /**
   * Output only. Identifier of the edit. Can be used in subsequent API calls.
   *
   * @var string
   */
  public $id;

  /**
   * Output only. The time (as seconds since Epoch) at which the edit will
   * expire and will be no longer valid for use.
   *
   * @param string $expiryTimeSeconds
   */
  public function setExpiryTimeSeconds($expiryTimeSeconds)
  {
    $this->expiryTimeSeconds = $expiryTimeSeconds;
  }
  /**
   * @return string
   */
  public function getExpiryTimeSeconds()
  {
    return $this->expiryTimeSeconds;
  }
  /**
   * Output only. Identifier of the edit. Can be used in subsequent API calls.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppEdit::class, 'Google_Service_AndroidPublisher_AppEdit');
