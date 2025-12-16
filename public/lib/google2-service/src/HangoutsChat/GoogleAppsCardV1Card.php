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

namespace Google\Service\HangoutsChat;

class GoogleAppsCardV1Card extends \Google\Collection
{
  /**
   * Don't use. Unspecified.
   */
  public const DISPLAY_STYLE_DISPLAY_STYLE_UNSPECIFIED = 'DISPLAY_STYLE_UNSPECIFIED';
  /**
   * The header of the card appears at the bottom of the sidebar, partially
   * covering the current top card of the stack. Clicking the header pops the
   * card into the card stack. If the card has no header, a generated header is
   * used instead.
   */
  public const DISPLAY_STYLE_PEEK = 'PEEK';
  /**
   * Default value. The card is shown by replacing the view of the top card in
   * the card stack.
   */
  public const DISPLAY_STYLE_REPLACE = 'REPLACE';
  /**
   * Don't use. Unspecified.
   */
  public const SECTION_DIVIDER_STYLE_DIVIDER_STYLE_UNSPECIFIED = 'DIVIDER_STYLE_UNSPECIFIED';
  /**
   * Default option. Render a solid divider.
   */
  public const SECTION_DIVIDER_STYLE_SOLID_DIVIDER = 'SOLID_DIVIDER';
  /**
   * If set, no divider is rendered. This style completely removes the divider
   * from the layout. The result is equivalent to not adding a divider at all.
   */
  public const SECTION_DIVIDER_STYLE_NO_DIVIDER = 'NO_DIVIDER';
  protected $collection_key = 'sections';
  protected $cardActionsType = GoogleAppsCardV1CardAction::class;
  protected $cardActionsDataType = 'array';
  /**
   * In Google Workspace add-ons, sets the display properties of the
   * `peekCardHeader`. [Google Workspace add-
   * ons](https://developers.google.com/workspace/add-ons):
   *
   * @var string
   */
  public $displayStyle;
  protected $expressionDataType = GoogleAppsCardV1ExpressionData::class;
  protected $expressionDataDataType = 'array';
  protected $fixedFooterType = GoogleAppsCardV1CardFixedFooter::class;
  protected $fixedFooterDataType = '';
  protected $headerType = GoogleAppsCardV1CardHeader::class;
  protected $headerDataType = '';
  /**
   * Name of the card. Used as a card identifier in card navigation. [Google
   * Workspace add-ons](https://developers.google.com/workspace/add-ons):
   *
   * @var string
   */
  public $name;
  protected $peekCardHeaderType = GoogleAppsCardV1CardHeader::class;
  protected $peekCardHeaderDataType = '';
  /**
   * The divider style between the header, sections and footer.
   *
   * @var string
   */
  public $sectionDividerStyle;
  protected $sectionsType = GoogleAppsCardV1Section::class;
  protected $sectionsDataType = 'array';

  /**
   * The card's actions. Actions are added to the card's toolbar menu. [Google
   * Workspace add-ons](https://developers.google.com/workspace/add-ons): For
   * example, the following JSON constructs a card action menu with `Settings`
   * and `Send Feedback` options: ``` "card_actions": [ { "actionLabel":
   * "Settings", "onClick": { "action": { "functionName": "goToView",
   * "parameters": [ { "key": "viewType", "value": "SETTING" } ],
   * "loadIndicator": "LoadIndicator.SPINNER" } } }, { "actionLabel": "Send
   * Feedback", "onClick": { "openLink": { "url": "https://example.com/feedback"
   * } } } ] ```
   *
   * @param GoogleAppsCardV1CardAction[] $cardActions
   */
  public function setCardActions($cardActions)
  {
    $this->cardActions = $cardActions;
  }
  /**
   * @return GoogleAppsCardV1CardAction[]
   */
  public function getCardActions()
  {
    return $this->cardActions;
  }
  /**
   * In Google Workspace add-ons, sets the display properties of the
   * `peekCardHeader`. [Google Workspace add-
   * ons](https://developers.google.com/workspace/add-ons):
   *
   * Accepted values: DISPLAY_STYLE_UNSPECIFIED, PEEK, REPLACE
   *
   * @param self::DISPLAY_STYLE_* $displayStyle
   */
  public function setDisplayStyle($displayStyle)
  {
    $this->displayStyle = $displayStyle;
  }
  /**
   * @return self::DISPLAY_STYLE_*
   */
  public function getDisplayStyle()
  {
    return $this->displayStyle;
  }
  /**
   * The expression data for the card. Available for Google Workspace add-ons
   * that extend Google Workspace Studio. Unavailable for Google Chat apps.
   *
   * @param GoogleAppsCardV1ExpressionData[] $expressionData
   */
  public function setExpressionData($expressionData)
  {
    $this->expressionData = $expressionData;
  }
  /**
   * @return GoogleAppsCardV1ExpressionData[]
   */
  public function getExpressionData()
  {
    return $this->expressionData;
  }
  /**
   * The fixed footer shown at the bottom of this card. Setting `fixedFooter`
   * without specifying a `primaryButton` or a `secondaryButton` causes an
   * error. For Chat apps, you can use fixed footers in
   * [dialogs](https://developers.google.com/workspace/chat/dialogs), but not
   * [card messages](https://developers.google.com/workspace/chat/create-
   * messages#create). [Google Workspace add-ons and Chat
   * apps](https://developers.google.com/workspace/extend):
   *
   * @param GoogleAppsCardV1CardFixedFooter $fixedFooter
   */
  public function setFixedFooter(GoogleAppsCardV1CardFixedFooter $fixedFooter)
  {
    $this->fixedFooter = $fixedFooter;
  }
  /**
   * @return GoogleAppsCardV1CardFixedFooter
   */
  public function getFixedFooter()
  {
    return $this->fixedFooter;
  }
  /**
   * The header of the card. A header usually contains a leading image and a
   * title. Headers always appear at the top of a card.
   *
   * @param GoogleAppsCardV1CardHeader $header
   */
  public function setHeader(GoogleAppsCardV1CardHeader $header)
  {
    $this->header = $header;
  }
  /**
   * @return GoogleAppsCardV1CardHeader
   */
  public function getHeader()
  {
    return $this->header;
  }
  /**
   * Name of the card. Used as a card identifier in card navigation. [Google
   * Workspace add-ons](https://developers.google.com/workspace/add-ons):
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * When displaying contextual content, the peek card header acts as a
   * placeholder so that the user can navigate forward between the homepage
   * cards and the contextual cards. [Google Workspace add-
   * ons](https://developers.google.com/workspace/add-ons):
   *
   * @param GoogleAppsCardV1CardHeader $peekCardHeader
   */
  public function setPeekCardHeader(GoogleAppsCardV1CardHeader $peekCardHeader)
  {
    $this->peekCardHeader = $peekCardHeader;
  }
  /**
   * @return GoogleAppsCardV1CardHeader
   */
  public function getPeekCardHeader()
  {
    return $this->peekCardHeader;
  }
  /**
   * The divider style between the header, sections and footer.
   *
   * Accepted values: DIVIDER_STYLE_UNSPECIFIED, SOLID_DIVIDER, NO_DIVIDER
   *
   * @param self::SECTION_DIVIDER_STYLE_* $sectionDividerStyle
   */
  public function setSectionDividerStyle($sectionDividerStyle)
  {
    $this->sectionDividerStyle = $sectionDividerStyle;
  }
  /**
   * @return self::SECTION_DIVIDER_STYLE_*
   */
  public function getSectionDividerStyle()
  {
    return $this->sectionDividerStyle;
  }
  /**
   * Contains a collection of widgets. Each section has its own, optional
   * header. Sections are visually separated by a line divider. For an example
   * in Google Chat apps, see [Define a section of a
   * card](https://developers.google.com/workspace/chat/design-components-card-
   * dialog#define_a_section_of_a_card).
   *
   * @param GoogleAppsCardV1Section[] $sections
   */
  public function setSections($sections)
  {
    $this->sections = $sections;
  }
  /**
   * @return GoogleAppsCardV1Section[]
   */
  public function getSections()
  {
    return $this->sections;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1Card::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1Card');
