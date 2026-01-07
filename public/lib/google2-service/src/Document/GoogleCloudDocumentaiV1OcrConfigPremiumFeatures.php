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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1OcrConfigPremiumFeatures extends \Google\Model
{
  /**
   * Turn on font identification model and return font style information.
   *
   * @var bool
   */
  public $computeStyleInfo;
  /**
   * Turn on the model that can extract LaTeX math formulas.
   *
   * @var bool
   */
  public $enableMathOcr;
  /**
   * Turn on selection mark detector in OCR engine. Only available in OCR 2.0
   * (and later) processors.
   *
   * @var bool
   */
  public $enableSelectionMarkDetection;

  /**
   * Turn on font identification model and return font style information.
   *
   * @param bool $computeStyleInfo
   */
  public function setComputeStyleInfo($computeStyleInfo)
  {
    $this->computeStyleInfo = $computeStyleInfo;
  }
  /**
   * @return bool
   */
  public function getComputeStyleInfo()
  {
    return $this->computeStyleInfo;
  }
  /**
   * Turn on the model that can extract LaTeX math formulas.
   *
   * @param bool $enableMathOcr
   */
  public function setEnableMathOcr($enableMathOcr)
  {
    $this->enableMathOcr = $enableMathOcr;
  }
  /**
   * @return bool
   */
  public function getEnableMathOcr()
  {
    return $this->enableMathOcr;
  }
  /**
   * Turn on selection mark detector in OCR engine. Only available in OCR 2.0
   * (and later) processors.
   *
   * @param bool $enableSelectionMarkDetection
   */
  public function setEnableSelectionMarkDetection($enableSelectionMarkDetection)
  {
    $this->enableSelectionMarkDetection = $enableSelectionMarkDetection;
  }
  /**
   * @return bool
   */
  public function getEnableSelectionMarkDetection()
  {
    return $this->enableSelectionMarkDetection;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1OcrConfigPremiumFeatures::class, 'Google_Service_Document_GoogleCloudDocumentaiV1OcrConfigPremiumFeatures');
