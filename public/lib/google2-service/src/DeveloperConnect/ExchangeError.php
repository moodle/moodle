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

namespace Google\Service\DeveloperConnect;

class ExchangeError extends \Google\Model
{
  /**
   * https://datatracker.ietf.org/doc/html/rfc6749#section-5.2 - error
   *
   * @var string
   */
  public $code;
  /**
   * https://datatracker.ietf.org/doc/html/rfc6749#section-5.2 -
   * error_description
   *
   * @var string
   */
  public $description;

  /**
   * https://datatracker.ietf.org/doc/html/rfc6749#section-5.2 - error
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * https://datatracker.ietf.org/doc/html/rfc6749#section-5.2 -
   * error_description
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExchangeError::class, 'Google_Service_DeveloperConnect_ExchangeError');
