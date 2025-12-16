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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1EmbedContentRequest extends \Google\Model
{
  /**
   * Unset value, which will default to one of the other enum values.
   */
  public const TASK_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Specifies the given text is a query in a search/retrieval setting.
   */
  public const TASK_TYPE_RETRIEVAL_QUERY = 'RETRIEVAL_QUERY';
  /**
   * Specifies the given text is a document from the corpus being searched.
   */
  public const TASK_TYPE_RETRIEVAL_DOCUMENT = 'RETRIEVAL_DOCUMENT';
  /**
   * Specifies the given text will be used for STS.
   */
  public const TASK_TYPE_SEMANTIC_SIMILARITY = 'SEMANTIC_SIMILARITY';
  /**
   * Specifies that the given text will be classified.
   */
  public const TASK_TYPE_CLASSIFICATION = 'CLASSIFICATION';
  /**
   * Specifies that the embeddings will be used for clustering.
   */
  public const TASK_TYPE_CLUSTERING = 'CLUSTERING';
  /**
   * Specifies that the embeddings will be used for question answering.
   */
  public const TASK_TYPE_QUESTION_ANSWERING = 'QUESTION_ANSWERING';
  /**
   * Specifies that the embeddings will be used for fact verification.
   */
  public const TASK_TYPE_FACT_VERIFICATION = 'FACT_VERIFICATION';
  /**
   * Specifies that the embeddings will be used for code retrieval.
   */
  public const TASK_TYPE_CODE_RETRIEVAL_QUERY = 'CODE_RETRIEVAL_QUERY';
  /**
   * Optional. Whether to silently truncate the input content if it's longer
   * than the maximum sequence length.
   *
   * @var bool
   */
  public $autoTruncate;
  protected $contentType = GoogleCloudAiplatformV1Content::class;
  protected $contentDataType = '';
  /**
   * Optional. Optional reduced dimension for the output embedding. If set,
   * excessive values in the output embedding are truncated from the end.
   *
   * @var int
   */
  public $outputDimensionality;
  /**
   * Optional. The task type of the embedding.
   *
   * @var string
   */
  public $taskType;
  /**
   * Optional. An optional title for the text.
   *
   * @var string
   */
  public $title;

  /**
   * Optional. Whether to silently truncate the input content if it's longer
   * than the maximum sequence length.
   *
   * @param bool $autoTruncate
   */
  public function setAutoTruncate($autoTruncate)
  {
    $this->autoTruncate = $autoTruncate;
  }
  /**
   * @return bool
   */
  public function getAutoTruncate()
  {
    return $this->autoTruncate;
  }
  /**
   * Required. Input content to be embedded. Required.
   *
   * @param GoogleCloudAiplatformV1Content $content
   */
  public function setContent(GoogleCloudAiplatformV1Content $content)
  {
    $this->content = $content;
  }
  /**
   * @return GoogleCloudAiplatformV1Content
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Optional. Optional reduced dimension for the output embedding. If set,
   * excessive values in the output embedding are truncated from the end.
   *
   * @param int $outputDimensionality
   */
  public function setOutputDimensionality($outputDimensionality)
  {
    $this->outputDimensionality = $outputDimensionality;
  }
  /**
   * @return int
   */
  public function getOutputDimensionality()
  {
    return $this->outputDimensionality;
  }
  /**
   * Optional. The task type of the embedding.
   *
   * Accepted values: UNSPECIFIED, RETRIEVAL_QUERY, RETRIEVAL_DOCUMENT,
   * SEMANTIC_SIMILARITY, CLASSIFICATION, CLUSTERING, QUESTION_ANSWERING,
   * FACT_VERIFICATION, CODE_RETRIEVAL_QUERY
   *
   * @param self::TASK_TYPE_* $taskType
   */
  public function setTaskType($taskType)
  {
    $this->taskType = $taskType;
  }
  /**
   * @return self::TASK_TYPE_*
   */
  public function getTaskType()
  {
    return $this->taskType;
  }
  /**
   * Optional. An optional title for the text.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EmbedContentRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EmbedContentRequest');
