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

class GoogleCloudAiplatformV1SchemaPromptSpecReferenceSentencePairList extends \Google\Collection
{
  protected $collection_key = 'referenceSentencePairs';
  protected $referenceSentencePairsType = GoogleCloudAiplatformV1SchemaPromptSpecReferenceSentencePair::class;
  protected $referenceSentencePairsDataType = 'array';

  /**
   * Reference sentence pairs.
   *
   * @param GoogleCloudAiplatformV1SchemaPromptSpecReferenceSentencePair[] $referenceSentencePairs
   */
  public function setReferenceSentencePairs($referenceSentencePairs)
  {
    $this->referenceSentencePairs = $referenceSentencePairs;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaPromptSpecReferenceSentencePair[]
   */
  public function getReferenceSentencePairs()
  {
    return $this->referenceSentencePairs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPromptSpecReferenceSentencePairList::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPromptSpecReferenceSentencePairList');
