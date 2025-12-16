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

namespace Google\Service\Testing;

class TestDetails extends \Google\Collection
{
  protected $collection_key = 'progressMessages';
  /**
   * Output only. If the TestState is ERROR, then this string will contain
   * human-readable details about the error.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * Output only. Human-readable, detailed descriptions of the test's progress.
   * For example: "Provisioning a device", "Starting Test". During the course of
   * execution new data may be appended to the end of progress_messages.
   *
   * @var string[]
   */
  public $progressMessages;

  /**
   * Output only. If the TestState is ERROR, then this string will contain
   * human-readable details about the error.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * Output only. Human-readable, detailed descriptions of the test's progress.
   * For example: "Provisioning a device", "Starting Test". During the course of
   * execution new data may be appended to the end of progress_messages.
   *
   * @param string[] $progressMessages
   */
  public function setProgressMessages($progressMessages)
  {
    $this->progressMessages = $progressMessages;
  }
  /**
   * @return string[]
   */
  public function getProgressMessages()
  {
    return $this->progressMessages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestDetails::class, 'Google_Service_Testing_TestDetails');
