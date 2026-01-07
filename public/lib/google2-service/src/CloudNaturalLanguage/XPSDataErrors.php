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

namespace Google\Service\CloudNaturalLanguage;

class XPSDataErrors extends \Google\Model
{
  /**
   * Not specified.
   */
  public const ERROR_TYPE_ERROR_TYPE_UNSPECIFIED = 'ERROR_TYPE_UNSPECIFIED';
  /**
   * Audio format not in the formats by cloud-speech AutoML. Currently only wav
   * and flac file formats are supported.
   */
  public const ERROR_TYPE_UNSUPPORTED_AUDIO_FORMAT = 'UNSUPPORTED_AUDIO_FORMAT';
  /**
   * File format differnt from what is specified in the file name extension.
   */
  public const ERROR_TYPE_FILE_EXTENSION_MISMATCH_WITH_AUDIO_FORMAT = 'FILE_EXTENSION_MISMATCH_WITH_AUDIO_FORMAT';
  /**
   * File too large. Maximum allowed size is 50 MB.
   */
  public const ERROR_TYPE_FILE_TOO_LARGE = 'FILE_TOO_LARGE';
  /**
   * Transcript is missing.
   */
  public const ERROR_TYPE_MISSING_TRANSCRIPTION = 'MISSING_TRANSCRIPTION';
  /**
   * Number of records having errors associated with the enum.
   *
   * @var int
   */
  public $count;
  /**
   * Type of the error.
   *
   * @var string
   */
  public $errorType;

  /**
   * Number of records having errors associated with the enum.
   *
   * @param int $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return int
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Type of the error.
   *
   * Accepted values: ERROR_TYPE_UNSPECIFIED, UNSUPPORTED_AUDIO_FORMAT,
   * FILE_EXTENSION_MISMATCH_WITH_AUDIO_FORMAT, FILE_TOO_LARGE,
   * MISSING_TRANSCRIPTION
   *
   * @param self::ERROR_TYPE_* $errorType
   */
  public function setErrorType($errorType)
  {
    $this->errorType = $errorType;
  }
  /**
   * @return self::ERROR_TYPE_*
   */
  public function getErrorType()
  {
    return $this->errorType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSDataErrors::class, 'Google_Service_CloudNaturalLanguage_XPSDataErrors');
