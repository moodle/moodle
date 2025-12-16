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

class GoogleAppsCardV1CarouselCard extends \Google\Collection
{
  protected $collection_key = 'widgets';
  protected $footerWidgetsType = GoogleAppsCardV1NestedWidget::class;
  protected $footerWidgetsDataType = 'array';
  protected $widgetsType = GoogleAppsCardV1NestedWidget::class;
  protected $widgetsDataType = 'array';

  /**
   * A list of widgets displayed at the bottom of the carousel card. The widgets
   * are displayed in the order that they are specified.
   *
   * @param GoogleAppsCardV1NestedWidget[] $footerWidgets
   */
  public function setFooterWidgets($footerWidgets)
  {
    $this->footerWidgets = $footerWidgets;
  }
  /**
   * @return GoogleAppsCardV1NestedWidget[]
   */
  public function getFooterWidgets()
  {
    return $this->footerWidgets;
  }
  /**
   * A list of widgets displayed in the carousel card. The widgets are displayed
   * in the order that they are specified.
   *
   * @param GoogleAppsCardV1NestedWidget[] $widgets
   */
  public function setWidgets($widgets)
  {
    $this->widgets = $widgets;
  }
  /**
   * @return GoogleAppsCardV1NestedWidget[]
   */
  public function getWidgets()
  {
    return $this->widgets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1CarouselCard::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1CarouselCard');
