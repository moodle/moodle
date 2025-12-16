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

namespace Google\Service\CloudTalentSolution;

class ProcessingOptions extends \Google\Model
{
  /**
   * Default value.
   */
  public const HTML_SANITIZATION_HTML_SANITIZATION_UNSPECIFIED = 'HTML_SANITIZATION_UNSPECIFIED';
  /**
   * Disables sanitization on HTML input.
   */
  public const HTML_SANITIZATION_HTML_SANITIZATION_DISABLED = 'HTML_SANITIZATION_DISABLED';
  /**
   * Sanitizes HTML input, only accepts bold, italic, ordered list, and
   * unordered list markup tags.
   */
  public const HTML_SANITIZATION_SIMPLE_FORMATTING_ONLY = 'SIMPLE_FORMATTING_ONLY';
  /**
   * If set to `true`, the service does not attempt to resolve a more precise
   * address for the job.
   *
   * @var bool
   */
  public $disableStreetAddressResolution;
  /**
   * Option for job HTML content sanitization. Applied fields are: * description
   * * applicationInfo.instruction * incentives * qualifications *
   * responsibilities HTML tags in these fields may be stripped if sanitiazation
   * isn't disabled. Defaults to HtmlSanitization.SIMPLE_FORMATTING_ONLY.
   *
   * @var string
   */
  public $htmlSanitization;

  /**
   * If set to `true`, the service does not attempt to resolve a more precise
   * address for the job.
   *
   * @param bool $disableStreetAddressResolution
   */
  public function setDisableStreetAddressResolution($disableStreetAddressResolution)
  {
    $this->disableStreetAddressResolution = $disableStreetAddressResolution;
  }
  /**
   * @return bool
   */
  public function getDisableStreetAddressResolution()
  {
    return $this->disableStreetAddressResolution;
  }
  /**
   * Option for job HTML content sanitization. Applied fields are: * description
   * * applicationInfo.instruction * incentives * qualifications *
   * responsibilities HTML tags in these fields may be stripped if sanitiazation
   * isn't disabled. Defaults to HtmlSanitization.SIMPLE_FORMATTING_ONLY.
   *
   * Accepted values: HTML_SANITIZATION_UNSPECIFIED, HTML_SANITIZATION_DISABLED,
   * SIMPLE_FORMATTING_ONLY
   *
   * @param self::HTML_SANITIZATION_* $htmlSanitization
   */
  public function setHtmlSanitization($htmlSanitization)
  {
    $this->htmlSanitization = $htmlSanitization;
  }
  /**
   * @return self::HTML_SANITIZATION_*
   */
  public function getHtmlSanitization()
  {
    return $this->htmlSanitization;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProcessingOptions::class, 'Google_Service_CloudTalentSolution_ProcessingOptions');
