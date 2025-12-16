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

class XPSEvaluationMetrics extends \Google\Model
{
  /**
   * The annotation_spec for which this evaluation metrics instance had been
   * created. Empty iff this is an overall model evaluation (like Tables
   * evaluation metrics), i.e. aggregated across all labels. The value comes
   * from the input annotations in AnnotatedExample. For MVP product or for text
   * sentiment models where annotation_spec_id_token is not available, set label
   * instead.
   *
   * @var string
   */
  public $annotationSpecIdToken;
  /**
   * The integer category label for which this evaluation metric instance had
   * been created. Valid categories are 0 or higher. Overall model evaluation
   * should set this to negative values (rather than implicit zero). Only used
   * for Image Segmentation (prefer to set annotation_spec_id_token instead).
   * Note: uCAIP Image Segmentation should use annotation_spec_id_token.
   *
   * @var int
   */
  public $category;
  /**
   * The number of examples used to create this evaluation metrics instance.
   *
   * @var int
   */
  public $evaluatedExampleCount;
  protected $imageClassificationEvalMetricsType = XPSClassificationEvaluationMetrics::class;
  protected $imageClassificationEvalMetricsDataType = '';
  protected $imageObjectDetectionEvalMetricsType = XPSImageObjectDetectionEvaluationMetrics::class;
  protected $imageObjectDetectionEvalMetricsDataType = '';
  protected $imageSegmentationEvalMetricsType = XPSImageSegmentationEvaluationMetrics::class;
  protected $imageSegmentationEvalMetricsDataType = '';
  /**
   * The label for which this evaluation metrics instance had been created.
   * Empty iff this is an overall model evaluation (like Tables evaluation
   * metrics), i.e. aggregated across all labels. The label maps to
   * AnnotationSpec.display_name in Public API protos. Only used by MVP
   * implementation and text sentiment FULL implementation.
   *
   * @var string
   */
  public $label;
  protected $regressionEvalMetricsType = XPSRegressionEvaluationMetrics::class;
  protected $regressionEvalMetricsDataType = '';
  protected $tablesClassificationEvalMetricsType = XPSClassificationEvaluationMetrics::class;
  protected $tablesClassificationEvalMetricsDataType = '';
  protected $tablesEvalMetricsType = XPSTablesEvaluationMetrics::class;
  protected $tablesEvalMetricsDataType = '';
  protected $textClassificationEvalMetricsType = XPSClassificationEvaluationMetrics::class;
  protected $textClassificationEvalMetricsDataType = '';
  protected $textExtractionEvalMetricsType = XPSTextExtractionEvaluationMetrics::class;
  protected $textExtractionEvalMetricsDataType = '';
  protected $textSentimentEvalMetricsType = XPSTextSentimentEvaluationMetrics::class;
  protected $textSentimentEvalMetricsDataType = '';
  protected $translationEvalMetricsType = XPSTranslationEvaluationMetrics::class;
  protected $translationEvalMetricsDataType = '';
  protected $videoActionRecognitionEvalMetricsType = XPSVideoActionRecognitionEvaluationMetrics::class;
  protected $videoActionRecognitionEvalMetricsDataType = '';
  protected $videoClassificationEvalMetricsType = XPSClassificationEvaluationMetrics::class;
  protected $videoClassificationEvalMetricsDataType = '';
  protected $videoObjectTrackingEvalMetricsType = XPSVideoObjectTrackingEvaluationMetrics::class;
  protected $videoObjectTrackingEvalMetricsDataType = '';

  /**
   * The annotation_spec for which this evaluation metrics instance had been
   * created. Empty iff this is an overall model evaluation (like Tables
   * evaluation metrics), i.e. aggregated across all labels. The value comes
   * from the input annotations in AnnotatedExample. For MVP product or for text
   * sentiment models where annotation_spec_id_token is not available, set label
   * instead.
   *
   * @param string $annotationSpecIdToken
   */
  public function setAnnotationSpecIdToken($annotationSpecIdToken)
  {
    $this->annotationSpecIdToken = $annotationSpecIdToken;
  }
  /**
   * @return string
   */
  public function getAnnotationSpecIdToken()
  {
    return $this->annotationSpecIdToken;
  }
  /**
   * The integer category label for which this evaluation metric instance had
   * been created. Valid categories are 0 or higher. Overall model evaluation
   * should set this to negative values (rather than implicit zero). Only used
   * for Image Segmentation (prefer to set annotation_spec_id_token instead).
   * Note: uCAIP Image Segmentation should use annotation_spec_id_token.
   *
   * @param int $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return int
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * The number of examples used to create this evaluation metrics instance.
   *
   * @param int $evaluatedExampleCount
   */
  public function setEvaluatedExampleCount($evaluatedExampleCount)
  {
    $this->evaluatedExampleCount = $evaluatedExampleCount;
  }
  /**
   * @return int
   */
  public function getEvaluatedExampleCount()
  {
    return $this->evaluatedExampleCount;
  }
  /**
   * @param XPSClassificationEvaluationMetrics $imageClassificationEvalMetrics
   */
  public function setImageClassificationEvalMetrics(XPSClassificationEvaluationMetrics $imageClassificationEvalMetrics)
  {
    $this->imageClassificationEvalMetrics = $imageClassificationEvalMetrics;
  }
  /**
   * @return XPSClassificationEvaluationMetrics
   */
  public function getImageClassificationEvalMetrics()
  {
    return $this->imageClassificationEvalMetrics;
  }
  /**
   * @param XPSImageObjectDetectionEvaluationMetrics $imageObjectDetectionEvalMetrics
   */
  public function setImageObjectDetectionEvalMetrics(XPSImageObjectDetectionEvaluationMetrics $imageObjectDetectionEvalMetrics)
  {
    $this->imageObjectDetectionEvalMetrics = $imageObjectDetectionEvalMetrics;
  }
  /**
   * @return XPSImageObjectDetectionEvaluationMetrics
   */
  public function getImageObjectDetectionEvalMetrics()
  {
    return $this->imageObjectDetectionEvalMetrics;
  }
  /**
   * @param XPSImageSegmentationEvaluationMetrics $imageSegmentationEvalMetrics
   */
  public function setImageSegmentationEvalMetrics(XPSImageSegmentationEvaluationMetrics $imageSegmentationEvalMetrics)
  {
    $this->imageSegmentationEvalMetrics = $imageSegmentationEvalMetrics;
  }
  /**
   * @return XPSImageSegmentationEvaluationMetrics
   */
  public function getImageSegmentationEvalMetrics()
  {
    return $this->imageSegmentationEvalMetrics;
  }
  /**
   * The label for which this evaluation metrics instance had been created.
   * Empty iff this is an overall model evaluation (like Tables evaluation
   * metrics), i.e. aggregated across all labels. The label maps to
   * AnnotationSpec.display_name in Public API protos. Only used by MVP
   * implementation and text sentiment FULL implementation.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * @param XPSRegressionEvaluationMetrics $regressionEvalMetrics
   */
  public function setRegressionEvalMetrics(XPSRegressionEvaluationMetrics $regressionEvalMetrics)
  {
    $this->regressionEvalMetrics = $regressionEvalMetrics;
  }
  /**
   * @return XPSRegressionEvaluationMetrics
   */
  public function getRegressionEvalMetrics()
  {
    return $this->regressionEvalMetrics;
  }
  /**
   * @param XPSClassificationEvaluationMetrics $tablesClassificationEvalMetrics
   */
  public function setTablesClassificationEvalMetrics(XPSClassificationEvaluationMetrics $tablesClassificationEvalMetrics)
  {
    $this->tablesClassificationEvalMetrics = $tablesClassificationEvalMetrics;
  }
  /**
   * @return XPSClassificationEvaluationMetrics
   */
  public function getTablesClassificationEvalMetrics()
  {
    return $this->tablesClassificationEvalMetrics;
  }
  /**
   * @param XPSTablesEvaluationMetrics $tablesEvalMetrics
   */
  public function setTablesEvalMetrics(XPSTablesEvaluationMetrics $tablesEvalMetrics)
  {
    $this->tablesEvalMetrics = $tablesEvalMetrics;
  }
  /**
   * @return XPSTablesEvaluationMetrics
   */
  public function getTablesEvalMetrics()
  {
    return $this->tablesEvalMetrics;
  }
  /**
   * @param XPSClassificationEvaluationMetrics $textClassificationEvalMetrics
   */
  public function setTextClassificationEvalMetrics(XPSClassificationEvaluationMetrics $textClassificationEvalMetrics)
  {
    $this->textClassificationEvalMetrics = $textClassificationEvalMetrics;
  }
  /**
   * @return XPSClassificationEvaluationMetrics
   */
  public function getTextClassificationEvalMetrics()
  {
    return $this->textClassificationEvalMetrics;
  }
  /**
   * @param XPSTextExtractionEvaluationMetrics $textExtractionEvalMetrics
   */
  public function setTextExtractionEvalMetrics(XPSTextExtractionEvaluationMetrics $textExtractionEvalMetrics)
  {
    $this->textExtractionEvalMetrics = $textExtractionEvalMetrics;
  }
  /**
   * @return XPSTextExtractionEvaluationMetrics
   */
  public function getTextExtractionEvalMetrics()
  {
    return $this->textExtractionEvalMetrics;
  }
  /**
   * @param XPSTextSentimentEvaluationMetrics $textSentimentEvalMetrics
   */
  public function setTextSentimentEvalMetrics(XPSTextSentimentEvaluationMetrics $textSentimentEvalMetrics)
  {
    $this->textSentimentEvalMetrics = $textSentimentEvalMetrics;
  }
  /**
   * @return XPSTextSentimentEvaluationMetrics
   */
  public function getTextSentimentEvalMetrics()
  {
    return $this->textSentimentEvalMetrics;
  }
  /**
   * @param XPSTranslationEvaluationMetrics $translationEvalMetrics
   */
  public function setTranslationEvalMetrics(XPSTranslationEvaluationMetrics $translationEvalMetrics)
  {
    $this->translationEvalMetrics = $translationEvalMetrics;
  }
  /**
   * @return XPSTranslationEvaluationMetrics
   */
  public function getTranslationEvalMetrics()
  {
    return $this->translationEvalMetrics;
  }
  /**
   * @param XPSVideoActionRecognitionEvaluationMetrics $videoActionRecognitionEvalMetrics
   */
  public function setVideoActionRecognitionEvalMetrics(XPSVideoActionRecognitionEvaluationMetrics $videoActionRecognitionEvalMetrics)
  {
    $this->videoActionRecognitionEvalMetrics = $videoActionRecognitionEvalMetrics;
  }
  /**
   * @return XPSVideoActionRecognitionEvaluationMetrics
   */
  public function getVideoActionRecognitionEvalMetrics()
  {
    return $this->videoActionRecognitionEvalMetrics;
  }
  /**
   * @param XPSClassificationEvaluationMetrics $videoClassificationEvalMetrics
   */
  public function setVideoClassificationEvalMetrics(XPSClassificationEvaluationMetrics $videoClassificationEvalMetrics)
  {
    $this->videoClassificationEvalMetrics = $videoClassificationEvalMetrics;
  }
  /**
   * @return XPSClassificationEvaluationMetrics
   */
  public function getVideoClassificationEvalMetrics()
  {
    return $this->videoClassificationEvalMetrics;
  }
  /**
   * @param XPSVideoObjectTrackingEvaluationMetrics $videoObjectTrackingEvalMetrics
   */
  public function setVideoObjectTrackingEvalMetrics(XPSVideoObjectTrackingEvaluationMetrics $videoObjectTrackingEvalMetrics)
  {
    $this->videoObjectTrackingEvalMetrics = $videoObjectTrackingEvalMetrics;
  }
  /**
   * @return XPSVideoObjectTrackingEvaluationMetrics
   */
  public function getVideoObjectTrackingEvalMetrics()
  {
    return $this->videoObjectTrackingEvalMetrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSEvaluationMetrics::class, 'Google_Service_CloudNaturalLanguage_XPSEvaluationMetrics');
