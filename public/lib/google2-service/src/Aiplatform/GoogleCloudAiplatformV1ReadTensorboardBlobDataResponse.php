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

class GoogleCloudAiplatformV1ReadTensorboardBlobDataResponse extends \Google\Collection
{
  protected $collection_key = 'blobs';
  protected $blobsType = GoogleCloudAiplatformV1TensorboardBlob::class;
  protected $blobsDataType = 'array';

  /**
   * Blob messages containing blob bytes.
   *
   * @param GoogleCloudAiplatformV1TensorboardBlob[] $blobs
   */
  public function setBlobs($blobs)
  {
    $this->blobs = $blobs;
  }
  /**
   * @return GoogleCloudAiplatformV1TensorboardBlob[]
   */
  public function getBlobs()
  {
    return $this->blobs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ReadTensorboardBlobDataResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReadTensorboardBlobDataResponse');
