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

class GoogleCloudAiplatformV1NotebookExecutionJobGcsNotebookSource extends \Google\Model
{
  /**
   * The version of the Cloud Storage object to read. If unset, the current
   * version of the object is read. See
   * https://cloud.google.com/storage/docs/metadata#generation-number.
   *
   * @var string
   */
  public $generation;
  /**
   * The Cloud Storage uri pointing to the ipynb file. Format:
   * `gs://bucket/notebook_file.ipynb`
   *
   * @var string
   */
  public $uri;

  /**
   * The version of the Cloud Storage object to read. If unset, the current
   * version of the object is read. See
   * https://cloud.google.com/storage/docs/metadata#generation-number.
   *
   * @param string $generation
   */
  public function setGeneration($generation)
  {
    $this->generation = $generation;
  }
  /**
   * @return string
   */
  public function getGeneration()
  {
    return $this->generation;
  }
  /**
   * The Cloud Storage uri pointing to the ipynb file. Format:
   * `gs://bucket/notebook_file.ipynb`
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NotebookExecutionJobGcsNotebookSource::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NotebookExecutionJobGcsNotebookSource');
