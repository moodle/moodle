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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaAnswerQueryRequest extends \Google\Model
{
  protected $answerGenerationSpecType = GoogleCloudDiscoveryengineV1betaAnswerQueryRequestAnswerGenerationSpec::class;
  protected $answerGenerationSpecDataType = '';
  /**
   * @var bool
   */
  public $asynchronousMode;
  protected $queryType = GoogleCloudDiscoveryengineV1betaQuery::class;
  protected $queryDataType = '';
  protected $queryUnderstandingSpecType = GoogleCloudDiscoveryengineV1betaAnswerQueryRequestQueryUnderstandingSpec::class;
  protected $queryUnderstandingSpecDataType = '';
  protected $relatedQuestionsSpecType = GoogleCloudDiscoveryengineV1betaAnswerQueryRequestRelatedQuestionsSpec::class;
  protected $relatedQuestionsSpecDataType = '';
  protected $safetySpecType = GoogleCloudDiscoveryengineV1betaAnswerQueryRequestSafetySpec::class;
  protected $safetySpecDataType = '';
  protected $searchSpecType = GoogleCloudDiscoveryengineV1betaAnswerQueryRequestSearchSpec::class;
  protected $searchSpecDataType = '';
  /**
   * @var string
   */
  public $session;
  /**
   * @var string
   */
  public $userPseudoId;

  /**
   * @param GoogleCloudDiscoveryengineV1betaAnswerQueryRequestAnswerGenerationSpec
   */
  public function setAnswerGenerationSpec(GoogleCloudDiscoveryengineV1betaAnswerQueryRequestAnswerGenerationSpec $answerGenerationSpec)
  {
    $this->answerGenerationSpec = $answerGenerationSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaAnswerQueryRequestAnswerGenerationSpec
   */
  public function getAnswerGenerationSpec()
  {
    return $this->answerGenerationSpec;
  }
  /**
   * @param bool
   */
  public function setAsynchronousMode($asynchronousMode)
  {
    $this->asynchronousMode = $asynchronousMode;
  }
  /**
   * @return bool
   */
  public function getAsynchronousMode()
  {
    return $this->asynchronousMode;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaQuery
   */
  public function setQuery(GoogleCloudDiscoveryengineV1betaQuery $query)
  {
    $this->query = $query;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaQuery
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaAnswerQueryRequestQueryUnderstandingSpec
   */
  public function setQueryUnderstandingSpec(GoogleCloudDiscoveryengineV1betaAnswerQueryRequestQueryUnderstandingSpec $queryUnderstandingSpec)
  {
    $this->queryUnderstandingSpec = $queryUnderstandingSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaAnswerQueryRequestQueryUnderstandingSpec
   */
  public function getQueryUnderstandingSpec()
  {
    return $this->queryUnderstandingSpec;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaAnswerQueryRequestRelatedQuestionsSpec
   */
  public function setRelatedQuestionsSpec(GoogleCloudDiscoveryengineV1betaAnswerQueryRequestRelatedQuestionsSpec $relatedQuestionsSpec)
  {
    $this->relatedQuestionsSpec = $relatedQuestionsSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaAnswerQueryRequestRelatedQuestionsSpec
   */
  public function getRelatedQuestionsSpec()
  {
    return $this->relatedQuestionsSpec;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaAnswerQueryRequestSafetySpec
   */
  public function setSafetySpec(GoogleCloudDiscoveryengineV1betaAnswerQueryRequestSafetySpec $safetySpec)
  {
    $this->safetySpec = $safetySpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaAnswerQueryRequestSafetySpec
   */
  public function getSafetySpec()
  {
    return $this->safetySpec;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaAnswerQueryRequestSearchSpec
   */
  public function setSearchSpec(GoogleCloudDiscoveryengineV1betaAnswerQueryRequestSearchSpec $searchSpec)
  {
    $this->searchSpec = $searchSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaAnswerQueryRequestSearchSpec
   */
  public function getSearchSpec()
  {
    return $this->searchSpec;
  }
  /**
   * @param string
   */
  public function setSession($session)
  {
    $this->session = $session;
  }
  /**
   * @return string
   */
  public function getSession()
  {
    return $this->session;
  }
  /**
   * @param string
   */
  public function setUserPseudoId($userPseudoId)
  {
    $this->userPseudoId = $userPseudoId;
  }
  /**
   * @return string
   */
  public function getUserPseudoId()
  {
    return $this->userPseudoId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaAnswerQueryRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaAnswerQueryRequest');
