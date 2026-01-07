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

namespace Google\Service\CloudHealthcare;

class ImageConfig extends \Google\Model
{
  /**
   * No text redaction specified. Same as REDACT_NO_TEXT.
   */
  public const TEXT_REDACTION_MODE_TEXT_REDACTION_MODE_UNSPECIFIED = 'TEXT_REDACTION_MODE_UNSPECIFIED';
  /**
   * Redact all text.
   */
  public const TEXT_REDACTION_MODE_REDACT_ALL_TEXT = 'REDACT_ALL_TEXT';
  /**
   * Redact sensitive text. Uses the set of [Default DICOM
   * InfoTypes](https://cloud.google.com/healthcare-api/docs/how-tos/dicom-
   * deidentify#default_dicom_infotypes).
   */
  public const TEXT_REDACTION_MODE_REDACT_SENSITIVE_TEXT = 'REDACT_SENSITIVE_TEXT';
  /**
   * Do not redact text.
   */
  public const TEXT_REDACTION_MODE_REDACT_NO_TEXT = 'REDACT_NO_TEXT';
  /**
   * Optional. Determines how to redact text from image.
   *
   * @var string
   */
  public $textRedactionMode;

  /**
   * Optional. Determines how to redact text from image.
   *
   * Accepted values: TEXT_REDACTION_MODE_UNSPECIFIED, REDACT_ALL_TEXT,
   * REDACT_SENSITIVE_TEXT, REDACT_NO_TEXT
   *
   * @param self::TEXT_REDACTION_MODE_* $textRedactionMode
   */
  public function setTextRedactionMode($textRedactionMode)
  {
    $this->textRedactionMode = $textRedactionMode;
  }
  /**
   * @return self::TEXT_REDACTION_MODE_*
   */
  public function getTextRedactionMode()
  {
    return $this->textRedactionMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImageConfig::class, 'Google_Service_CloudHealthcare_ImageConfig');
