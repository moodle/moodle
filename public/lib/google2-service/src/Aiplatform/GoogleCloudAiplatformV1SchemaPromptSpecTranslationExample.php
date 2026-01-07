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

class GoogleCloudAiplatformV1SchemaPromptSpecTranslationExample extends \Google\Collection
{
  protected $collection_key = 'referenceSentencesFileInputs';
  protected $referenceSentencePairListsType = GoogleCloudAiplatformV1SchemaPromptSpecReferenceSentencePairList::class;
  protected $referenceSentencePairListsDataType = 'array';
  protected $referenceSentencesFileInputsType = GoogleCloudAiplatformV1SchemaPromptSpecTranslationSentenceFileInput::class;
  protected $referenceSentencesFileInputsDataType = 'array';

  /**
   * The reference sentences from inline text.
   *
   * @param GoogleCloudAiplatformV1SchemaPromptSpecReferenceSentencePairList[] $referenceSentencePairLists
   */
  public function setReferenceSentencePairLists($referenceSentencePairLists)
  {
    $this->referenceSentencePairLists = $referenceSentencePairLists;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaPromptSpecReferenceSentencePairList[]
   */
  public function getReferenceSentencePairLists()
  {
    return $this->referenceSentencePairLists;
  }
  /**
   * The reference sentences from file.
   *
   * @param GoogleCloudAiplatformV1SchemaPromptSpecTranslationSentenceFileInput[] $referenceSentencesFileInputs
   */
  public function setReferenceSentencesFileInputs($referenceSentencesFileInputs)
  {
    $this->referenceSentencesFileInputs = $referenceSentencesFileInputs;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaPromptSpecTranslationSentenceFileInput[]
   */
  public function getReferenceSentencesFileInputs()
  {
    return $this->referenceSentencesFileInputs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPromptSpecTranslationExample::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPromptSpecTranslationExample');
