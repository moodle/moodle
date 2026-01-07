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

class AnnotateTextResponse extends \Google\Collection
{
  protected $collection_key = 'sentences';
  protected $categoriesType = ClassificationCategory::class;
  protected $categoriesDataType = 'array';
  protected $documentSentimentType = Sentiment::class;
  protected $documentSentimentDataType = '';
  protected $entitiesType = Entity::class;
  protected $entitiesDataType = 'array';
  /**
   * The language of the text, which will be the same as the language specified
   * in the request or, if not specified, the automatically-detected language.
   * See Document.language_code field for more details.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Whether the language is officially supported by all requested features. The
   * API may still return a response when the language is not supported, but it
   * is on a best effort basis.
   *
   * @var bool
   */
  public $languageSupported;
  protected $moderationCategoriesType = ClassificationCategory::class;
  protected $moderationCategoriesDataType = 'array';
  protected $sentencesType = Sentence::class;
  protected $sentencesDataType = 'array';

  /**
   * Categories identified in the input document.
   *
   * @param ClassificationCategory[] $categories
   */
  public function setCategories($categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return ClassificationCategory[]
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * The overall sentiment for the document. Populated if the user enables
   * AnnotateTextRequest.Features.extract_document_sentiment.
   *
   * @param Sentiment $documentSentiment
   */
  public function setDocumentSentiment(Sentiment $documentSentiment)
  {
    $this->documentSentiment = $documentSentiment;
  }
  /**
   * @return Sentiment
   */
  public function getDocumentSentiment()
  {
    return $this->documentSentiment;
  }
  /**
   * Entities, along with their semantic information, in the input document.
   * Populated if the user enables AnnotateTextRequest.Features.extract_entities
   * .
   *
   * @param Entity[] $entities
   */
  public function setEntities($entities)
  {
    $this->entities = $entities;
  }
  /**
   * @return Entity[]
   */
  public function getEntities()
  {
    return $this->entities;
  }
  /**
   * The language of the text, which will be the same as the language specified
   * in the request or, if not specified, the automatically-detected language.
   * See Document.language_code field for more details.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Whether the language is officially supported by all requested features. The
   * API may still return a response when the language is not supported, but it
   * is on a best effort basis.
   *
   * @param bool $languageSupported
   */
  public function setLanguageSupported($languageSupported)
  {
    $this->languageSupported = $languageSupported;
  }
  /**
   * @return bool
   */
  public function getLanguageSupported()
  {
    return $this->languageSupported;
  }
  /**
   * Harmful and sensitive categories identified in the input document.
   *
   * @param ClassificationCategory[] $moderationCategories
   */
  public function setModerationCategories($moderationCategories)
  {
    $this->moderationCategories = $moderationCategories;
  }
  /**
   * @return ClassificationCategory[]
   */
  public function getModerationCategories()
  {
    return $this->moderationCategories;
  }
  /**
   * Sentences in the input document. Populated if the user enables
   * AnnotateTextRequest.Features.extract_document_sentiment.
   *
   * @param Sentence[] $sentences
   */
  public function setSentences($sentences)
  {
    $this->sentences = $sentences;
  }
  /**
   * @return Sentence[]
   */
  public function getSentences()
  {
    return $this->sentences;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnnotateTextResponse::class, 'Google_Service_CloudNaturalLanguage_AnnotateTextResponse');
