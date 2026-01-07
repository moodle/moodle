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

class GoogleCloudDialogflowV2beta1ResponseMessage extends \Google\Model
{
  protected $endInteractionType = GoogleCloudDialogflowV2beta1ResponseMessageEndInteraction::class;
  protected $endInteractionDataType = '';
  protected $liveAgentHandoffType = GoogleCloudDialogflowV2beta1ResponseMessageLiveAgentHandoff::class;
  protected $liveAgentHandoffDataType = '';
  protected $mixedAudioType = GoogleCloudDialogflowV2beta1ResponseMessageMixedAudio::class;
  protected $mixedAudioDataType = '';
  /**
   * Returns a response containing a custom, platform-specific payload.
   *
   * @var array[]
   */
  public $payload;
  protected $telephonyTransferCallType = GoogleCloudDialogflowV2beta1ResponseMessageTelephonyTransferCall::class;
  protected $telephonyTransferCallDataType = '';
  protected $textType = GoogleCloudDialogflowV2beta1ResponseMessageText::class;
  protected $textDataType = '';

  /**
   * A signal that indicates the interaction with the Dialogflow agent has
   * ended.
   *
   * @param GoogleCloudDialogflowV2beta1ResponseMessageEndInteraction $endInteraction
   */
  public function setEndInteraction(GoogleCloudDialogflowV2beta1ResponseMessageEndInteraction $endInteraction)
  {
    $this->endInteraction = $endInteraction;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1ResponseMessageEndInteraction
   */
  public function getEndInteraction()
  {
    return $this->endInteraction;
  }
  /**
   * Hands off conversation to a live agent.
   *
   * @param GoogleCloudDialogflowV2beta1ResponseMessageLiveAgentHandoff $liveAgentHandoff
   */
  public function setLiveAgentHandoff(GoogleCloudDialogflowV2beta1ResponseMessageLiveAgentHandoff $liveAgentHandoff)
  {
    $this->liveAgentHandoff = $liveAgentHandoff;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1ResponseMessageLiveAgentHandoff
   */
  public function getLiveAgentHandoff()
  {
    return $this->liveAgentHandoff;
  }
  /**
   * An audio response message composed of both the synthesized Dialogflow agent
   * responses and the audios hosted in places known to the client.
   *
   * @param GoogleCloudDialogflowV2beta1ResponseMessageMixedAudio $mixedAudio
   */
  public function setMixedAudio(GoogleCloudDialogflowV2beta1ResponseMessageMixedAudio $mixedAudio)
  {
    $this->mixedAudio = $mixedAudio;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1ResponseMessageMixedAudio
   */
  public function getMixedAudio()
  {
    return $this->mixedAudio;
  }
  /**
   * Returns a response containing a custom, platform-specific payload.
   *
   * @param array[] $payload
   */
  public function setPayload($payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return array[]
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * A signal that the client should transfer the phone call connected to this
   * agent to a third-party endpoint.
   *
   * @param GoogleCloudDialogflowV2beta1ResponseMessageTelephonyTransferCall $telephonyTransferCall
   */
  public function setTelephonyTransferCall(GoogleCloudDialogflowV2beta1ResponseMessageTelephonyTransferCall $telephonyTransferCall)
  {
    $this->telephonyTransferCall = $telephonyTransferCall;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1ResponseMessageTelephonyTransferCall
   */
  public function getTelephonyTransferCall()
  {
    return $this->telephonyTransferCall;
  }
  /**
   * Returns a text response.
   *
   * @param GoogleCloudDialogflowV2beta1ResponseMessageText $text
   */
  public function setText(GoogleCloudDialogflowV2beta1ResponseMessageText $text)
  {
    $this->text = $text;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1ResponseMessageText
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1ResponseMessage::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1ResponseMessage');
