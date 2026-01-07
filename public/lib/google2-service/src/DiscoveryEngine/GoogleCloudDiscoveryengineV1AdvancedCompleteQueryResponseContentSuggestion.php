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

class GoogleCloudDiscoveryengineV1AdvancedCompleteQueryResponseContentSuggestion extends \Google\Model
{
  /**
   * Default value.
   */
  public const CONTENT_TYPE_CONTENT_TYPE_UNSPECIFIED = 'CONTENT_TYPE_UNSPECIFIED';
  /**
   * The suggestion is from a Google Workspace source.
   */
  public const CONTENT_TYPE_GOOGLE_WORKSPACE = 'GOOGLE_WORKSPACE';
  /**
   * The suggestion is from a third party source.
   */
  public const CONTENT_TYPE_THIRD_PARTY = 'THIRD_PARTY';
  /**
   * The type of the content suggestion.
   *
   * @var string
   */
  public $contentType;
  /**
   * The name of the dataStore that this suggestion belongs to.
   *
   * @var string
   */
  public $dataStore;
  /**
   * The destination uri of the content suggestion.
   *
   * @var string
   */
  public $destinationUri;
  protected $documentType = GoogleCloudDiscoveryengineV1Document::class;
  protected $documentDataType = '';
  /**
   * The icon uri of the content suggestion.
   *
   * @var string
   */
  public $iconUri;
  /**
   * The score of each suggestion. The score is in the range of [0, 1].
   *
   * @var 
   */
  public $score;
  /**
   * The suggestion for the query.
   *
   * @var string
   */
  public $suggestion;

  /**
   * The type of the content suggestion.
   *
   * Accepted values: CONTENT_TYPE_UNSPECIFIED, GOOGLE_WORKSPACE, THIRD_PARTY
   *
   * @param self::CONTENT_TYPE_* $contentType
   */
  public function setContentType($contentType)
  {
    $this->contentType = $contentType;
  }
  /**
   * @return self::CONTENT_TYPE_*
   */
  public function getContentType()
  {
    return $this->contentType;
  }
  /**
   * The name of the dataStore that this suggestion belongs to.
   *
   * @param string $dataStore
   */
  public function setDataStore($dataStore)
  {
    $this->dataStore = $dataStore;
  }
  /**
   * @return string
   */
  public function getDataStore()
  {
    return $this->dataStore;
  }
  /**
   * The destination uri of the content suggestion.
   *
   * @param string $destinationUri
   */
  public function setDestinationUri($destinationUri)
  {
    $this->destinationUri = $destinationUri;
  }
  /**
   * @return string
   */
  public function getDestinationUri()
  {
    return $this->destinationUri;
  }
  /**
   * The document data snippet in the suggestion. Only a subset of fields will
   * be populated.
   *
   * @param GoogleCloudDiscoveryengineV1Document $document
   */
  public function setDocument(GoogleCloudDiscoveryengineV1Document $document)
  {
    $this->document = $document;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1Document
   */
  public function getDocument()
  {
    return $this->document;
  }
  /**
   * The icon uri of the content suggestion.
   *
   * @param string $iconUri
   */
  public function setIconUri($iconUri)
  {
    $this->iconUri = $iconUri;
  }
  /**
   * @return string
   */
  public function getIconUri()
  {
    return $this->iconUri;
  }
  public function setScore($score)
  {
    $this->score = $score;
  }
  public function getScore()
  {
    return $this->score;
  }
  /**
   * The suggestion for the query.
   *
   * @param string $suggestion
   */
  public function setSuggestion($suggestion)
  {
    $this->suggestion = $suggestion;
  }
  /**
   * @return string
   */
  public function getSuggestion()
  {
    return $this->suggestion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AdvancedCompleteQueryResponseContentSuggestion::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AdvancedCompleteQueryResponseContentSuggestion');
