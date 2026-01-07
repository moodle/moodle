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

namespace Google\Service\CloudNaturalLanguage\Resource;

use Google\Service\CloudNaturalLanguage\AnalyzeEntitiesRequest;
use Google\Service\CloudNaturalLanguage\AnalyzeEntitiesResponse;
use Google\Service\CloudNaturalLanguage\AnalyzeSentimentRequest;
use Google\Service\CloudNaturalLanguage\AnalyzeSentimentResponse;
use Google\Service\CloudNaturalLanguage\AnnotateTextRequest;
use Google\Service\CloudNaturalLanguage\AnnotateTextResponse;
use Google\Service\CloudNaturalLanguage\ClassifyTextRequest;
use Google\Service\CloudNaturalLanguage\ClassifyTextResponse;
use Google\Service\CloudNaturalLanguage\ModerateTextRequest;
use Google\Service\CloudNaturalLanguage\ModerateTextResponse;

/**
 * The "documents" collection of methods.
 * Typical usage is:
 *  <code>
 *   $languageService = new Google\Service\CloudNaturalLanguage(...);
 *   $documents = $languageService->documents;
 *  </code>
 */
class Documents extends \Google\Service\Resource
{
  /**
   * Finds named entities (currently proper names and common nouns) in the text
   * along with entity types, probability, mentions for each entity, and other
   * properties. (documents.analyzeEntities)
   *
   * @param AnalyzeEntitiesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return AnalyzeEntitiesResponse
   * @throws \Google\Service\Exception
   */
  public function analyzeEntities(AnalyzeEntitiesRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('analyzeEntities', [$params], AnalyzeEntitiesResponse::class);
  }
  /**
   * Analyzes the sentiment of the provided text. (documents.analyzeSentiment)
   *
   * @param AnalyzeSentimentRequest $postBody
   * @param array $optParams Optional parameters.
   * @return AnalyzeSentimentResponse
   * @throws \Google\Service\Exception
   */
  public function analyzeSentiment(AnalyzeSentimentRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('analyzeSentiment', [$params], AnalyzeSentimentResponse::class);
  }
  /**
   * A convenience method that provides all features in one call.
   * (documents.annotateText)
   *
   * @param AnnotateTextRequest $postBody
   * @param array $optParams Optional parameters.
   * @return AnnotateTextResponse
   * @throws \Google\Service\Exception
   */
  public function annotateText(AnnotateTextRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('annotateText', [$params], AnnotateTextResponse::class);
  }
  /**
   * Classifies a document into categories. (documents.classifyText)
   *
   * @param ClassifyTextRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ClassifyTextResponse
   * @throws \Google\Service\Exception
   */
  public function classifyText(ClassifyTextRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('classifyText', [$params], ClassifyTextResponse::class);
  }
  /**
   * Moderates a document for harmful and sensitive categories.
   * (documents.moderateText)
   *
   * @param ModerateTextRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ModerateTextResponse
   * @throws \Google\Service\Exception
   */
  public function moderateText(ModerateTextRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('moderateText', [$params], ModerateTextResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Documents::class, 'Google_Service_CloudNaturalLanguage_Resource_Documents');
