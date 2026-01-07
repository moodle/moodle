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

class GoogleCloudAiplatformV1VertexRagStoreRagResource extends \Google\Collection
{
  protected $collection_key = 'ragFileIds';
  /**
   * Optional. RagCorpora resource name. Format:
   * `projects/{project}/locations/{location}/ragCorpora/{rag_corpus}`
   *
   * @var string
   */
  public $ragCorpus;
  /**
   * Optional. rag_file_id. The files should be in the same rag_corpus set in
   * rag_corpus field.
   *
   * @var string[]
   */
  public $ragFileIds;

  /**
   * Optional. RagCorpora resource name. Format:
   * `projects/{project}/locations/{location}/ragCorpora/{rag_corpus}`
   *
   * @param string $ragCorpus
   */
  public function setRagCorpus($ragCorpus)
  {
    $this->ragCorpus = $ragCorpus;
  }
  /**
   * @return string
   */
  public function getRagCorpus()
  {
    return $this->ragCorpus;
  }
  /**
   * Optional. rag_file_id. The files should be in the same rag_corpus set in
   * rag_corpus field.
   *
   * @param string[] $ragFileIds
   */
  public function setRagFileIds($ragFileIds)
  {
    $this->ragFileIds = $ragFileIds;
  }
  /**
   * @return string[]
   */
  public function getRagFileIds()
  {
    return $this->ragFileIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1VertexRagStoreRagResource::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1VertexRagStoreRagResource');
