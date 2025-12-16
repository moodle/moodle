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

class GoogleCloudAiplatformV1RagQuery extends \Google\Model
{
  protected $ragRetrievalConfigType = GoogleCloudAiplatformV1RagRetrievalConfig::class;
  protected $ragRetrievalConfigDataType = '';
  /**
   * Optional. The query in text format to get relevant contexts.
   *
   * @var string
   */
  public $text;

  /**
   * Optional. The retrieval config for the query.
   *
   * @param GoogleCloudAiplatformV1RagRetrievalConfig $ragRetrievalConfig
   */
  public function setRagRetrievalConfig(GoogleCloudAiplatformV1RagRetrievalConfig $ragRetrievalConfig)
  {
    $this->ragRetrievalConfig = $ragRetrievalConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1RagRetrievalConfig
   */
  public function getRagRetrievalConfig()
  {
    return $this->ragRetrievalConfig;
  }
  /**
   * Optional. The query in text format to get relevant contexts.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RagQuery::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagQuery');
