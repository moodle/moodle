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

class GoogleCloudDocumentaiV1OcrConfig extends \Google\Collection
{
  protected $collection_key = 'advancedOcrOptions';
  /**
   * A list of advanced OCR options to further fine-tune OCR behavior. Current
   * valid values are: - `legacy_layout`: a heuristics layout detection
   * algorithm, which serves as an alternative to the current ML-based layout
   * detection algorithm. Customers can choose the best suitable layout
   * algorithm based on their situation.
   *
   * @var string[]
   */
  public $advancedOcrOptions;
  /**
   * Turn on font identification model and return font style information.
   * Deprecated, use PremiumFeatures.compute_style_info instead.
   *
   * @deprecated
   * @var bool
   */
  public $computeStyleInfo;
  /**
   * Turn off character box detector in OCR engine. Character box detection is
   * enabled by default in OCR 2.0 (and later) processors.
   *
   * @var bool
   */
  public $disableCharacterBoxesDetection;
  /**
   * Enables intelligent document quality scores after OCR. Can help with
   * diagnosing why OCR responses are of poor quality for a given input. Adds
   * additional latency comparable to regular OCR to the process call.
   *
   * @var bool
   */
  public $enableImageQualityScores;
  /**
   * Enables special handling for PDFs with existing text information. Results
   * in better text extraction quality in such PDF inputs.
   *
   * @var bool
   */
  public $enableNativePdfParsing;
  /**
   * Includes symbol level OCR information if set to true.
   *
   * @var bool
   */
  public $enableSymbol;
  protected $hintsType = GoogleCloudDocumentaiV1OcrConfigHints::class;
  protected $hintsDataType = '';
  protected $premiumFeaturesType = GoogleCloudDocumentaiV1OcrConfigPremiumFeatures::class;
  protected $premiumFeaturesDataType = '';

  /**
   * A list of advanced OCR options to further fine-tune OCR behavior. Current
   * valid values are: - `legacy_layout`: a heuristics layout detection
   * algorithm, which serves as an alternative to the current ML-based layout
   * detection algorithm. Customers can choose the best suitable layout
   * algorithm based on their situation.
   *
   * @param string[] $advancedOcrOptions
   */
  public function setAdvancedOcrOptions($advancedOcrOptions)
  {
    $this->advancedOcrOptions = $advancedOcrOptions;
  }
  /**
   * @return string[]
   */
  public function getAdvancedOcrOptions()
  {
    return $this->advancedOcrOptions;
  }
  /**
   * Turn on font identification model and return font style information.
   * Deprecated, use PremiumFeatures.compute_style_info instead.
   *
   * @deprecated
   * @param bool $computeStyleInfo
   */
  public function setComputeStyleInfo($computeStyleInfo)
  {
    $this->computeStyleInfo = $computeStyleInfo;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getComputeStyleInfo()
  {
    return $this->computeStyleInfo;
  }
  /**
   * Turn off character box detector in OCR engine. Character box detection is
   * enabled by default in OCR 2.0 (and later) processors.
   *
   * @param bool $disableCharacterBoxesDetection
   */
  public function setDisableCharacterBoxesDetection($disableCharacterBoxesDetection)
  {
    $this->disableCharacterBoxesDetection = $disableCharacterBoxesDetection;
  }
  /**
   * @return bool
   */
  public function getDisableCharacterBoxesDetection()
  {
    return $this->disableCharacterBoxesDetection;
  }
  /**
   * Enables intelligent document quality scores after OCR. Can help with
   * diagnosing why OCR responses are of poor quality for a given input. Adds
   * additional latency comparable to regular OCR to the process call.
   *
   * @param bool $enableImageQualityScores
   */
  public function setEnableImageQualityScores($enableImageQualityScores)
  {
    $this->enableImageQualityScores = $enableImageQualityScores;
  }
  /**
   * @return bool
   */
  public function getEnableImageQualityScores()
  {
    return $this->enableImageQualityScores;
  }
  /**
   * Enables special handling for PDFs with existing text information. Results
   * in better text extraction quality in such PDF inputs.
   *
   * @param bool $enableNativePdfParsing
   */
  public function setEnableNativePdfParsing($enableNativePdfParsing)
  {
    $this->enableNativePdfParsing = $enableNativePdfParsing;
  }
  /**
   * @return bool
   */
  public function getEnableNativePdfParsing()
  {
    return $this->enableNativePdfParsing;
  }
  /**
   * Includes symbol level OCR information if set to true.
   *
   * @param bool $enableSymbol
   */
  public function setEnableSymbol($enableSymbol)
  {
    $this->enableSymbol = $enableSymbol;
  }
  /**
   * @return bool
   */
  public function getEnableSymbol()
  {
    return $this->enableSymbol;
  }
  /**
   * Hints for the OCR model.
   *
   * @param GoogleCloudDocumentaiV1OcrConfigHints $hints
   */
  public function setHints(GoogleCloudDocumentaiV1OcrConfigHints $hints)
  {
    $this->hints = $hints;
  }
  /**
   * @return GoogleCloudDocumentaiV1OcrConfigHints
   */
  public function getHints()
  {
    return $this->hints;
  }
  /**
   * Configurations for premium OCR features.
   *
   * @param GoogleCloudDocumentaiV1OcrConfigPremiumFeatures $premiumFeatures
   */
  public function setPremiumFeatures(GoogleCloudDocumentaiV1OcrConfigPremiumFeatures $premiumFeatures)
  {
    $this->premiumFeatures = $premiumFeatures;
  }
  /**
   * @return GoogleCloudDocumentaiV1OcrConfigPremiumFeatures
   */
  public function getPremiumFeatures()
  {
    return $this->premiumFeatures;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1OcrConfig::class, 'Google_Service_Document_GoogleCloudDocumentaiV1OcrConfig');
