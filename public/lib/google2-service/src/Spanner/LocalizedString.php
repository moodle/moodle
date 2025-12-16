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

namespace Google\Service\Spanner;

class LocalizedString extends \Google\Model
{
  /**
   * A map of arguments used when creating the localized message. Keys represent
   * parameter names which may be used by the localized version when
   * substituting dynamic values.
   *
   * @var string[]
   */
  public $args;
  /**
   * The canonical English version of this message. If no token is provided or
   * the front-end has no message associated with the token, this text will be
   * displayed as-is.
   *
   * @var string
   */
  public $message;
  /**
   * The token identifying the message, e.g. 'METRIC_READ_CPU'. This should be
   * unique within the service.
   *
   * @var string
   */
  public $token;

  /**
   * A map of arguments used when creating the localized message. Keys represent
   * parameter names which may be used by the localized version when
   * substituting dynamic values.
   *
   * @param string[] $args
   */
  public function setArgs($args)
  {
    $this->args = $args;
  }
  /**
   * @return string[]
   */
  public function getArgs()
  {
    return $this->args;
  }
  /**
   * The canonical English version of this message. If no token is provided or
   * the front-end has no message associated with the token, this text will be
   * displayed as-is.
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
  /**
   * The token identifying the message, e.g. 'METRIC_READ_CPU'. This should be
   * unique within the service.
   *
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }
  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LocalizedString::class, 'Google_Service_Spanner_LocalizedString');
