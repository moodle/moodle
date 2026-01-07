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

class GoogleCloudAiplatformV1RagVectorDbConfigRagManagedDb extends \Google\Model
{
  protected $annType = GoogleCloudAiplatformV1RagVectorDbConfigRagManagedDbANN::class;
  protected $annDataType = '';
  protected $knnType = GoogleCloudAiplatformV1RagVectorDbConfigRagManagedDbKNN::class;
  protected $knnDataType = '';

  /**
   * Performs an ANN search on RagCorpus. Use this if you have a lot of files (>
   * 10K) in your RagCorpus and want to reduce the search latency.
   *
   * @param GoogleCloudAiplatformV1RagVectorDbConfigRagManagedDbANN $ann
   */
  public function setAnn(GoogleCloudAiplatformV1RagVectorDbConfigRagManagedDbANN $ann)
  {
    $this->ann = $ann;
  }
  /**
   * @return GoogleCloudAiplatformV1RagVectorDbConfigRagManagedDbANN
   */
  public function getAnn()
  {
    return $this->ann;
  }
  /**
   * Performs a KNN search on RagCorpus. Default choice if not specified.
   *
   * @param GoogleCloudAiplatformV1RagVectorDbConfigRagManagedDbKNN $knn
   */
  public function setKnn(GoogleCloudAiplatformV1RagVectorDbConfigRagManagedDbKNN $knn)
  {
    $this->knn = $knn;
  }
  /**
   * @return GoogleCloudAiplatformV1RagVectorDbConfigRagManagedDbKNN
   */
  public function getKnn()
  {
    return $this->knn;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RagVectorDbConfigRagManagedDb::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagVectorDbConfigRagManagedDb');
