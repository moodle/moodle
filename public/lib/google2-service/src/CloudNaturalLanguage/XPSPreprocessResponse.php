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

class XPSPreprocessResponse extends \Google\Model
{
  protected $outputExampleSetType = XPSExampleSet::class;
  protected $outputExampleSetDataType = '';
  protected $speechPreprocessRespType = XPSSpeechPreprocessResponse::class;
  protected $speechPreprocessRespDataType = '';
  protected $tablesPreprocessResponseType = XPSTablesPreprocessResponse::class;
  protected $tablesPreprocessResponseDataType = '';
  protected $translationPreprocessRespType = XPSTranslationPreprocessResponse::class;
  protected $translationPreprocessRespDataType = '';

  /**
   * Preprocessed examples, that are to be imported into AutoML storage. This
   * should point to RecordIO file(s) of PreprocessedExample messages. The
   * PreprocessedExample.mvp_training_data-s returned here are later verbatim
   * passed to Train() call in TrainExample.mvp_training_data.
   *
   * @param XPSExampleSet $outputExampleSet
   */
  public function setOutputExampleSet(XPSExampleSet $outputExampleSet)
  {
    $this->outputExampleSet = $outputExampleSet;
  }
  /**
   * @return XPSExampleSet
   */
  public function getOutputExampleSet()
  {
    return $this->outputExampleSet;
  }
  /**
   * @param XPSSpeechPreprocessResponse $speechPreprocessResp
   */
  public function setSpeechPreprocessResp(XPSSpeechPreprocessResponse $speechPreprocessResp)
  {
    $this->speechPreprocessResp = $speechPreprocessResp;
  }
  /**
   * @return XPSSpeechPreprocessResponse
   */
  public function getSpeechPreprocessResp()
  {
    return $this->speechPreprocessResp;
  }
  /**
   * @param XPSTablesPreprocessResponse $tablesPreprocessResponse
   */
  public function setTablesPreprocessResponse(XPSTablesPreprocessResponse $tablesPreprocessResponse)
  {
    $this->tablesPreprocessResponse = $tablesPreprocessResponse;
  }
  /**
   * @return XPSTablesPreprocessResponse
   */
  public function getTablesPreprocessResponse()
  {
    return $this->tablesPreprocessResponse;
  }
  /**
   * @param XPSTranslationPreprocessResponse $translationPreprocessResp
   */
  public function setTranslationPreprocessResp(XPSTranslationPreprocessResponse $translationPreprocessResp)
  {
    $this->translationPreprocessResp = $translationPreprocessResp;
  }
  /**
   * @return XPSTranslationPreprocessResponse
   */
  public function getTranslationPreprocessResp()
  {
    return $this->translationPreprocessResp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSPreprocessResponse::class, 'Google_Service_CloudNaturalLanguage_XPSPreprocessResponse');
