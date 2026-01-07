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

class Validation extends \Google\Collection
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Validation did not execute.
   */
  public const STATE_NOT_EXECUTED = 'NOT_EXECUTED';
  /**
   * Validation failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Validation passed.
   */
  public const STATE_PASSED = 'PASSED';
  /**
   * Validation executed with warnings.
   */
  public const STATE_WARNING = 'WARNING';
  protected $collection_key = 'message';
  /**
   * A custom code identifying this validation.
   *
   * @var string
   */
  public $code;
  /**
   * A short description of the validation.
   *
   * @var string
   */
  public $description;
  protected $messageType = ValidationMessage::class;
  protected $messageDataType = 'array';
  /**
   * Output only. Validation execution status.
   *
   * @var string
   */
  public $state;

  /**
   * A custom code identifying this validation.
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
   * A short description of the validation.
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
   * Messages reflecting the validation results.
   *
   * @param ValidationMessage[] $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return ValidationMessage[]
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Output only. Validation execution status.
   *
   * Accepted values: STATE_UNSPECIFIED, NOT_EXECUTED, FAILED, PASSED, WARNING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Validation::class, 'Google_Service_Datastream_Validation');
