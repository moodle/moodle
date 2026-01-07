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

class GoogleCloudDialogflowV2beta1SuggestArticlesResponse extends \Google\Collection
{
  protected $collection_key = 'articleAnswers';
  protected $articleAnswersType = GoogleCloudDialogflowV2beta1ArticleAnswer::class;
  protected $articleAnswersDataType = 'array';
  /**
   * Number of messages prior to and including latest_message to compile the
   * suggestion. It may be smaller than the SuggestArticlesResponse.context_size
   * field in the request if there aren't that many messages in the
   * conversation.
   *
   * @var int
   */
  public $contextSize;
  /**
   * The name of the latest conversation message used to compile suggestion for.
   * Format: `projects//locations//conversations//messages/`.
   *
   * @var string
   */
  public $latestMessage;

  /**
   * Output only. Articles ordered by score in descending order.
   *
   * @param GoogleCloudDialogflowV2beta1ArticleAnswer[] $articleAnswers
   */
  public function setArticleAnswers($articleAnswers)
  {
    $this->articleAnswers = $articleAnswers;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1ArticleAnswer[]
   */
  public function getArticleAnswers()
  {
    return $this->articleAnswers;
  }
  /**
   * Number of messages prior to and including latest_message to compile the
   * suggestion. It may be smaller than the SuggestArticlesResponse.context_size
   * field in the request if there aren't that many messages in the
   * conversation.
   *
   * @param int $contextSize
   */
  public function setContextSize($contextSize)
  {
    $this->contextSize = $contextSize;
  }
  /**
   * @return int
   */
  public function getContextSize()
  {
    return $this->contextSize;
  }
  /**
   * The name of the latest conversation message used to compile suggestion for.
   * Format: `projects//locations//conversations//messages/`.
   *
   * @param string $latestMessage
   */
  public function setLatestMessage($latestMessage)
  {
    $this->latestMessage = $latestMessage;
  }
  /**
   * @return string
   */
  public function getLatestMessage()
  {
    return $this->latestMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1SuggestArticlesResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1SuggestArticlesResponse');
