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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2RedactImageResponse extends \Google\Model
{
  /**
   * If an image was being inspected and the InspectConfig's include_quote was
   * set to true, then this field will include all text, if any, that was found
   * in the image.
   *
   * @var string
   */
  public $extractedText;
  protected $inspectResultType = GooglePrivacyDlpV2InspectResult::class;
  protected $inspectResultDataType = '';
  /**
   * The redacted image. The type will be the same as the original image.
   *
   * @var string
   */
  public $redactedImage;

  /**
   * If an image was being inspected and the InspectConfig's include_quote was
   * set to true, then this field will include all text, if any, that was found
   * in the image.
   *
   * @param string $extractedText
   */
  public function setExtractedText($extractedText)
  {
    $this->extractedText = $extractedText;
  }
  /**
   * @return string
   */
  public function getExtractedText()
  {
    return $this->extractedText;
  }
  /**
   * The findings. Populated when include_findings in the request is true.
   *
   * @param GooglePrivacyDlpV2InspectResult $inspectResult
   */
  public function setInspectResult(GooglePrivacyDlpV2InspectResult $inspectResult)
  {
    $this->inspectResult = $inspectResult;
  }
  /**
   * @return GooglePrivacyDlpV2InspectResult
   */
  public function getInspectResult()
  {
    return $this->inspectResult;
  }
  /**
   * The redacted image. The type will be the same as the original image.
   *
   * @param string $redactedImage
   */
  public function setRedactedImage($redactedImage)
  {
    $this->redactedImage = $redactedImage;
  }
  /**
   * @return string
   */
  public function getRedactedImage()
  {
    return $this->redactedImage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2RedactImageResponse::class, 'Google_Service_DLP_GooglePrivacyDlpV2RedactImageResponse');
