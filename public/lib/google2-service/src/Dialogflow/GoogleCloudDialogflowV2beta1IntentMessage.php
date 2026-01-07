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

class GoogleCloudDialogflowV2beta1IntentMessage extends \Google\Model
{
  /**
   * Not specified.
   */
  public const PLATFORM_PLATFORM_UNSPECIFIED = 'PLATFORM_UNSPECIFIED';
  /**
   * Facebook.
   */
  public const PLATFORM_FACEBOOK = 'FACEBOOK';
  /**
   * Slack.
   */
  public const PLATFORM_SLACK = 'SLACK';
  /**
   * Telegram.
   */
  public const PLATFORM_TELEGRAM = 'TELEGRAM';
  /**
   * Kik.
   */
  public const PLATFORM_KIK = 'KIK';
  /**
   * Skype.
   */
  public const PLATFORM_SKYPE = 'SKYPE';
  /**
   * Line.
   */
  public const PLATFORM_LINE = 'LINE';
  /**
   * Viber.
   */
  public const PLATFORM_VIBER = 'VIBER';
  /**
   * Google Assistant See [Dialogflow webhook format](https://developers.google.
   * com/assistant/actions/build/json/dialogflow-webhook-json)
   */
  public const PLATFORM_ACTIONS_ON_GOOGLE = 'ACTIONS_ON_GOOGLE';
  /**
   * Telephony Gateway.
   */
  public const PLATFORM_TELEPHONY = 'TELEPHONY';
  /**
   * Google Hangouts.
   */
  public const PLATFORM_GOOGLE_HANGOUTS = 'GOOGLE_HANGOUTS';
  protected $basicCardType = GoogleCloudDialogflowV2beta1IntentMessageBasicCard::class;
  protected $basicCardDataType = '';
  protected $browseCarouselCardType = GoogleCloudDialogflowV2beta1IntentMessageBrowseCarouselCard::class;
  protected $browseCarouselCardDataType = '';
  protected $cardType = GoogleCloudDialogflowV2beta1IntentMessageCard::class;
  protected $cardDataType = '';
  protected $carouselSelectType = GoogleCloudDialogflowV2beta1IntentMessageCarouselSelect::class;
  protected $carouselSelectDataType = '';
  protected $imageType = GoogleCloudDialogflowV2beta1IntentMessageImage::class;
  protected $imageDataType = '';
  protected $linkOutSuggestionType = GoogleCloudDialogflowV2beta1IntentMessageLinkOutSuggestion::class;
  protected $linkOutSuggestionDataType = '';
  protected $listSelectType = GoogleCloudDialogflowV2beta1IntentMessageListSelect::class;
  protected $listSelectDataType = '';
  protected $mediaContentType = GoogleCloudDialogflowV2beta1IntentMessageMediaContent::class;
  protected $mediaContentDataType = '';
  /**
   * A custom platform-specific response.
   *
   * @var array[]
   */
  public $payload;
  /**
   * Optional. The platform that this message is intended for.
   *
   * @var string
   */
  public $platform;
  protected $quickRepliesType = GoogleCloudDialogflowV2beta1IntentMessageQuickReplies::class;
  protected $quickRepliesDataType = '';
  protected $rbmCarouselRichCardType = GoogleCloudDialogflowV2beta1IntentMessageRbmCarouselCard::class;
  protected $rbmCarouselRichCardDataType = '';
  protected $rbmStandaloneRichCardType = GoogleCloudDialogflowV2beta1IntentMessageRbmStandaloneCard::class;
  protected $rbmStandaloneRichCardDataType = '';
  protected $rbmTextType = GoogleCloudDialogflowV2beta1IntentMessageRbmText::class;
  protected $rbmTextDataType = '';
  protected $simpleResponsesType = GoogleCloudDialogflowV2beta1IntentMessageSimpleResponses::class;
  protected $simpleResponsesDataType = '';
  protected $suggestionsType = GoogleCloudDialogflowV2beta1IntentMessageSuggestions::class;
  protected $suggestionsDataType = '';
  protected $tableCardType = GoogleCloudDialogflowV2beta1IntentMessageTableCard::class;
  protected $tableCardDataType = '';
  protected $telephonyPlayAudioType = GoogleCloudDialogflowV2beta1IntentMessageTelephonyPlayAudio::class;
  protected $telephonyPlayAudioDataType = '';
  protected $telephonySynthesizeSpeechType = GoogleCloudDialogflowV2beta1IntentMessageTelephonySynthesizeSpeech::class;
  protected $telephonySynthesizeSpeechDataType = '';
  protected $telephonyTransferCallType = GoogleCloudDialogflowV2beta1IntentMessageTelephonyTransferCall::class;
  protected $telephonyTransferCallDataType = '';
  protected $textType = GoogleCloudDialogflowV2beta1IntentMessageText::class;
  protected $textDataType = '';

  /**
   * Displays a basic card for Actions on Google.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageBasicCard $basicCard
   */
  public function setBasicCard(GoogleCloudDialogflowV2beta1IntentMessageBasicCard $basicCard)
  {
    $this->basicCard = $basicCard;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageBasicCard
   */
  public function getBasicCard()
  {
    return $this->basicCard;
  }
  /**
   * Browse carousel card for Actions on Google.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageBrowseCarouselCard $browseCarouselCard
   */
  public function setBrowseCarouselCard(GoogleCloudDialogflowV2beta1IntentMessageBrowseCarouselCard $browseCarouselCard)
  {
    $this->browseCarouselCard = $browseCarouselCard;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageBrowseCarouselCard
   */
  public function getBrowseCarouselCard()
  {
    return $this->browseCarouselCard;
  }
  /**
   * Displays a card.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageCard $card
   */
  public function setCard(GoogleCloudDialogflowV2beta1IntentMessageCard $card)
  {
    $this->card = $card;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageCard
   */
  public function getCard()
  {
    return $this->card;
  }
  /**
   * Displays a carousel card for Actions on Google.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageCarouselSelect $carouselSelect
   */
  public function setCarouselSelect(GoogleCloudDialogflowV2beta1IntentMessageCarouselSelect $carouselSelect)
  {
    $this->carouselSelect = $carouselSelect;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageCarouselSelect
   */
  public function getCarouselSelect()
  {
    return $this->carouselSelect;
  }
  /**
   * Displays an image.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageImage $image
   */
  public function setImage(GoogleCloudDialogflowV2beta1IntentMessageImage $image)
  {
    $this->image = $image;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageImage
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Displays a link out suggestion chip for Actions on Google.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageLinkOutSuggestion $linkOutSuggestion
   */
  public function setLinkOutSuggestion(GoogleCloudDialogflowV2beta1IntentMessageLinkOutSuggestion $linkOutSuggestion)
  {
    $this->linkOutSuggestion = $linkOutSuggestion;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageLinkOutSuggestion
   */
  public function getLinkOutSuggestion()
  {
    return $this->linkOutSuggestion;
  }
  /**
   * Displays a list card for Actions on Google.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageListSelect $listSelect
   */
  public function setListSelect(GoogleCloudDialogflowV2beta1IntentMessageListSelect $listSelect)
  {
    $this->listSelect = $listSelect;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageListSelect
   */
  public function getListSelect()
  {
    return $this->listSelect;
  }
  /**
   * The media content card for Actions on Google.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageMediaContent $mediaContent
   */
  public function setMediaContent(GoogleCloudDialogflowV2beta1IntentMessageMediaContent $mediaContent)
  {
    $this->mediaContent = $mediaContent;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageMediaContent
   */
  public function getMediaContent()
  {
    return $this->mediaContent;
  }
  /**
   * A custom platform-specific response.
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
   * Optional. The platform that this message is intended for.
   *
   * Accepted values: PLATFORM_UNSPECIFIED, FACEBOOK, SLACK, TELEGRAM, KIK,
   * SKYPE, LINE, VIBER, ACTIONS_ON_GOOGLE, TELEPHONY, GOOGLE_HANGOUTS
   *
   * @param self::PLATFORM_* $platform
   */
  public function setPlatform($platform)
  {
    $this->platform = $platform;
  }
  /**
   * @return self::PLATFORM_*
   */
  public function getPlatform()
  {
    return $this->platform;
  }
  /**
   * Displays quick replies.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageQuickReplies $quickReplies
   */
  public function setQuickReplies(GoogleCloudDialogflowV2beta1IntentMessageQuickReplies $quickReplies)
  {
    $this->quickReplies = $quickReplies;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageQuickReplies
   */
  public function getQuickReplies()
  {
    return $this->quickReplies;
  }
  /**
   * Rich Business Messaging (RBM) carousel rich card response.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageRbmCarouselCard $rbmCarouselRichCard
   */
  public function setRbmCarouselRichCard(GoogleCloudDialogflowV2beta1IntentMessageRbmCarouselCard $rbmCarouselRichCard)
  {
    $this->rbmCarouselRichCard = $rbmCarouselRichCard;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageRbmCarouselCard
   */
  public function getRbmCarouselRichCard()
  {
    return $this->rbmCarouselRichCard;
  }
  /**
   * Standalone Rich Business Messaging (RBM) rich card response.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageRbmStandaloneCard $rbmStandaloneRichCard
   */
  public function setRbmStandaloneRichCard(GoogleCloudDialogflowV2beta1IntentMessageRbmStandaloneCard $rbmStandaloneRichCard)
  {
    $this->rbmStandaloneRichCard = $rbmStandaloneRichCard;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageRbmStandaloneCard
   */
  public function getRbmStandaloneRichCard()
  {
    return $this->rbmStandaloneRichCard;
  }
  /**
   * Rich Business Messaging (RBM) text response. RBM allows businesses to send
   * enriched and branded versions of SMS. See https://jibe.google.com/business-
   * messaging.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageRbmText $rbmText
   */
  public function setRbmText(GoogleCloudDialogflowV2beta1IntentMessageRbmText $rbmText)
  {
    $this->rbmText = $rbmText;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageRbmText
   */
  public function getRbmText()
  {
    return $this->rbmText;
  }
  /**
   * Returns a voice or text-only response for Actions on Google.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageSimpleResponses $simpleResponses
   */
  public function setSimpleResponses(GoogleCloudDialogflowV2beta1IntentMessageSimpleResponses $simpleResponses)
  {
    $this->simpleResponses = $simpleResponses;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageSimpleResponses
   */
  public function getSimpleResponses()
  {
    return $this->simpleResponses;
  }
  /**
   * Displays suggestion chips for Actions on Google.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageSuggestions $suggestions
   */
  public function setSuggestions(GoogleCloudDialogflowV2beta1IntentMessageSuggestions $suggestions)
  {
    $this->suggestions = $suggestions;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageSuggestions
   */
  public function getSuggestions()
  {
    return $this->suggestions;
  }
  /**
   * Table card for Actions on Google.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageTableCard $tableCard
   */
  public function setTableCard(GoogleCloudDialogflowV2beta1IntentMessageTableCard $tableCard)
  {
    $this->tableCard = $tableCard;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageTableCard
   */
  public function getTableCard()
  {
    return $this->tableCard;
  }
  /**
   * Plays audio from a file in Telephony Gateway.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageTelephonyPlayAudio $telephonyPlayAudio
   */
  public function setTelephonyPlayAudio(GoogleCloudDialogflowV2beta1IntentMessageTelephonyPlayAudio $telephonyPlayAudio)
  {
    $this->telephonyPlayAudio = $telephonyPlayAudio;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageTelephonyPlayAudio
   */
  public function getTelephonyPlayAudio()
  {
    return $this->telephonyPlayAudio;
  }
  /**
   * Synthesizes speech in Telephony Gateway.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageTelephonySynthesizeSpeech $telephonySynthesizeSpeech
   */
  public function setTelephonySynthesizeSpeech(GoogleCloudDialogflowV2beta1IntentMessageTelephonySynthesizeSpeech $telephonySynthesizeSpeech)
  {
    $this->telephonySynthesizeSpeech = $telephonySynthesizeSpeech;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageTelephonySynthesizeSpeech
   */
  public function getTelephonySynthesizeSpeech()
  {
    return $this->telephonySynthesizeSpeech;
  }
  /**
   * Transfers the call in Telephony Gateway.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageTelephonyTransferCall $telephonyTransferCall
   */
  public function setTelephonyTransferCall(GoogleCloudDialogflowV2beta1IntentMessageTelephonyTransferCall $telephonyTransferCall)
  {
    $this->telephonyTransferCall = $telephonyTransferCall;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageTelephonyTransferCall
   */
  public function getTelephonyTransferCall()
  {
    return $this->telephonyTransferCall;
  }
  /**
   * Returns a text response.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageText $text
   */
  public function setText(GoogleCloudDialogflowV2beta1IntentMessageText $text)
  {
    $this->text = $text;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageText
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1IntentMessage::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1IntentMessage');
