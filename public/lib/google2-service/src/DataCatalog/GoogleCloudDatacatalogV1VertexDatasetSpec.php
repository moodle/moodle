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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1VertexDatasetSpec extends \Google\Model
{
  /**
   * Should not be used.
   */
  public const DATA_TYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  /**
   * Structured data dataset.
   */
  public const DATA_TYPE_TABLE = 'TABLE';
  /**
   * Image dataset which supports ImageClassification, ImageObjectDetection and
   * ImageSegmentation problems.
   */
  public const DATA_TYPE_IMAGE = 'IMAGE';
  /**
   * Document dataset which supports TextClassification, TextExtraction and
   * TextSentiment problems.
   */
  public const DATA_TYPE_TEXT = 'TEXT';
  /**
   * Video dataset which supports VideoClassification, VideoObjectTracking and
   * VideoActionRecognition problems.
   */
  public const DATA_TYPE_VIDEO = 'VIDEO';
  /**
   * Conversation dataset which supports conversation problems.
   */
  public const DATA_TYPE_CONVERSATION = 'CONVERSATION';
  /**
   * TimeSeries dataset.
   */
  public const DATA_TYPE_TIME_SERIES = 'TIME_SERIES';
  /**
   * Document dataset which supports DocumentAnnotation problems.
   */
  public const DATA_TYPE_DOCUMENT = 'DOCUMENT';
  /**
   * TextToSpeech dataset which supports TextToSpeech problems.
   */
  public const DATA_TYPE_TEXT_TO_SPEECH = 'TEXT_TO_SPEECH';
  /**
   * Translation dataset which supports Translation problems.
   */
  public const DATA_TYPE_TRANSLATION = 'TRANSLATION';
  /**
   * Store Vision dataset which is used for HITL integration.
   */
  public const DATA_TYPE_STORE_VISION = 'STORE_VISION';
  /**
   * Enterprise Knowledge Graph dataset which is used for HITL labeling
   * integration.
   */
  public const DATA_TYPE_ENTERPRISE_KNOWLEDGE_GRAPH = 'ENTERPRISE_KNOWLEDGE_GRAPH';
  /**
   * Text prompt dataset which supports Large Language Models.
   */
  public const DATA_TYPE_TEXT_PROMPT = 'TEXT_PROMPT';
  /**
   * The number of DataItems in this Dataset. Only apply for non-structured
   * Dataset.
   *
   * @var string
   */
  public $dataItemCount;
  /**
   * Type of the dataset.
   *
   * @var string
   */
  public $dataType;

  /**
   * The number of DataItems in this Dataset. Only apply for non-structured
   * Dataset.
   *
   * @param string $dataItemCount
   */
  public function setDataItemCount($dataItemCount)
  {
    $this->dataItemCount = $dataItemCount;
  }
  /**
   * @return string
   */
  public function getDataItemCount()
  {
    return $this->dataItemCount;
  }
  /**
   * Type of the dataset.
   *
   * Accepted values: DATA_TYPE_UNSPECIFIED, TABLE, IMAGE, TEXT, VIDEO,
   * CONVERSATION, TIME_SERIES, DOCUMENT, TEXT_TO_SPEECH, TRANSLATION,
   * STORE_VISION, ENTERPRISE_KNOWLEDGE_GRAPH, TEXT_PROMPT
   *
   * @param self::DATA_TYPE_* $dataType
   */
  public function setDataType($dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return self::DATA_TYPE_*
   */
  public function getDataType()
  {
    return $this->dataType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1VertexDatasetSpec::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1VertexDatasetSpec');
