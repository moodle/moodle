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

class GoogleCloudAiplatformV1EvaluationInstanceInstanceData extends \Google\Model
{
  protected $contentsType = GoogleCloudAiplatformV1EvaluationInstanceInstanceDataContents::class;
  protected $contentsDataType = '';
  /**
   * Text data.
   *
   * @var string
   */
  public $text;

  /**
   * List of Gemini content data.
   *
   * @param GoogleCloudAiplatformV1EvaluationInstanceInstanceDataContents $contents
   */
  public function setContents(GoogleCloudAiplatformV1EvaluationInstanceInstanceDataContents $contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationInstanceInstanceDataContents
   */
  public function getContents()
  {
    return $this->contents;
  }
  /**
   * Text data.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EvaluationInstanceInstanceData::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EvaluationInstanceInstanceData');
