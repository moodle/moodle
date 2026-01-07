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

class GoogleCloudAiplatformV1RagVectorDbConfigRagManagedDbANN extends \Google\Model
{
  /**
   * Number of leaf nodes in the tree-based structure. Each leaf node contains
   * groups of closely related vectors along with their corresponding centroid.
   * Recommended value is 10 * sqrt(num of RagFiles in your RagCorpus). Default
   * value is 500.
   *
   * @var int
   */
  public $leafCount;
  /**
   * The depth of the tree-based structure. Only depth values of 2 and 3 are
   * supported. Recommended value is 2 if you have if you have O(10K) files in
   * the RagCorpus and set this to 3 if more than that. Default value is 2.
   *
   * @var int
   */
  public $treeDepth;

  /**
   * Number of leaf nodes in the tree-based structure. Each leaf node contains
   * groups of closely related vectors along with their corresponding centroid.
   * Recommended value is 10 * sqrt(num of RagFiles in your RagCorpus). Default
   * value is 500.
   *
   * @param int $leafCount
   */
  public function setLeafCount($leafCount)
  {
    $this->leafCount = $leafCount;
  }
  /**
   * @return int
   */
  public function getLeafCount()
  {
    return $this->leafCount;
  }
  /**
   * The depth of the tree-based structure. Only depth values of 2 and 3 are
   * supported. Recommended value is 2 if you have if you have O(10K) files in
   * the RagCorpus and set this to 3 if more than that. Default value is 2.
   *
   * @param int $treeDepth
   */
  public function setTreeDepth($treeDepth)
  {
    $this->treeDepth = $treeDepth;
  }
  /**
   * @return int
   */
  public function getTreeDepth()
  {
    return $this->treeDepth;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RagVectorDbConfigRagManagedDbANN::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagVectorDbConfigRagManagedDbANN');
