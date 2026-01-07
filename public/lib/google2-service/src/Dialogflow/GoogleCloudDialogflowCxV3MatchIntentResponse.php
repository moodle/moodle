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

class GoogleCloudDialogflowCxV3MatchIntentResponse extends \Google\Collection
{
  protected $collection_key = 'matches';
  protected $currentPageType = GoogleCloudDialogflowCxV3Page::class;
  protected $currentPageDataType = '';
  protected $matchesType = GoogleCloudDialogflowCxV3Match::class;
  protected $matchesDataType = 'array';
  /**
   * If natural language text was provided as input, this field will contain a
   * copy of the text.
   *
   * @var string
   */
  public $text;
  /**
   * If natural language speech audio was provided as input, this field will
   * contain the transcript for the audio.
   *
   * @var string
   */
  public $transcript;
  /**
   * If an event was provided as input, this field will contain a copy of the
   * event name.
   *
   * @var string
   */
  public $triggerEvent;
  /**
   * If an intent was provided as input, this field will contain a copy of the
   * intent identifier. Format: `projects//locations//agents//intents/`.
   *
   * @var string
   */
  public $triggerIntent;

  /**
   * The current Page. Some, not all fields are filled in this message,
   * including but not limited to `name` and `display_name`.
   *
   * @param GoogleCloudDialogflowCxV3Page $currentPage
   */
  public function setCurrentPage(GoogleCloudDialogflowCxV3Page $currentPage)
  {
    $this->currentPage = $currentPage;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Page
   */
  public function getCurrentPage()
  {
    return $this->currentPage;
  }
  /**
   * Match results, if more than one, ordered descendingly by the confidence we
   * have that the particular intent matches the query.
   *
   * @param GoogleCloudDialogflowCxV3Match[] $matches
   */
  public function setMatches($matches)
  {
    $this->matches = $matches;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Match[]
   */
  public function getMatches()
  {
    return $this->matches;
  }
  /**
   * If natural language text was provided as input, this field will contain a
   * copy of the text.
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
  /**
   * If natural language speech audio was provided as input, this field will
   * contain the transcript for the audio.
   *
   * @param string $transcript
   */
  public function setTranscript($transcript)
  {
    $this->transcript = $transcript;
  }
  /**
   * @return string
   */
  public function getTranscript()
  {
    return $this->transcript;
  }
  /**
   * If an event was provided as input, this field will contain a copy of the
   * event name.
   *
   * @param string $triggerEvent
   */
  public function setTriggerEvent($triggerEvent)
  {
    $this->triggerEvent = $triggerEvent;
  }
  /**
   * @return string
   */
  public function getTriggerEvent()
  {
    return $this->triggerEvent;
  }
  /**
   * If an intent was provided as input, this field will contain a copy of the
   * intent identifier. Format: `projects//locations//agents//intents/`.
   *
   * @param string $triggerIntent
   */
  public function setTriggerIntent($triggerIntent)
  {
    $this->triggerIntent = $triggerIntent;
  }
  /**
   * @return string
   */
  public function getTriggerIntent()
  {
    return $this->triggerIntent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3MatchIntentResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3MatchIntentResponse');
