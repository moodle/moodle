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

namespace Google\Service\ServiceManagement;

class Diagnostic extends \Google\Model
{
  /**
   * Warnings and errors
   */
  public const KIND_WARNING = 'WARNING';
  /**
   * Only errors
   */
  public const KIND_ERROR = 'ERROR';
  /**
   * The kind of diagnostic information provided.
   *
   * @var string
   */
  public $kind;
  /**
   * File name and line number of the error or warning.
   *
   * @var string
   */
  public $location;
  /**
   * Message describing the error or warning.
   *
   * @var string
   */
  public $message;

  /**
   * The kind of diagnostic information provided.
   *
   * Accepted values: WARNING, ERROR
   *
   * @param self::KIND_* $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return self::KIND_*
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * File name and line number of the error or warning.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Message describing the error or warning.
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
class_alias(Diagnostic::class, 'Google_Service_ServiceManagement_Diagnostic');
