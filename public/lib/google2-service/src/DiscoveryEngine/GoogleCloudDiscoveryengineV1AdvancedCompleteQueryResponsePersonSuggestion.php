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

class GoogleCloudDiscoveryengineV1AdvancedCompleteQueryResponsePersonSuggestion extends \Google\Model
{
  /**
   * Default value.
   */
  public const PERSON_TYPE_PERSON_TYPE_UNSPECIFIED = 'PERSON_TYPE_UNSPECIFIED';
  /**
   * The suggestion is from a GOOGLE_IDENTITY source.
   */
  public const PERSON_TYPE_CLOUD_IDENTITY = 'CLOUD_IDENTITY';
  /**
   * The suggestion is from a THIRD_PARTY_IDENTITY source.
   */
  public const PERSON_TYPE_THIRD_PARTY_IDENTITY = 'THIRD_PARTY_IDENTITY';
  /**
   * The name of the dataStore that this suggestion belongs to.
   *
   * @var string
   */
  public $dataStore;
  /**
   * The destination uri of the person suggestion.
   *
   * @var string
   */
  public $destinationUri;
  /**
   * The photo uri of the person suggestion.
   *
   * @var string
   */
  public $displayPhotoUri;
  protected $documentType = GoogleCloudDiscoveryengineV1Document::class;
  protected $documentDataType = '';
  /**
   * The type of the person.
   *
   * @var string
   */
  public $personType;
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
   * The destination uri of the person suggestion.
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
   * The photo uri of the person suggestion.
   *
   * @param string $displayPhotoUri
   */
  public function setDisplayPhotoUri($displayPhotoUri)
  {
    $this->displayPhotoUri = $displayPhotoUri;
  }
  /**
   * @return string
   */
  public function getDisplayPhotoUri()
  {
    return $this->displayPhotoUri;
  }
  /**
   * The document data snippet in the suggestion. Only a subset of fields is
   * populated.
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
   * The type of the person.
   *
   * Accepted values: PERSON_TYPE_UNSPECIFIED, CLOUD_IDENTITY,
   * THIRD_PARTY_IDENTITY
   *
   * @param self::PERSON_TYPE_* $personType
   */
  public function setPersonType($personType)
  {
    $this->personType = $personType;
  }
  /**
   * @return self::PERSON_TYPE_*
   */
  public function getPersonType()
  {
    return $this->personType;
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
class_alias(GoogleCloudDiscoveryengineV1AdvancedCompleteQueryResponsePersonSuggestion::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AdvancedCompleteQueryResponsePersonSuggestion');
