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

class GoogleAppsCardV1Section extends \Google\Collection
{
  protected $collection_key = 'widgets';
  protected $collapseControlType = GoogleAppsCardV1CollapseControl::class;
  protected $collapseControlDataType = '';
  /**
   * Indicates whether this section is collapsible. Collapsible sections hide
   * some or all widgets, but users can expand the section to reveal the hidden
   * widgets by clicking **Show more**. Users can hide the widgets again by
   * clicking **Show less**. To determine which widgets are hidden, specify
   * `uncollapsibleWidgetsCount`.
   *
   * @var bool
   */
  public $collapsible;
  /**
   * Text that appears at the top of a section. Supports simple HTML formatted
   * text. For more information about formatting text, see [Formatting text in
   * Google Chat apps](https://developers.google.com/workspace/chat/format-
   * messages#card-formatting) and [Formatting text in Google Workspace add-
   * ons](https://developers.google.com/apps-script/add-
   * ons/concepts/widgets#text_formatting).
   *
   * @var string
   */
  public $header;
  /**
   * A unique ID assigned to the section that's used to identify the section to
   * be mutated. The ID has a character limit of 64 characters and should be in
   * the format of `[a-zA-Z0-9-]+`. Available for Google Workspace add-ons that
   * extend Google Workspace Studio. Unavailable for Google Chat apps.
   *
   * @var string
   */
  public $id;
  /**
   * The number of uncollapsible widgets which remain visible even when a
   * section is collapsed. For example, when a section contains five widgets and
   * the `uncollapsibleWidgetsCount` is set to `2`, the first two widgets are
   * always shown and the last three are collapsed by default. The
   * `uncollapsibleWidgetsCount` is taken into account only when `collapsible`
   * is `true`.
   *
   * @var int
   */
  public $uncollapsibleWidgetsCount;
  protected $widgetsType = GoogleAppsCardV1Widget::class;
  protected $widgetsDataType = 'array';

  /**
   * Optional. Define the expand and collapse button of the section. This button
   * will be shown only if the section is collapsible. If this field isn't set,
   * the default button is used.
   *
   * @param GoogleAppsCardV1CollapseControl $collapseControl
   */
  public function setCollapseControl(GoogleAppsCardV1CollapseControl $collapseControl)
  {
    $this->collapseControl = $collapseControl;
  }
  /**
   * @return GoogleAppsCardV1CollapseControl
   */
  public function getCollapseControl()
  {
    return $this->collapseControl;
  }
  /**
   * Indicates whether this section is collapsible. Collapsible sections hide
   * some or all widgets, but users can expand the section to reveal the hidden
   * widgets by clicking **Show more**. Users can hide the widgets again by
   * clicking **Show less**. To determine which widgets are hidden, specify
   * `uncollapsibleWidgetsCount`.
   *
   * @param bool $collapsible
   */
  public function setCollapsible($collapsible)
  {
    $this->collapsible = $collapsible;
  }
  /**
   * @return bool
   */
  public function getCollapsible()
  {
    return $this->collapsible;
  }
  /**
   * Text that appears at the top of a section. Supports simple HTML formatted
   * text. For more information about formatting text, see [Formatting text in
   * Google Chat apps](https://developers.google.com/workspace/chat/format-
   * messages#card-formatting) and [Formatting text in Google Workspace add-
   * ons](https://developers.google.com/apps-script/add-
   * ons/concepts/widgets#text_formatting).
   *
   * @param string $header
   */
  public function setHeader($header)
  {
    $this->header = $header;
  }
  /**
   * @return string
   */
  public function getHeader()
  {
    return $this->header;
  }
  /**
   * A unique ID assigned to the section that's used to identify the section to
   * be mutated. The ID has a character limit of 64 characters and should be in
   * the format of `[a-zA-Z0-9-]+`. Available for Google Workspace add-ons that
   * extend Google Workspace Studio. Unavailable for Google Chat apps.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The number of uncollapsible widgets which remain visible even when a
   * section is collapsed. For example, when a section contains five widgets and
   * the `uncollapsibleWidgetsCount` is set to `2`, the first two widgets are
   * always shown and the last three are collapsed by default. The
   * `uncollapsibleWidgetsCount` is taken into account only when `collapsible`
   * is `true`.
   *
   * @param int $uncollapsibleWidgetsCount
   */
  public function setUncollapsibleWidgetsCount($uncollapsibleWidgetsCount)
  {
    $this->uncollapsibleWidgetsCount = $uncollapsibleWidgetsCount;
  }
  /**
   * @return int
   */
  public function getUncollapsibleWidgetsCount()
  {
    return $this->uncollapsibleWidgetsCount;
  }
  /**
   * All the widgets in the section. Must contain at least one widget.
   *
   * @param GoogleAppsCardV1Widget[] $widgets
   */
  public function setWidgets($widgets)
  {
    $this->widgets = $widgets;
  }
  /**
   * @return GoogleAppsCardV1Widget[]
   */
  public function getWidgets()
  {
    return $this->widgets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1Section::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1Section');
