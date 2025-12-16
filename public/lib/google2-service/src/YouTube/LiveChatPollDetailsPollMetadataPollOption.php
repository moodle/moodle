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

class LiveChatPollDetailsPollMetadataPollOption extends \Google\Model
{
  /**
   * @var string
   */
  public $optionText;
  /**
   * @var string
   */
  public $tally;

  /**
   * @param string $optionText
   */
  public function setOptionText($optionText)
  {
    $this->optionText = $optionText;
  }
  /**
   * @return string
   */
  public function getOptionText()
  {
    return $this->optionText;
  }
  /**
   * @param string $tally
   */
  public function setTally($tally)
  {
    $this->tally = $tally;
  }
  /**
   * @return string
   */
  public function getTally()
  {
    return $this->tally;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveChatPollDetailsPollMetadataPollOption::class, 'Google_Service_YouTube_LiveChatPollDetailsPollMetadataPollOption');
