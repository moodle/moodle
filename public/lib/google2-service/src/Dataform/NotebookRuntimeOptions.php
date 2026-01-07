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

namespace Google\Service\Dataform;

class NotebookRuntimeOptions extends \Google\Model
{
  /**
   * Optional. The resource name of the [Colab runtime template]
   * (https://cloud.google.com/colab/docs/runtimes), from which a runtime is
   * created for notebook executions. If not specified, a runtime is created
   * with Colab's default specifications.
   *
   * @var string
   */
  public $aiPlatformNotebookRuntimeTemplate;
  /**
   * Optional. The Google Cloud Storage location to upload the result to.
   * Format: `gs://bucket-name`.
   *
   * @var string
   */
  public $gcsOutputBucket;

  /**
   * Optional. The resource name of the [Colab runtime template]
   * (https://cloud.google.com/colab/docs/runtimes), from which a runtime is
   * created for notebook executions. If not specified, a runtime is created
   * with Colab's default specifications.
   *
   * @param string $aiPlatformNotebookRuntimeTemplate
   */
  public function setAiPlatformNotebookRuntimeTemplate($aiPlatformNotebookRuntimeTemplate)
  {
    $this->aiPlatformNotebookRuntimeTemplate = $aiPlatformNotebookRuntimeTemplate;
  }
  /**
   * @return string
   */
  public function getAiPlatformNotebookRuntimeTemplate()
  {
    return $this->aiPlatformNotebookRuntimeTemplate;
  }
  /**
   * Optional. The Google Cloud Storage location to upload the result to.
   * Format: `gs://bucket-name`.
   *
   * @param string $gcsOutputBucket
   */
  public function setGcsOutputBucket($gcsOutputBucket)
  {
    $this->gcsOutputBucket = $gcsOutputBucket;
  }
  /**
   * @return string
   */
  public function getGcsOutputBucket()
  {
    return $this->gcsOutputBucket;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NotebookRuntimeOptions::class, 'Google_Service_Dataform_NotebookRuntimeOptions');
