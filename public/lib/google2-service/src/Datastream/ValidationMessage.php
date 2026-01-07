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

namespace Google\Service\Datastream;

class ValidationMessage extends \Google\Model
{
  /**
   * Unspecified level.
   */
  public const LEVEL_LEVEL_UNSPECIFIED = 'LEVEL_UNSPECIFIED';
  /**
   * Potentially cause issues with the Stream.
   */
  public const LEVEL_WARNING = 'WARNING';
  /**
   * Definitely cause issues with the Stream.
   */
  public const LEVEL_ERROR = 'ERROR';
  /**
   * A custom code identifying this specific message.
   *
   * @var string
   */
  public $code;
  /**
   * Message severity level (warning or error).
   *
   * @var string
   */
  public $level;
  /**
   * The result of the validation.
   *
   * @var string
   */
  public $message;
  /**
   * Additional metadata related to the result.
   *
   * @var string[]
   */
  public $metadata;

  /**
   * A custom code identifying this specific message.
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
   * Message severity level (warning or error).
   *
   * Accepted values: LEVEL_UNSPECIFIED, WARNING, ERROR
   *
   * @param self::LEVEL_* $level
   */
  public function setLevel($level)
  {
    $this->level = $level;
  }
  /**
   * @return self::LEVEL_*
   */
  public function getLevel()
  {
    return $this->level;
  }
  /**
   * The result of the validation.
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
   * Additional metadata related to the result.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValidationMessage::class, 'Google_Service_Datastream_ValidationMessage');
