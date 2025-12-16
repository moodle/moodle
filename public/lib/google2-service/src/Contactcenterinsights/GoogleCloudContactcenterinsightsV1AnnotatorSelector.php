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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1AnnotatorSelector extends \Google\Collection
{
  protected $collection_key = 'phraseMatchers';
  /**
   * The issue model to run. If not provided, the most recently deployed topic
   * model will be used. The provided issue model will only be used for
   * inference if the issue model is deployed and if run_issue_model_annotator
   * is set to true. If more than one issue model is provided, only the first
   * provided issue model will be used for inference.
   *
   * @var string[]
   */
  public $issueModels;
  /**
   * The list of phrase matchers to run. If not provided, all active phrase
   * matchers will be used. If inactive phrase matchers are provided, they will
   * not be used. Phrase matchers will be run only if
   * run_phrase_matcher_annotator is set to true. Format:
   * projects/{project}/locations/{location}/phraseMatchers/{phrase_matcher}
   *
   * @var string[]
   */
  public $phraseMatchers;
  protected $qaConfigType = GoogleCloudContactcenterinsightsV1AnnotatorSelectorQaConfig::class;
  protected $qaConfigDataType = '';
  /**
   * Whether to run the entity annotator.
   *
   * @var bool
   */
  public $runEntityAnnotator;
  /**
   * Whether to run the intent annotator.
   *
   * @var bool
   */
  public $runIntentAnnotator;
  /**
   * Whether to run the interruption annotator.
   *
   * @var bool
   */
  public $runInterruptionAnnotator;
  /**
   * Whether to run the issue model annotator. A model should have already been
   * deployed for this to take effect.
   *
   * @var bool
   */
  public $runIssueModelAnnotator;
  /**
   * Whether to run the active phrase matcher annotator(s).
   *
   * @var bool
   */
  public $runPhraseMatcherAnnotator;
  /**
   * Whether to run the QA annotator.
   *
   * @var bool
   */
  public $runQaAnnotator;
  /**
   * Whether to run the sentiment annotator.
   *
   * @var bool
   */
  public $runSentimentAnnotator;
  /**
   * Whether to run the silence annotator.
   *
   * @var bool
   */
  public $runSilenceAnnotator;
  /**
   * Whether to run the summarization annotator.
   *
   * @var bool
   */
  public $runSummarizationAnnotator;
  protected $summarizationConfigType = GoogleCloudContactcenterinsightsV1AnnotatorSelectorSummarizationConfig::class;
  protected $summarizationConfigDataType = '';

  /**
   * The issue model to run. If not provided, the most recently deployed topic
   * model will be used. The provided issue model will only be used for
   * inference if the issue model is deployed and if run_issue_model_annotator
   * is set to true. If more than one issue model is provided, only the first
   * provided issue model will be used for inference.
   *
   * @param string[] $issueModels
   */
  public function setIssueModels($issueModels)
  {
    $this->issueModels = $issueModels;
  }
  /**
   * @return string[]
   */
  public function getIssueModels()
  {
    return $this->issueModels;
  }
  /**
   * The list of phrase matchers to run. If not provided, all active phrase
   * matchers will be used. If inactive phrase matchers are provided, they will
   * not be used. Phrase matchers will be run only if
   * run_phrase_matcher_annotator is set to true. Format:
   * projects/{project}/locations/{location}/phraseMatchers/{phrase_matcher}
   *
   * @param string[] $phraseMatchers
   */
  public function setPhraseMatchers($phraseMatchers)
  {
    $this->phraseMatchers = $phraseMatchers;
  }
  /**
   * @return string[]
   */
  public function getPhraseMatchers()
  {
    return $this->phraseMatchers;
  }
  /**
   * Configuration for the QA annotator.
   *
   * @param GoogleCloudContactcenterinsightsV1AnnotatorSelectorQaConfig $qaConfig
   */
  public function setQaConfig(GoogleCloudContactcenterinsightsV1AnnotatorSelectorQaConfig $qaConfig)
  {
    $this->qaConfig = $qaConfig;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AnnotatorSelectorQaConfig
   */
  public function getQaConfig()
  {
    return $this->qaConfig;
  }
  /**
   * Whether to run the entity annotator.
   *
   * @param bool $runEntityAnnotator
   */
  public function setRunEntityAnnotator($runEntityAnnotator)
  {
    $this->runEntityAnnotator = $runEntityAnnotator;
  }
  /**
   * @return bool
   */
  public function getRunEntityAnnotator()
  {
    return $this->runEntityAnnotator;
  }
  /**
   * Whether to run the intent annotator.
   *
   * @param bool $runIntentAnnotator
   */
  public function setRunIntentAnnotator($runIntentAnnotator)
  {
    $this->runIntentAnnotator = $runIntentAnnotator;
  }
  /**
   * @return bool
   */
  public function getRunIntentAnnotator()
  {
    return $this->runIntentAnnotator;
  }
  /**
   * Whether to run the interruption annotator.
   *
   * @param bool $runInterruptionAnnotator
   */
  public function setRunInterruptionAnnotator($runInterruptionAnnotator)
  {
    $this->runInterruptionAnnotator = $runInterruptionAnnotator;
  }
  /**
   * @return bool
   */
  public function getRunInterruptionAnnotator()
  {
    return $this->runInterruptionAnnotator;
  }
  /**
   * Whether to run the issue model annotator. A model should have already been
   * deployed for this to take effect.
   *
   * @param bool $runIssueModelAnnotator
   */
  public function setRunIssueModelAnnotator($runIssueModelAnnotator)
  {
    $this->runIssueModelAnnotator = $runIssueModelAnnotator;
  }
  /**
   * @return bool
   */
  public function getRunIssueModelAnnotator()
  {
    return $this->runIssueModelAnnotator;
  }
  /**
   * Whether to run the active phrase matcher annotator(s).
   *
   * @param bool $runPhraseMatcherAnnotator
   */
  public function setRunPhraseMatcherAnnotator($runPhraseMatcherAnnotator)
  {
    $this->runPhraseMatcherAnnotator = $runPhraseMatcherAnnotator;
  }
  /**
   * @return bool
   */
  public function getRunPhraseMatcherAnnotator()
  {
    return $this->runPhraseMatcherAnnotator;
  }
  /**
   * Whether to run the QA annotator.
   *
   * @param bool $runQaAnnotator
   */
  public function setRunQaAnnotator($runQaAnnotator)
  {
    $this->runQaAnnotator = $runQaAnnotator;
  }
  /**
   * @return bool
   */
  public function getRunQaAnnotator()
  {
    return $this->runQaAnnotator;
  }
  /**
   * Whether to run the sentiment annotator.
   *
   * @param bool $runSentimentAnnotator
   */
  public function setRunSentimentAnnotator($runSentimentAnnotator)
  {
    $this->runSentimentAnnotator = $runSentimentAnnotator;
  }
  /**
   * @return bool
   */
  public function getRunSentimentAnnotator()
  {
    return $this->runSentimentAnnotator;
  }
  /**
   * Whether to run the silence annotator.
   *
   * @param bool $runSilenceAnnotator
   */
  public function setRunSilenceAnnotator($runSilenceAnnotator)
  {
    $this->runSilenceAnnotator = $runSilenceAnnotator;
  }
  /**
   * @return bool
   */
  public function getRunSilenceAnnotator()
  {
    return $this->runSilenceAnnotator;
  }
  /**
   * Whether to run the summarization annotator.
   *
   * @param bool $runSummarizationAnnotator
   */
  public function setRunSummarizationAnnotator($runSummarizationAnnotator)
  {
    $this->runSummarizationAnnotator = $runSummarizationAnnotator;
  }
  /**
   * @return bool
   */
  public function getRunSummarizationAnnotator()
  {
    return $this->runSummarizationAnnotator;
  }
  /**
   * Configuration for the summarization annotator.
   *
   * @param GoogleCloudContactcenterinsightsV1AnnotatorSelectorSummarizationConfig $summarizationConfig
   */
  public function setSummarizationConfig(GoogleCloudContactcenterinsightsV1AnnotatorSelectorSummarizationConfig $summarizationConfig)
  {
    $this->summarizationConfig = $summarizationConfig;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AnnotatorSelectorSummarizationConfig
   */
  public function getSummarizationConfig()
  {
    return $this->summarizationConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1AnnotatorSelector::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1AnnotatorSelector');
