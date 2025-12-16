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

class GoogleCloudAiplatformV1MemoryBankCustomizationConfigGenerateMemoriesExample extends \Google\Collection
{
  protected $collection_key = 'generatedMemories';
  protected $conversationSourceType = GoogleCloudAiplatformV1MemoryBankCustomizationConfigGenerateMemoriesExampleConversationSource::class;
  protected $conversationSourceDataType = '';
  protected $generatedMemoriesType = GoogleCloudAiplatformV1MemoryBankCustomizationConfigGenerateMemoriesExampleGeneratedMemory::class;
  protected $generatedMemoriesDataType = 'array';

  /**
   * A conversation source for the example.
   *
   * @param GoogleCloudAiplatformV1MemoryBankCustomizationConfigGenerateMemoriesExampleConversationSource $conversationSource
   */
  public function setConversationSource(GoogleCloudAiplatformV1MemoryBankCustomizationConfigGenerateMemoriesExampleConversationSource $conversationSource)
  {
    $this->conversationSource = $conversationSource;
  }
  /**
   * @return GoogleCloudAiplatformV1MemoryBankCustomizationConfigGenerateMemoriesExampleConversationSource
   */
  public function getConversationSource()
  {
    return $this->conversationSource;
  }
  /**
   * Optional. The memories that are expected to be generated from the input
   * conversation. An empty list indicates that no memories are expected to be
   * generated for the input conversation.
   *
   * @param GoogleCloudAiplatformV1MemoryBankCustomizationConfigGenerateMemoriesExampleGeneratedMemory[] $generatedMemories
   */
  public function setGeneratedMemories($generatedMemories)
  {
    $this->generatedMemories = $generatedMemories;
  }
  /**
   * @return GoogleCloudAiplatformV1MemoryBankCustomizationConfigGenerateMemoriesExampleGeneratedMemory[]
   */
  public function getGeneratedMemories()
  {
    return $this->generatedMemories;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MemoryBankCustomizationConfigGenerateMemoriesExample::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MemoryBankCustomizationConfigGenerateMemoriesExample');
