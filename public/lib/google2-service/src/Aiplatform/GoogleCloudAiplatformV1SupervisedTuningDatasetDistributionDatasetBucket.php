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

class GoogleCloudAiplatformV1SupervisedTuningDatasetDistributionDatasetBucket extends \Google\Model
{
  /**
   * Output only. Number of values in the bucket.
   *
   * @var 
   */
  public $count;
  /**
   * Output only. Left bound of the bucket.
   *
   * @var 
   */
  public $left;
  /**
   * Output only. Right bound of the bucket.
   *
   * @var 
   */
  public $right;

  public function setCount($count)
  {
    $this->count = $count;
  }
  public function getCount()
  {
    return $this->count;
  }
  public function setLeft($left)
  {
    $this->left = $left;
  }
  public function getLeft()
  {
    return $this->left;
  }
  public function setRight($right)
  {
    $this->right = $right;
  }
  public function getRight()
  {
    return $this->right;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SupervisedTuningDatasetDistributionDatasetBucket::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SupervisedTuningDatasetDistributionDatasetBucket');
