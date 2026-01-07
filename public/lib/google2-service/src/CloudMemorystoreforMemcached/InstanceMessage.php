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

namespace Google\Service\CloudMemorystoreforMemcached;

class InstanceMessage extends \Google\Model
{
  /**
   * Message Code not set.
   */
  public const CODE_CODE_UNSPECIFIED = 'CODE_UNSPECIFIED';
  /**
   * Memcached nodes are distributed unevenly.
   */
  public const CODE_ZONE_DISTRIBUTION_UNBALANCED = 'ZONE_DISTRIBUTION_UNBALANCED';
  /**
   * A code that correspond to one type of user-facing message.
   *
   * @var string
   */
  public $code;
  /**
   * Message on memcached instance which will be exposed to users.
   *
   * @var string
   */
  public $message;

  /**
   * A code that correspond to one type of user-facing message.
   *
   * Accepted values: CODE_UNSPECIFIED, ZONE_DISTRIBUTION_UNBALANCED
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Message on memcached instance which will be exposed to users.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceMessage::class, 'Google_Service_CloudMemorystoreforMemcached_InstanceMessage');
