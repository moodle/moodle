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

class GoogleCloudDiscoveryengineV1AnswerQueryRequest extends \Google\Model
{
  protected $answerGenerationSpecType = GoogleCloudDiscoveryengineV1AnswerQueryRequestAnswerGenerationSpec::class;
  protected $answerGenerationSpecDataType = '';
  /**
   * Deprecated: This field is deprecated. Streaming Answer API will be
   * supported. Asynchronous mode control. If enabled, the response will be
   * returned with answer/session resource name without final answer. The API
   * users need to do the polling to get the latest status of answer/session by
   * calling ConversationalSearchService.GetAnswer or
   * ConversationalSearchService.GetSession method.
   *
   * @deprecated
   * @var bool
   */
  public $asynchronousMode;
  protected $endUserSpecType = GoogleCloudDiscoveryengineV1AnswerQueryRequestEndUserSpec::class;
  protected $endUserSpecDataType = '';
  protected $groundingSpecType = GoogleCloudDiscoveryengineV1AnswerQueryRequestGroundingSpec::class;
  protected $groundingSpecDataType = '';
  protected $queryType = GoogleCloudDiscoveryengineV1Query::class;
  protected $queryDataType = '';
  protected $queryUnderstandingSpecType = GoogleCloudDiscoveryengineV1AnswerQueryRequestQueryUnderstandingSpec::class;
  protected $queryUnderstandingSpecDataType = '';
  protected $relatedQuestionsSpecType = GoogleCloudDiscoveryengineV1AnswerQueryRequestRelatedQuestionsSpec::class;
  protected $relatedQuestionsSpecDataType = '';
  protected $safetySpecType = GoogleCloudDiscoveryengineV1AnswerQueryRequestSafetySpec::class;
  protected $safetySpecDataType = '';
  protected $searchSpecType = GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpec::class;
  protected $searchSpecDataType = '';
  /**
   * The session resource name. Not required. When session field is not set, the
   * API is in sessionless mode. We support auto session mode: users can use the
   * wildcard symbol `-` as session ID. A new ID will be automatically generated
   * and assigned.
   *
   * @var string
   */
  public $session;
  /**
   * The user labels applied to a resource must meet the following requirements:
   * * Each resource can have multiple labels, up to a maximum of 64. * Each
   * label must be a key-value pair. * Keys have a minimum length of 1 character
   * and a maximum length of 63 characters and cannot be empty. Values can be
   * empty and have a maximum length of 63 characters. * Keys and values can
   * contain only lowercase letters, numeric characters, underscores, and
   * dashes. All characters must use UTF-8 encoding, and international
   * characters are allowed. * The key portion of a label must be unique.
   * However, you can use the same key with multiple resources. * Keys must
   * start with a lowercase letter or international character. See [Google Cloud
   * Document](https://cloud.google.com/resource-manager/docs/creating-managing-
   * labels#requirements) for more details.
   *
   * @var string[]
   */
  public $userLabels;
  /**
   * A unique identifier for tracking visitors. For example, this could be
   * implemented with an HTTP cookie, which should be able to uniquely identify
   * a visitor on a single device. This unique identifier should not change if
   * the visitor logs in or out of the website. This field should NOT have a
   * fixed value such as `unknown_visitor`. The field must be a UTF-8 encoded
   * string with a length limit of 128 characters. Otherwise, an
   * `INVALID_ARGUMENT` error is returned.
   *
   * @var string
   */
  public $userPseudoId;

  /**
   * Answer generation specification.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestAnswerGenerationSpec $answerGenerationSpec
   */
  public function setAnswerGenerationSpec(GoogleCloudDiscoveryengineV1AnswerQueryRequestAnswerGenerationSpec $answerGenerationSpec)
  {
    $this->answerGenerationSpec = $answerGenerationSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestAnswerGenerationSpec
   */
  public function getAnswerGenerationSpec()
  {
    return $this->answerGenerationSpec;
  }
  /**
   * Deprecated: This field is deprecated. Streaming Answer API will be
   * supported. Asynchronous mode control. If enabled, the response will be
   * returned with answer/session resource name without final answer. The API
   * users need to do the polling to get the latest status of answer/session by
   * calling ConversationalSearchService.GetAnswer or
   * ConversationalSearchService.GetSession method.
   *
   * @deprecated
   * @param bool $asynchronousMode
   */
  public function setAsynchronousMode($asynchronousMode)
  {
    $this->asynchronousMode = $asynchronousMode;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getAsynchronousMode()
  {
    return $this->asynchronousMode;
  }
  /**
   * Optional. End user specification.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestEndUserSpec $endUserSpec
   */
  public function setEndUserSpec(GoogleCloudDiscoveryengineV1AnswerQueryRequestEndUserSpec $endUserSpec)
  {
    $this->endUserSpec = $endUserSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestEndUserSpec
   */
  public function getEndUserSpec()
  {
    return $this->endUserSpec;
  }
  /**
   * Optional. Grounding specification.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestGroundingSpec $groundingSpec
   */
  public function setGroundingSpec(GoogleCloudDiscoveryengineV1AnswerQueryRequestGroundingSpec $groundingSpec)
  {
    $this->groundingSpec = $groundingSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestGroundingSpec
   */
  public function getGroundingSpec()
  {
    return $this->groundingSpec;
  }
  /**
   * Required. Current user query.
   *
   * @param GoogleCloudDiscoveryengineV1Query $query
   */
  public function setQuery(GoogleCloudDiscoveryengineV1Query $query)
  {
    $this->query = $query;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1Query
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Query understanding specification.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestQueryUnderstandingSpec $queryUnderstandingSpec
   */
  public function setQueryUnderstandingSpec(GoogleCloudDiscoveryengineV1AnswerQueryRequestQueryUnderstandingSpec $queryUnderstandingSpec)
  {
    $this->queryUnderstandingSpec = $queryUnderstandingSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestQueryUnderstandingSpec
   */
  public function getQueryUnderstandingSpec()
  {
    return $this->queryUnderstandingSpec;
  }
  /**
   * Related questions specification.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestRelatedQuestionsSpec $relatedQuestionsSpec
   */
  public function setRelatedQuestionsSpec(GoogleCloudDiscoveryengineV1AnswerQueryRequestRelatedQuestionsSpec $relatedQuestionsSpec)
  {
    $this->relatedQuestionsSpec = $relatedQuestionsSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestRelatedQuestionsSpec
   */
  public function getRelatedQuestionsSpec()
  {
    return $this->relatedQuestionsSpec;
  }
  /**
   * Model specification.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestSafetySpec $safetySpec
   */
  public function setSafetySpec(GoogleCloudDiscoveryengineV1AnswerQueryRequestSafetySpec $safetySpec)
  {
    $this->safetySpec = $safetySpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestSafetySpec
   */
  public function getSafetySpec()
  {
    return $this->safetySpec;
  }
  /**
   * Search specification.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpec $searchSpec
   */
  public function setSearchSpec(GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpec $searchSpec)
  {
    $this->searchSpec = $searchSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpec
   */
  public function getSearchSpec()
  {
    return $this->searchSpec;
  }
  /**
   * The session resource name. Not required. When session field is not set, the
   * API is in sessionless mode. We support auto session mode: users can use the
   * wildcard symbol `-` as session ID. A new ID will be automatically generated
   * and assigned.
   *
   * @param string $session
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
   * The user labels applied to a resource must meet the following requirements:
   * * Each resource can have multiple labels, up to a maximum of 64. * Each
   * label must be a key-value pair. * Keys have a minimum length of 1 character
   * and a maximum length of 63 characters and cannot be empty. Values can be
   * empty and have a maximum length of 63 characters. * Keys and values can
   * contain only lowercase letters, numeric characters, underscores, and
   * dashes. All characters must use UTF-8 encoding, and international
   * characters are allowed. * The key portion of a label must be unique.
   * However, you can use the same key with multiple resources. * Keys must
   * start with a lowercase letter or international character. See [Google Cloud
   * Document](https://cloud.google.com/resource-manager/docs/creating-managing-
   * labels#requirements) for more details.
   *
   * @param string[] $userLabels
   */
  public function setUserLabels($userLabels)
  {
    $this->userLabels = $userLabels;
  }
  /**
   * @return string[]
   */
  public function getUserLabels()
  {
    return $this->userLabels;
  }
  /**
   * A unique identifier for tracking visitors. For example, this could be
   * implemented with an HTTP cookie, which should be able to uniquely identify
   * a visitor on a single device. This unique identifier should not change if
   * the visitor logs in or out of the website. This field should NOT have a
   * fixed value such as `unknown_visitor`. The field must be a UTF-8 encoded
   * string with a length limit of 128 characters. Otherwise, an
   * `INVALID_ARGUMENT` error is returned.
   *
   * @param string $userPseudoId
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
class_alias(GoogleCloudDiscoveryengineV1AnswerQueryRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AnswerQueryRequest');
