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

namespace Google\Service\YouTube;

class LiveChatPollDetailsPollMetadata extends \Google\Collection
{
  protected $collection_key = 'options';
  protected $optionsType = LiveChatPollDetailsPollMetadataPollOption::class;
  protected $optionsDataType = 'array';
  /**
   * @var string
   */
  public $questionText;

  /**
   * The options will be returned in the order that is displayed in 1P
   *
   * @param LiveChatPollDetailsPollMetadataPollOption[] $options
   */
  public function setOptions($options)
  {
    $this->options = $options;
  }
  /**
   * @return LiveChatPollDetailsPollMetadataPollOption[]
   */
  public function getOptions()
  {
    return $this->options;
  }
  /**
   * @param string $questionText
   */
  public function setQuestionText($questionText)
  {
    $this->questionText = $questionText;
  }
  /**
   * @return string
   */
  public function getQuestionText()
  {
    return $this->questionText;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveChatPollDetailsPollMetadata::class, 'Google_Service_YouTube_LiveChatPollDetailsPollMetadata');
