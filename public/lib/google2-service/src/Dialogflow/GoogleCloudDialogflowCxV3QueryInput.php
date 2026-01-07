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

class GoogleCloudDialogflowCxV3QueryInput extends \Google\Model
{
  protected $audioType = GoogleCloudDialogflowCxV3AudioInput::class;
  protected $audioDataType = '';
  protected $dtmfType = GoogleCloudDialogflowCxV3DtmfInput::class;
  protected $dtmfDataType = '';
  protected $eventType = GoogleCloudDialogflowCxV3EventInput::class;
  protected $eventDataType = '';
  protected $intentType = GoogleCloudDialogflowCxV3IntentInput::class;
  protected $intentDataType = '';
  /**
   * Required. The language of the input. See [Language
   * Support](https://cloud.google.com/dialogflow/cx/docs/reference/language)
   * for a list of the currently supported language codes. Note that queries in
   * the same session do not necessarily need to specify the same language.
   *
   * @var string
   */
  public $languageCode;
  protected $textType = GoogleCloudDialogflowCxV3TextInput::class;
  protected $textDataType = '';
  protected $toolCallResultType = GoogleCloudDialogflowCxV3ToolCallResult::class;
  protected $toolCallResultDataType = '';

  /**
   * The natural language speech audio to be processed.
   *
   * @param GoogleCloudDialogflowCxV3AudioInput $audio
   */
  public function setAudio(GoogleCloudDialogflowCxV3AudioInput $audio)
  {
    $this->audio = $audio;
  }
  /**
   * @return GoogleCloudDialogflowCxV3AudioInput
   */
  public function getAudio()
  {
    return $this->audio;
  }
  /**
   * The DTMF event to be handled.
   *
   * @param GoogleCloudDialogflowCxV3DtmfInput $dtmf
   */
  public function setDtmf(GoogleCloudDialogflowCxV3DtmfInput $dtmf)
  {
    $this->dtmf = $dtmf;
  }
  /**
   * @return GoogleCloudDialogflowCxV3DtmfInput
   */
  public function getDtmf()
  {
    return $this->dtmf;
  }
  /**
   * The event to be triggered.
   *
   * @param GoogleCloudDialogflowCxV3EventInput $event
   */
  public function setEvent(GoogleCloudDialogflowCxV3EventInput $event)
  {
    $this->event = $event;
  }
  /**
   * @return GoogleCloudDialogflowCxV3EventInput
   */
  public function getEvent()
  {
    return $this->event;
  }
  /**
   * The intent to be triggered.
   *
   * @param GoogleCloudDialogflowCxV3IntentInput $intent
   */
  public function setIntent(GoogleCloudDialogflowCxV3IntentInput $intent)
  {
    $this->intent = $intent;
  }
  /**
   * @return GoogleCloudDialogflowCxV3IntentInput
   */
  public function getIntent()
  {
    return $this->intent;
  }
  /**
   * Required. The language of the input. See [Language
   * Support](https://cloud.google.com/dialogflow/cx/docs/reference/language)
   * for a list of the currently supported language codes. Note that queries in
   * the same session do not necessarily need to specify the same language.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * The natural language text to be processed.
   *
   * @param GoogleCloudDialogflowCxV3TextInput $text
   */
  public function setText(GoogleCloudDialogflowCxV3TextInput $text)
  {
    $this->text = $text;
  }
  /**
   * @return GoogleCloudDialogflowCxV3TextInput
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * The results of a tool executed by the client.
   *
   * @param GoogleCloudDialogflowCxV3ToolCallResult $toolCallResult
   */
  public function setToolCallResult(GoogleCloudDialogflowCxV3ToolCallResult $toolCallResult)
  {
    $this->toolCallResult = $toolCallResult;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ToolCallResult
   */
  public function getToolCallResult()
  {
    return $this->toolCallResult;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3QueryInput::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3QueryInput');
