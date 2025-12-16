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

class XPSTrainResponse extends \Google\Collection
{
  protected $collection_key = 'explanationConfigs';
  /**
   * Estimated model size in bytes once deployed.
   *
   * @var string
   */
  public $deployedModelSizeBytes;
  protected $errorAnalysisConfigsType = XPSVisionErrorAnalysisConfig::class;
  protected $errorAnalysisConfigsDataType = 'array';
  protected $evaluatedExampleSetType = XPSExampleSet::class;
  protected $evaluatedExampleSetDataType = '';
  protected $evaluationMetricsSetType = XPSEvaluationMetricsSet::class;
  protected $evaluationMetricsSetDataType = '';
  protected $explanationConfigsType = XPSResponseExplanationSpec::class;
  protected $explanationConfigsDataType = 'array';
  protected $imageClassificationTrainRespType = XPSImageClassificationTrainResponse::class;
  protected $imageClassificationTrainRespDataType = '';
  protected $imageObjectDetectionTrainRespType = XPSImageObjectDetectionModelSpec::class;
  protected $imageObjectDetectionTrainRespDataType = '';
  protected $imageSegmentationTrainRespType = XPSImageSegmentationTrainResponse::class;
  protected $imageSegmentationTrainRespDataType = '';
  /**
   * Token that represents the trained model. This is considered immutable and
   * is persisted in AutoML. xPS can put their own proto in the byte string, to
   * e.g. point to the model checkpoints. The token is passed to other xPS APIs
   * to refer to the model.
   *
   * @var string
   */
  public $modelToken;
  protected $speechTrainRespType = XPSSpeechModelSpec::class;
  protected $speechTrainRespDataType = '';
  protected $tablesTrainRespType = XPSTablesTrainResponse::class;
  protected $tablesTrainRespDataType = '';
  protected $textToSpeechTrainRespType = XPSTextToSpeechTrainResponse::class;
  protected $textToSpeechTrainRespDataType = '';
  protected $textTrainRespType = XPSTextTrainResponse::class;
  protected $textTrainRespDataType = '';
  protected $translationTrainRespType = XPSTranslationTrainResponse::class;
  protected $translationTrainRespDataType = '';
  protected $videoActionRecognitionTrainRespType = XPSVideoActionRecognitionTrainResponse::class;
  protected $videoActionRecognitionTrainRespDataType = '';
  protected $videoClassificationTrainRespType = XPSVideoClassificationTrainResponse::class;
  protected $videoClassificationTrainRespDataType = '';
  protected $videoObjectTrackingTrainRespType = XPSVideoObjectTrackingTrainResponse::class;
  protected $videoObjectTrackingTrainRespDataType = '';

  /**
   * Estimated model size in bytes once deployed.
   *
   * @param string $deployedModelSizeBytes
   */
  public function setDeployedModelSizeBytes($deployedModelSizeBytes)
  {
    $this->deployedModelSizeBytes = $deployedModelSizeBytes;
  }
  /**
   * @return string
   */
  public function getDeployedModelSizeBytes()
  {
    return $this->deployedModelSizeBytes;
  }
  /**
   * Optional vision model error analysis configuration. The field is set when
   * model error analysis is enabled in the training request. The results of
   * error analysis will be binded together with evaluation results (in the
   * format of AnnotatedExample).
   *
   * @param XPSVisionErrorAnalysisConfig[] $errorAnalysisConfigs
   */
  public function setErrorAnalysisConfigs($errorAnalysisConfigs)
  {
    $this->errorAnalysisConfigs = $errorAnalysisConfigs;
  }
  /**
   * @return XPSVisionErrorAnalysisConfig[]
   */
  public function getErrorAnalysisConfigs()
  {
    return $this->errorAnalysisConfigs;
  }
  /**
   * Examples used to evaluate the model (usually the test set), with the
   * predicted annotations. The file_spec should point to recordio file(s) of
   * AnnotatedExample. For each returned example, the example_id_token and
   * annotations predicted by the model must be set. The example payload can and
   * is recommended to be omitted.
   *
   * @param XPSExampleSet $evaluatedExampleSet
   */
  public function setEvaluatedExampleSet(XPSExampleSet $evaluatedExampleSet)
  {
    $this->evaluatedExampleSet = $evaluatedExampleSet;
  }
  /**
   * @return XPSExampleSet
   */
  public function getEvaluatedExampleSet()
  {
    return $this->evaluatedExampleSet;
  }
  /**
   * The trained model evaluation metrics. This can be optionally returned.
   *
   * @param XPSEvaluationMetricsSet $evaluationMetricsSet
   */
  public function setEvaluationMetricsSet(XPSEvaluationMetricsSet $evaluationMetricsSet)
  {
    $this->evaluationMetricsSet = $evaluationMetricsSet;
  }
  /**
   * @return XPSEvaluationMetricsSet
   */
  public function getEvaluationMetricsSet()
  {
    return $this->evaluationMetricsSet;
  }
  /**
   * VisionExplanationConfig for XAI on test set. Optional for when XAI is
   * enable in training request.
   *
   * @deprecated
   * @param XPSResponseExplanationSpec[] $explanationConfigs
   */
  public function setExplanationConfigs($explanationConfigs)
  {
    $this->explanationConfigs = $explanationConfigs;
  }
  /**
   * @deprecated
   * @return XPSResponseExplanationSpec[]
   */
  public function getExplanationConfigs()
  {
    return $this->explanationConfigs;
  }
  /**
   * @param XPSImageClassificationTrainResponse $imageClassificationTrainResp
   */
  public function setImageClassificationTrainResp(XPSImageClassificationTrainResponse $imageClassificationTrainResp)
  {
    $this->imageClassificationTrainResp = $imageClassificationTrainResp;
  }
  /**
   * @return XPSImageClassificationTrainResponse
   */
  public function getImageClassificationTrainResp()
  {
    return $this->imageClassificationTrainResp;
  }
  /**
   * @param XPSImageObjectDetectionModelSpec $imageObjectDetectionTrainResp
   */
  public function setImageObjectDetectionTrainResp(XPSImageObjectDetectionModelSpec $imageObjectDetectionTrainResp)
  {
    $this->imageObjectDetectionTrainResp = $imageObjectDetectionTrainResp;
  }
  /**
   * @return XPSImageObjectDetectionModelSpec
   */
  public function getImageObjectDetectionTrainResp()
  {
    return $this->imageObjectDetectionTrainResp;
  }
  /**
   * @param XPSImageSegmentationTrainResponse $imageSegmentationTrainResp
   */
  public function setImageSegmentationTrainResp(XPSImageSegmentationTrainResponse $imageSegmentationTrainResp)
  {
    $this->imageSegmentationTrainResp = $imageSegmentationTrainResp;
  }
  /**
   * @return XPSImageSegmentationTrainResponse
   */
  public function getImageSegmentationTrainResp()
  {
    return $this->imageSegmentationTrainResp;
  }
  /**
   * Token that represents the trained model. This is considered immutable and
   * is persisted in AutoML. xPS can put their own proto in the byte string, to
   * e.g. point to the model checkpoints. The token is passed to other xPS APIs
   * to refer to the model.
   *
   * @param string $modelToken
   */
  public function setModelToken($modelToken)
  {
    $this->modelToken = $modelToken;
  }
  /**
   * @return string
   */
  public function getModelToken()
  {
    return $this->modelToken;
  }
  /**
   * @param XPSSpeechModelSpec $speechTrainResp
   */
  public function setSpeechTrainResp(XPSSpeechModelSpec $speechTrainResp)
  {
    $this->speechTrainResp = $speechTrainResp;
  }
  /**
   * @return XPSSpeechModelSpec
   */
  public function getSpeechTrainResp()
  {
    return $this->speechTrainResp;
  }
  /**
   * @param XPSTablesTrainResponse $tablesTrainResp
   */
  public function setTablesTrainResp(XPSTablesTrainResponse $tablesTrainResp)
  {
    $this->tablesTrainResp = $tablesTrainResp;
  }
  /**
   * @return XPSTablesTrainResponse
   */
  public function getTablesTrainResp()
  {
    return $this->tablesTrainResp;
  }
  /**
   * @param XPSTextToSpeechTrainResponse $textToSpeechTrainResp
   */
  public function setTextToSpeechTrainResp(XPSTextToSpeechTrainResponse $textToSpeechTrainResp)
  {
    $this->textToSpeechTrainResp = $textToSpeechTrainResp;
  }
  /**
   * @return XPSTextToSpeechTrainResponse
   */
  public function getTextToSpeechTrainResp()
  {
    return $this->textToSpeechTrainResp;
  }
  /**
   * Will only be needed for uCAIP from Beta.
   *
   * @param XPSTextTrainResponse $textTrainResp
   */
  public function setTextTrainResp(XPSTextTrainResponse $textTrainResp)
  {
    $this->textTrainResp = $textTrainResp;
  }
  /**
   * @return XPSTextTrainResponse
   */
  public function getTextTrainResp()
  {
    return $this->textTrainResp;
  }
  /**
   * @param XPSTranslationTrainResponse $translationTrainResp
   */
  public function setTranslationTrainResp(XPSTranslationTrainResponse $translationTrainResp)
  {
    $this->translationTrainResp = $translationTrainResp;
  }
  /**
   * @return XPSTranslationTrainResponse
   */
  public function getTranslationTrainResp()
  {
    return $this->translationTrainResp;
  }
  /**
   * @param XPSVideoActionRecognitionTrainResponse $videoActionRecognitionTrainResp
   */
  public function setVideoActionRecognitionTrainResp(XPSVideoActionRecognitionTrainResponse $videoActionRecognitionTrainResp)
  {
    $this->videoActionRecognitionTrainResp = $videoActionRecognitionTrainResp;
  }
  /**
   * @return XPSVideoActionRecognitionTrainResponse
   */
  public function getVideoActionRecognitionTrainResp()
  {
    return $this->videoActionRecognitionTrainResp;
  }
  /**
   * @param XPSVideoClassificationTrainResponse $videoClassificationTrainResp
   */
  public function setVideoClassificationTrainResp(XPSVideoClassificationTrainResponse $videoClassificationTrainResp)
  {
    $this->videoClassificationTrainResp = $videoClassificationTrainResp;
  }
  /**
   * @return XPSVideoClassificationTrainResponse
   */
  public function getVideoClassificationTrainResp()
  {
    return $this->videoClassificationTrainResp;
  }
  /**
   * @param XPSVideoObjectTrackingTrainResponse $videoObjectTrackingTrainResp
   */
  public function setVideoObjectTrackingTrainResp(XPSVideoObjectTrackingTrainResponse $videoObjectTrackingTrainResp)
  {
    $this->videoObjectTrackingTrainResp = $videoObjectTrackingTrainResp;
  }
  /**
   * @return XPSVideoObjectTrackingTrainResponse
   */
  public function getVideoObjectTrackingTrainResp()
  {
    return $this->videoObjectTrackingTrainResp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSTrainResponse::class, 'Google_Service_CloudNaturalLanguage_XPSTrainResponse');
