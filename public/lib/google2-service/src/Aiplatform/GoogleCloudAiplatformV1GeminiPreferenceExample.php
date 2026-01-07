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

class GoogleCloudAiplatformV1GeminiPreferenceExample extends \Google\Collection
{
  protected $collection_key = 'contents';
  protected $completionsType = GoogleCloudAiplatformV1GeminiPreferenceExampleCompletion::class;
  protected $completionsDataType = 'array';
  protected $contentsType = GoogleCloudAiplatformV1Content::class;
  protected $contentsDataType = 'array';

  /**
   * List of completions for a given prompt.
   *
   * @param GoogleCloudAiplatformV1GeminiPreferenceExampleCompletion[] $completions
   */
  public function setCompletions($completions)
  {
    $this->completions = $completions;
  }
  /**
   * @return GoogleCloudAiplatformV1GeminiPreferenceExampleCompletion[]
   */
  public function getCompletions()
  {
    return $this->completions;
  }
  /**
   * Multi-turn contents that represents the Prompt.
   *
   * @param GoogleCloudAiplatformV1Content[] $contents
   */
  public function setContents($contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return GoogleCloudAiplatformV1Content[]
   */
  public function getContents()
  {
    return $this->contents;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GeminiPreferenceExample::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GeminiPreferenceExample');
