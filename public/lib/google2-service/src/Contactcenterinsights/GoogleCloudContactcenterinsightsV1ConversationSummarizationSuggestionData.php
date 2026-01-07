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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1ConversationSummarizationSuggestionData extends \Google\Model
{
  /**
   * The name of the answer record. Format:
   * projects/{project}/locations/{location}/answerRecords/{answer_record}
   *
   * @var string
   */
  public $answerRecord;
  /**
   * The confidence score of the summarization.
   *
   * @var float
   */
  public $confidence;
  /**
   * The name of the model that generates this summary. Format: projects/{projec
   * t}/locations/{location}/conversationModels/{conversation_model}
   *
   * @var string
   */
  public $conversationModel;
  /**
   * Agent Assist generator ID.
   *
   * @var string
   */
  public $generatorId;
  /**
   * A map that contains metadata about the summarization and the document from
   * which it originates.
   *
   * @var string[]
   */
  public $metadata;
  /**
   * The summarization content that is concatenated into one string.
   *
   * @var string
   */
  public $text;
  /**
   * The summarization content that is divided into sections. The key is the
   * section's name and the value is the section's content. There is no specific
   * format for the key or value.
   *
   * @var string[]
   */
  public $textSections;

  /**
   * The name of the answer record. Format:
   * projects/{project}/locations/{location}/answerRecords/{answer_record}
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
   * The confidence score of the summarization.
   *
   * @param float $confidence
   */
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  /**
   * @return float
   */
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * The name of the model that generates this summary. Format: projects/{projec
   * t}/locations/{location}/conversationModels/{conversation_model}
   *
   * @param string $conversationModel
   */
  public function setConversationModel($conversationModel)
  {
    $this->conversationModel = $conversationModel;
  }
  /**
   * @return string
   */
  public function getConversationModel()
  {
    return $this->conversationModel;
  }
  /**
   * Agent Assist generator ID.
   *
   * @param string $generatorId
   */
  public function setGeneratorId($generatorId)
  {
    $this->generatorId = $generatorId;
  }
  /**
   * @return string
   */
  public function getGeneratorId()
  {
    return $this->generatorId;
  }
  /**
   * A map that contains metadata about the summarization and the document from
   * which it originates.
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
   * The summarization content that is concatenated into one string.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * The summarization content that is divided into sections. The key is the
   * section's name and the value is the section's content. There is no specific
   * format for the key or value.
   *
   * @param string[] $textSections
   */
  public function setTextSections($textSections)
  {
    $this->textSections = $textSections;
  }
  /**
   * @return string[]
   */
  public function getTextSections()
  {
    return $this->textSections;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1ConversationSummarizationSuggestionData::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1ConversationSummarizationSuggestionData');
