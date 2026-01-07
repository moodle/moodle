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

class GoogleCloudAiplatformV1PipelineTaskExecutorDetail extends \Google\Model
{
  protected $containerDetailType = GoogleCloudAiplatformV1PipelineTaskExecutorDetailContainerDetail::class;
  protected $containerDetailDataType = '';
  protected $customJobDetailType = GoogleCloudAiplatformV1PipelineTaskExecutorDetailCustomJobDetail::class;
  protected $customJobDetailDataType = '';

  /**
   * Output only. The detailed info for a container executor.
   *
   * @param GoogleCloudAiplatformV1PipelineTaskExecutorDetailContainerDetail $containerDetail
   */
  public function setContainerDetail(GoogleCloudAiplatformV1PipelineTaskExecutorDetailContainerDetail $containerDetail)
  {
    $this->containerDetail = $containerDetail;
  }
  /**
   * @return GoogleCloudAiplatformV1PipelineTaskExecutorDetailContainerDetail
   */
  public function getContainerDetail()
  {
    return $this->containerDetail;
  }
  /**
   * Output only. The detailed info for a custom job executor.
   *
   * @param GoogleCloudAiplatformV1PipelineTaskExecutorDetailCustomJobDetail $customJobDetail
   */
  public function setCustomJobDetail(GoogleCloudAiplatformV1PipelineTaskExecutorDetailCustomJobDetail $customJobDetail)
  {
    $this->customJobDetail = $customJobDetail;
  }
  /**
   * @return GoogleCloudAiplatformV1PipelineTaskExecutorDetailCustomJobDetail
   */
  public function getCustomJobDetail()
  {
    return $this->customJobDetail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PipelineTaskExecutorDetail::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PipelineTaskExecutorDetail');
