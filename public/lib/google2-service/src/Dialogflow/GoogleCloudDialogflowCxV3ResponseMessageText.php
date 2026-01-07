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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3ResponseMessageText extends \Google\Collection
{
  protected $collection_key = 'text';
  /**
   * Output only. Whether the playback of this message can be interrupted by the
   * end user's speech and the client can then starts the next Dialogflow
   * request.
   *
   * @var bool
   */
  public $allowPlaybackInterruption;
  /**
   * Required. A collection of text response variants. If multiple variants are
   * defined, only one text response variant is returned at runtime.
   *
   * @var string[]
   */
  public $text;

  /**
   * Output only. Whether the playback of this message can be interrupted by the
   * end user's speech and the client can then starts the next Dialogflow
   * request.
   *
   * @param bool $allowPlaybackInterruption
   */
  public function setAllowPlaybackInterruption($allowPlaybackInterruption)
  {
    $this->allowPlaybackInterruption = $allowPlaybackInterruption;
  }
  /**
   * @return bool
   */
  public function getAllowPlaybackInterruption()
  {
    return $this->allowPlaybackInterruption;
  }
  /**
   * Required. A collection of text response variants. If multiple variants are
   * defined, only one text response variant is returned at runtime.
   *
   * @param string[] $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string[]
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ResponseMessageText::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ResponseMessageText');
