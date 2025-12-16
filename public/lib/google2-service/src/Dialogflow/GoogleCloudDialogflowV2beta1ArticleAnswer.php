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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowV2beta1ArticleAnswer extends \Google\Collection
{
  protected $collection_key = 'snippets';
  /**
   * The name of answer record, in the format of
   * "projects//locations//answerRecords/"
   *
   * @var string
   */
  public $answerRecord;
  /**
   * A map that contains metadata about the answer and the document from which
   * it originates.
   *
   * @var string[]
   */
  public $metadata;
  /**
   * Output only. Article snippets.
   *
   * @var string[]
   */
  public $snippets;
  /**
   * The article title.
   *
   * @var string
   */
  public $title;
  /**
   * The article URI.
   *
   * @var string
   */
  public $uri;

  /**
   * The name of answer record, in the format of
   * "projects//locations//answerRecords/"
   *
   * @param string $answerRecord
   */
  public function setAnswerRecord($answerRecord)
  {
    $this->answerRecord = $answerRecord;
  }
  /**
   * @return string
   */
  public function getAnswerRecord()
  {
    return $this->answerRecord;
  }
  /**
   * A map that contains metadata about the answer and the document from which
   * it originates.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Output only. Article snippets.
   *
   * @param string[] $snippets
   */
  public function setSnippets($snippets)
  {
    $this->snippets = $snippets;
  }
  /**
   * @return string[]
   */
  public function getSnippets()
  {
    return $this->snippets;
  }
  /**
   * The article title.
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
   * The article URI.
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
class_alias(GoogleCloudDialogflowV2beta1ArticleAnswer::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1ArticleAnswer');
