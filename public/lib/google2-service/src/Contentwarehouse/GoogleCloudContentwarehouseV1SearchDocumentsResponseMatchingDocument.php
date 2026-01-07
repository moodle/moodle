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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1SearchDocumentsResponseMatchingDocument extends \Google\Collection
{
  protected $collection_key = 'matchedTokenPageIndices';
  protected $documentType = GoogleCloudContentwarehouseV1Document::class;
  protected $documentDataType = '';
  /**
   * Return the 1-based page indices where those pages have one or more matched
   * tokens.
   *
   * @var string[]
   */
  public $matchedTokenPageIndices;
  protected $qaResultType = GoogleCloudContentwarehouseV1QAResult::class;
  protected $qaResultDataType = '';
  /**
   * Contains snippets of text from the document full raw text that most closely
   * match a search query's keywords, if available. All HTML tags in the
   * original fields are stripped when returned in this field, and matching
   * query keywords are enclosed in HTML bold tags. If the question-answering
   * feature is enabled, this field will instead contain a snippet that answers
   * the user's natural-language query. No HTML bold tags will be present, and
   * highlights in the answer snippet can be found in QAResult.highlights.
   *
   * @var string
   */
  public $searchTextSnippet;

  /**
   * Document that matches the specified SearchDocumentsRequest. This document
   * only contains indexed metadata information.
   *
   * @param GoogleCloudContentwarehouseV1Document $document
   */
  public function setDocument(GoogleCloudContentwarehouseV1Document $document)
  {
    $this->document = $document;
  }
  /**
   * @return GoogleCloudContentwarehouseV1Document
   */
  public function getDocument()
  {
    return $this->document;
  }
  /**
   * Return the 1-based page indices where those pages have one or more matched
   * tokens.
   *
   * @param string[] $matchedTokenPageIndices
   */
  public function setMatchedTokenPageIndices($matchedTokenPageIndices)
  {
    $this->matchedTokenPageIndices = $matchedTokenPageIndices;
  }
  /**
   * @return string[]
   */
  public function getMatchedTokenPageIndices()
  {
    return $this->matchedTokenPageIndices;
  }
  /**
   * Experimental. Additional result info if the question-answering feature is
   * enabled.
   *
   * @param GoogleCloudContentwarehouseV1QAResult $qaResult
   */
  public function setQaResult(GoogleCloudContentwarehouseV1QAResult $qaResult)
  {
    $this->qaResult = $qaResult;
  }
  /**
   * @return GoogleCloudContentwarehouseV1QAResult
   */
  public function getQaResult()
  {
    return $this->qaResult;
  }
  /**
   * Contains snippets of text from the document full raw text that most closely
   * match a search query's keywords, if available. All HTML tags in the
   * original fields are stripped when returned in this field, and matching
   * query keywords are enclosed in HTML bold tags. If the question-answering
   * feature is enabled, this field will instead contain a snippet that answers
   * the user's natural-language query. No HTML bold tags will be present, and
   * highlights in the answer snippet can be found in QAResult.highlights.
   *
   * @param string $searchTextSnippet
   */
  public function setSearchTextSnippet($searchTextSnippet)
  {
    $this->searchTextSnippet = $searchTextSnippet;
  }
  /**
   * @return string
   */
  public function getSearchTextSnippet()
  {
    return $this->searchTextSnippet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1SearchDocumentsResponseMatchingDocument::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1SearchDocumentsResponseMatchingDocument');
