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

class GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultUnstructuredDocumentInfo extends \Google\Collection
{
  protected $collection_key = 'extractiveSegments';
  /**
   * Document resource name.
   *
   * @var string
   */
  public $document;
  protected $documentContextsType = GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultUnstructuredDocumentInfoDocumentContext::class;
  protected $documentContextsDataType = 'array';
  protected $extractiveAnswersType = GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultUnstructuredDocumentInfoExtractiveAnswer::class;
  protected $extractiveAnswersDataType = 'array';
  protected $extractiveSegmentsType = GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultUnstructuredDocumentInfoExtractiveSegment::class;
  protected $extractiveSegmentsDataType = 'array';
  /**
   * Title.
   *
   * @var string
   */
  public $title;
  /**
   * URI for the document.
   *
   * @var string
   */
  public $uri;

  /**
   * Document resource name.
   *
   * @param string $document
   */
  public function setDocument($document)
  {
    $this->document = $document;
  }
  /**
   * @return string
   */
  public function getDocument()
  {
    return $this->document;
  }
  /**
   * List of document contexts. The content will be used for Answer Generation.
   * This is supposed to be the main content of the document that can be long
   * and comprehensive.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultUnstructuredDocumentInfoDocumentContext[] $documentContexts
   */
  public function setDocumentContexts($documentContexts)
  {
    $this->documentContexts = $documentContexts;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultUnstructuredDocumentInfoDocumentContext[]
   */
  public function getDocumentContexts()
  {
    return $this->documentContexts;
  }
  /**
   * Deprecated: This field is deprecated and will have no effect on the Answer
   * generation. Please use document_contexts and extractive_segments fields.
   * List of extractive answers.
   *
   * @deprecated
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultUnstructuredDocumentInfoExtractiveAnswer[] $extractiveAnswers
   */
  public function setExtractiveAnswers($extractiveAnswers)
  {
    $this->extractiveAnswers = $extractiveAnswers;
  }
  /**
   * @deprecated
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultUnstructuredDocumentInfoExtractiveAnswer[]
   */
  public function getExtractiveAnswers()
  {
    return $this->extractiveAnswers;
  }
  /**
   * List of extractive segments.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultUnstructuredDocumentInfoExtractiveSegment[] $extractiveSegments
   */
  public function setExtractiveSegments($extractiveSegments)
  {
    $this->extractiveSegments = $extractiveSegments;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultUnstructuredDocumentInfoExtractiveSegment[]
   */
  public function getExtractiveSegments()
  {
    return $this->extractiveSegments;
  }
  /**
   * Title.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * URI for the document.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultUnstructuredDocumentInfo::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AnswerQueryRequestSearchSpecSearchResultListSearchResultUnstructuredDocumentInfo');
