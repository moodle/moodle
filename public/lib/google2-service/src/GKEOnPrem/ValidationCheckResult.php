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

namespace Google\Service\GKEOnPrem;

class ValidationCheckResult extends \Google\Model
{
  /**
   * The default value. The check result is unknown.
   */
  public const STATE_STATE_UNKNOWN = 'STATE_UNKNOWN';
  /**
   * The check failed.
   */
  public const STATE_STATE_FAILURE = 'STATE_FAILURE';
  /**
   * The check was skipped.
   */
  public const STATE_STATE_SKIPPED = 'STATE_SKIPPED';
  /**
   * The check itself failed to complete.
   */
  public const STATE_STATE_FATAL = 'STATE_FATAL';
  /**
   * The check encountered a warning.
   */
  public const STATE_STATE_WARNING = 'STATE_WARNING';
  /**
   * The category of the validation.
   *
   * @var string
   */
  public $category;
  /**
   * The description of the validation check.
   *
   * @var string
   */
  public $description;
  /**
   * Detailed failure information, which might be unformatted.
   *
   * @var string
   */
  public $details;
  /**
   * A human-readable message of the check failure.
   *
   * @var string
   */
  public $reason;
  /**
   * The validation check state.
   *
   * @var string
   */
  public $state;

  /**
   * The category of the validation.
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * The description of the validation check.
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
   * Detailed failure information, which might be unformatted.
   *
   * @param string $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * A human-readable message of the check failure.
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
   * The validation check state.
   *
   * Accepted values: STATE_UNKNOWN, STATE_FAILURE, STATE_SKIPPED, STATE_FATAL,
   * STATE_WARNING
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
class_alias(ValidationCheckResult::class, 'Google_Service_GKEOnPrem_ValidationCheckResult');
