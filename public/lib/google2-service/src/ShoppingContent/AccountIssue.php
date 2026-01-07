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

namespace Google\Service\ShoppingContent;

class AccountIssue extends \Google\Collection
{
  protected $collection_key = 'actions';
  protected $actionsType = Action::class;
  protected $actionsDataType = 'array';
  protected $impactType = AccountIssueImpact::class;
  protected $impactDataType = '';
  /**
   * Details of the issue as a pre-rendered HTML. HTML elements contain CSS
   * classes that can be used to customize the style of the content. Always
   * sanitize the HTML before embedding it directly to your application. The
   * sanitizer needs to allow basic HTML tags, such as: `div`, `span`, `p`, `a`,
   * `ul`, `li`, `table`, `tr`, `td`. For example, you can use
   * [DOMPurify](https://www.npmjs.com/package/dompurify). CSS classes: *
   * `issue-detail` - top level container for the detail of the issue *
   * `callout-banners` - section of the `issue-detail` with callout banners *
   * `callout-banner` - single callout banner, inside `callout-banners` *
   * `callout-banner-info` - callout with important information (default) *
   * `callout-banner-warning` - callout with a warning * `callout-banner-error`
   * - callout informing about an error (most severe) * `issue-content` -
   * section of the `issue-detail`, contains multiple `content-element` *
   * `content-element` - content element such as a list, link or paragraph,
   * inside `issue-content` * `root-causes` - unordered list with items
   * describing root causes of the issue, inside `issue-content` * `root-causes-
   * intro` - intro text before the `root-causes` list, inside `issue-content` *
   * `segment` - section of the text, `span` inside paragraph * `segment-
   * attribute` - section of the text that represents a product attribute, for
   * example 'image\_link' * `segment-literal` - section of the text that
   * contains a special value, for example '0-1000 kg' * `segment-bold` -
   * section of the text that should be rendered as bold * `segment-italic` -
   * section of the text that should be rendered as italic * `tooltip` - used on
   * paragraphs that should be rendered with a tooltip. A section of the text in
   * such a paragraph will have a class `tooltip-text` and is intended to be
   * shown in a mouse over dialog. If the style is not used, the `tooltip-text`
   * section would be shown on a new line, after the main part of the text. *
   * `tooltip-text` - marks a section of the text within a `tooltip`, that is
   * intended to be shown in a mouse over dialog. * `tooltip-icon` - marks a
   * section of the text within a `tooltip`, that can be replaced with a tooltip
   * icon, for example '?' or 'i'. By default, this section contains a `br` tag,
   * that is separating the main text and the tooltip text when the style is not
   * used. * `tooltip-style-question` - the tooltip shows helpful information,
   * can use the '?' as an icon. * `tooltip-style-info` - the tooltip adds
   * additional information fitting to the context, can use the 'i' as an icon.
   * * `content-moderation` - marks the paragraph that explains how the issue
   * was identified. * `new-element` - Present for new elements added to the
   * pre-rendered content in the future. To make sure that a new content element
   * does not break your style, you can hide everything with this class.
   *
   * @var string
   */
  public $prerenderedContent;
  /**
   * Pre-rendered HTML that contains a link to the external location where the
   * ODS can be requested and instructions for how to request it. HTML elements
   * contain CSS classes that can be used to customize the style of this
   * snippet. Always sanitize the HTML before embedding it directly to your
   * application. The sanitizer needs to allow basic HTML tags, such as: `div`,
   * `span`, `p`, `a`, `ul`, `li`, `table`, `tr`, `td`. For example, you can use
   * [DOMPurify](https://www.npmjs.com/package/dompurify). CSS classes: * `ods-
   * section`* - wrapper around the out-of-court dispute resolution section *
   * `ods-description`* - intro text for the out-of-court dispute resolution. It
   * may contain multiple segments and a link. * `ods-param`* - wrapper around
   * the header-value pair for parameters that merchant may need to provide
   * during the ODS process. * `ods-routing-id`* - ods param for the Routing ID.
   * * `ods-reference-id`* - ods param for the Routing ID. * `ods-param-header`*
   * - header for the ODS parameter * `ods-param-value`* - value of the ODS
   * parameter. This value should be rendered in a way that it is easy for
   * merchants to identify and copy. * `segment` - section of the text, `span`
   * inside paragraph * `segment-attribute` - section of the text that
   * represents a product attribute, for example 'image\_link' * `segment-
   * literal` - section of the text that contains a special value, for example
   * '0-1000 kg' * `segment-bold` - section of the text that should be rendered
   * as bold * `segment-italic` - section of the text that should be rendered as
   * italic * `tooltip` - used on paragraphs that should be rendered with a
   * tooltip. A section of the text in such a paragraph will have a class
   * `tooltip-text` and is intended to be shown in a mouse over dialog. If the
   * style is not used, the `tooltip-text` section would be shown on a new line,
   * after the main part of the text. * `tooltip-text` - marks a section of the
   * text within a `tooltip`, that is intended to be shown in a mouse over
   * dialog. * `tooltip-icon` - marks a section of the text within a `tooltip`,
   * that can be replaced with a tooltip icon, for example '?' or 'i'. By
   * default, this section contains a `br` tag, that is separating the main text
   * and the tooltip text when the style is not used. * `tooltip-style-question`
   * - the tooltip shows helpful information, can use the '?' as an icon. *
   * `tooltip-style-info` - the tooltip adds additional information fitting to
   * the context, can use the 'i' as an icon.
   *
   * @var string
   */
  public $prerenderedOutOfCourtDisputeSettlement;
  /**
   * Title of the issue.
   *
   * @var string
   */
  public $title;

  /**
   * A list of actionable steps that can be executed to solve the issue. An
   * example is requesting a re-review or providing arguments when merchant
   * disagrees with the issue. Actions that are supported in (your) third-party
   * application can be rendered as buttons and should be available to merchant
   * when they expand the issue.
   *
   * @param Action[] $actions
   */
  public function setActions($actions)
  {
    $this->actions = $actions;
  }
  /**
   * @return Action[]
   */
  public function getActions()
  {
    return $this->actions;
  }
  /**
   * Clarifies the severity of the issue. The summarizing message, if present,
   * should be shown right under the title for each issue. It helps merchants to
   * quickly understand the impact of the issue. The detailed breakdown helps
   * the merchant to fully understand the impact of the issue. It can be
   * rendered as dialog that opens when the merchant mouse over the summarized
   * impact statement. Issues with different severity can be styled differently.
   * They may use a different color or icon to signal the difference between
   * `ERROR`, `WARNING` and `INFO`.
   *
   * @param AccountIssueImpact $impact
   */
  public function setImpact(AccountIssueImpact $impact)
  {
    $this->impact = $impact;
  }
  /**
   * @return AccountIssueImpact
   */
  public function getImpact()
  {
    return $this->impact;
  }
  /**
   * Details of the issue as a pre-rendered HTML. HTML elements contain CSS
   * classes that can be used to customize the style of the content. Always
   * sanitize the HTML before embedding it directly to your application. The
   * sanitizer needs to allow basic HTML tags, such as: `div`, `span`, `p`, `a`,
   * `ul`, `li`, `table`, `tr`, `td`. For example, you can use
   * [DOMPurify](https://www.npmjs.com/package/dompurify). CSS classes: *
   * `issue-detail` - top level container for the detail of the issue *
   * `callout-banners` - section of the `issue-detail` with callout banners *
   * `callout-banner` - single callout banner, inside `callout-banners` *
   * `callout-banner-info` - callout with important information (default) *
   * `callout-banner-warning` - callout with a warning * `callout-banner-error`
   * - callout informing about an error (most severe) * `issue-content` -
   * section of the `issue-detail`, contains multiple `content-element` *
   * `content-element` - content element such as a list, link or paragraph,
   * inside `issue-content` * `root-causes` - unordered list with items
   * describing root causes of the issue, inside `issue-content` * `root-causes-
   * intro` - intro text before the `root-causes` list, inside `issue-content` *
   * `segment` - section of the text, `span` inside paragraph * `segment-
   * attribute` - section of the text that represents a product attribute, for
   * example 'image\_link' * `segment-literal` - section of the text that
   * contains a special value, for example '0-1000 kg' * `segment-bold` -
   * section of the text that should be rendered as bold * `segment-italic` -
   * section of the text that should be rendered as italic * `tooltip` - used on
   * paragraphs that should be rendered with a tooltip. A section of the text in
   * such a paragraph will have a class `tooltip-text` and is intended to be
   * shown in a mouse over dialog. If the style is not used, the `tooltip-text`
   * section would be shown on a new line, after the main part of the text. *
   * `tooltip-text` - marks a section of the text within a `tooltip`, that is
   * intended to be shown in a mouse over dialog. * `tooltip-icon` - marks a
   * section of the text within a `tooltip`, that can be replaced with a tooltip
   * icon, for example '?' or 'i'. By default, this section contains a `br` tag,
   * that is separating the main text and the tooltip text when the style is not
   * used. * `tooltip-style-question` - the tooltip shows helpful information,
   * can use the '?' as an icon. * `tooltip-style-info` - the tooltip adds
   * additional information fitting to the context, can use the 'i' as an icon.
   * * `content-moderation` - marks the paragraph that explains how the issue
   * was identified. * `new-element` - Present for new elements added to the
   * pre-rendered content in the future. To make sure that a new content element
   * does not break your style, you can hide everything with this class.
   *
   * @param string $prerenderedContent
   */
  public function setPrerenderedContent($prerenderedContent)
  {
    $this->prerenderedContent = $prerenderedContent;
  }
  /**
   * @return string
   */
  public function getPrerenderedContent()
  {
    return $this->prerenderedContent;
  }
  /**
   * Pre-rendered HTML that contains a link to the external location where the
   * ODS can be requested and instructions for how to request it. HTML elements
   * contain CSS classes that can be used to customize the style of this
   * snippet. Always sanitize the HTML before embedding it directly to your
   * application. The sanitizer needs to allow basic HTML tags, such as: `div`,
   * `span`, `p`, `a`, `ul`, `li`, `table`, `tr`, `td`. For example, you can use
   * [DOMPurify](https://www.npmjs.com/package/dompurify). CSS classes: * `ods-
   * section`* - wrapper around the out-of-court dispute resolution section *
   * `ods-description`* - intro text for the out-of-court dispute resolution. It
   * may contain multiple segments and a link. * `ods-param`* - wrapper around
   * the header-value pair for parameters that merchant may need to provide
   * during the ODS process. * `ods-routing-id`* - ods param for the Routing ID.
   * * `ods-reference-id`* - ods param for the Routing ID. * `ods-param-header`*
   * - header for the ODS parameter * `ods-param-value`* - value of the ODS
   * parameter. This value should be rendered in a way that it is easy for
   * merchants to identify and copy. * `segment` - section of the text, `span`
   * inside paragraph * `segment-attribute` - section of the text that
   * represents a product attribute, for example 'image\_link' * `segment-
   * literal` - section of the text that contains a special value, for example
   * '0-1000 kg' * `segment-bold` - section of the text that should be rendered
   * as bold * `segment-italic` - section of the text that should be rendered as
   * italic * `tooltip` - used on paragraphs that should be rendered with a
   * tooltip. A section of the text in such a paragraph will have a class
   * `tooltip-text` and is intended to be shown in a mouse over dialog. If the
   * style is not used, the `tooltip-text` section would be shown on a new line,
   * after the main part of the text. * `tooltip-text` - marks a section of the
   * text within a `tooltip`, that is intended to be shown in a mouse over
   * dialog. * `tooltip-icon` - marks a section of the text within a `tooltip`,
   * that can be replaced with a tooltip icon, for example '?' or 'i'. By
   * default, this section contains a `br` tag, that is separating the main text
   * and the tooltip text when the style is not used. * `tooltip-style-question`
   * - the tooltip shows helpful information, can use the '?' as an icon. *
   * `tooltip-style-info` - the tooltip adds additional information fitting to
   * the context, can use the 'i' as an icon.
   *
   * @param string $prerenderedOutOfCourtDisputeSettlement
   */
  public function setPrerenderedOutOfCourtDisputeSettlement($prerenderedOutOfCourtDisputeSettlement)
  {
    $this->prerenderedOutOfCourtDisputeSettlement = $prerenderedOutOfCourtDisputeSettlement;
  }
  /**
   * @return string
   */
  public function getPrerenderedOutOfCourtDisputeSettlement()
  {
    return $this->prerenderedOutOfCourtDisputeSettlement;
  }
  /**
   * Title of the issue.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountIssue::class, 'Google_Service_ShoppingContent_AccountIssue');
