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

class GoogleAppsCardV1SelectionInput extends \Google\Collection
{
  /**
   * A set of checkboxes. Users can select one or more checkboxes.
   */
  public const TYPE_CHECK_BOX = 'CHECK_BOX';
  /**
   * A set of radio buttons. Users can select one radio button.
   */
  public const TYPE_RADIO_BUTTON = 'RADIO_BUTTON';
  /**
   * A set of switches. Users can turn on one or more switches.
   */
  public const TYPE_SWITCH = 'SWITCH';
  /**
   * A dropdown menu. Users can select one item from the menu. For Google Chat
   * apps, as part of the [Developer Preview
   * Program](https://developers.google.com/workspace/preview), you can populate
   * items using a dynamic data source and autosuggest items as users type in
   * the menu. For example, users can start typing the name of a Google Chat
   * space and the widget autosuggests the space. To dynamically populate items
   * for a dropdown menu, use one of the following types of data sources: *
   * Google Workspace data: Items are populated using data from Google
   * Workspace, such as Google Workspace users or Google Chat spaces. * External
   * data: Items are populated from an external data source outside of Google
   * Workspace. For examples of how to implement dropdown menus for Chat apps,
   * see [Add a dropdown
   * menu](https://developers.google.com/workspace/chat/design-interactive-card-
   * dialog#dropdown-menu) and [Dynamically populate drop-down
   * menus](https://developers.google.com/workspace/chat/design-interactive-
   * card-dialog#dynamic-dropdown-menu). [Google Workspace add-ons and Chat
   * apps](https://developers.google.com/workspace/extend):
   */
  public const TYPE_DROPDOWN = 'DROPDOWN';
  /**
   * A menu with a text box. Users can type and select one or more items. For
   * Google Workspace add-ons, you must populate items using a static array of
   * `SelectionItem` objects. For Google Chat apps, you can also populate items
   * using a dynamic data source and autosuggest items as users type in the
   * menu. For example, users can start typing the name of a Google Chat space
   * and the widget autosuggests the space. To dynamically populate items for a
   * multiselect menu, use one of the following types of data sources: * Google
   * Workspace data: Items are populated using data from Google Workspace, such
   * as Google Workspace users or Google Chat spaces. * External data: Items are
   * populated from an external data source outside of Google Workspace. For
   * examples of how to implement multiselect menus for Chat apps, see [Add a
   * multiselect menu](https://developers.google.com/workspace/chat/design-
   * interactive-card-dialog#multiselect-menu). [Google Workspace add-ons and
   * Chat apps](https://developers.google.com/workspace/extend):
   */
  public const TYPE_MULTI_SELECT = 'MULTI_SELECT';
  protected $collection_key = 'items';
  protected $dataSourceConfigsType = GoogleAppsCardV1DataSourceConfig::class;
  protected $dataSourceConfigsDataType = 'array';
  protected $externalDataSourceType = GoogleAppsCardV1Action::class;
  protected $externalDataSourceDataType = '';
  /**
   * Optional. Text that appears below the selection input field meant to assist
   * users by prompting them to enter a certain value. This text is always
   * visible. Available for Google Workspace add-ons that extend Google
   * Workspace Studio. Unavailable for Google Chat apps.
   *
   * @var string
   */
  public $hintText;
  protected $itemsType = GoogleAppsCardV1SelectionItem::class;
  protected $itemsDataType = 'array';
  /**
   * The text that appears above the selection input field in the user
   * interface. Specify text that helps the user enter the information your app
   * needs. For example, if users are selecting the urgency of a work ticket
   * from a drop-down menu, the label might be "Urgency" or "Select urgency".
   *
   * @var string
   */
  public $label;
  /**
   * For multiselect menus, the maximum number of items that a user can select.
   * Minimum value is 1 item. If unspecified, defaults to 3 items.
   *
   * @var int
   */
  public $multiSelectMaxSelectedItems;
  /**
   * For multiselect menus, the number of text characters that a user inputs
   * before the menu returns suggested selection items. If unset, the
   * multiselect menu uses the following default values: * If the menu uses a
   * static array of `SelectionInput` items, defaults to 0 characters and
   * immediately populates items from the array. * If the menu uses a dynamic
   * data source (`multi_select_data_source`), defaults to 3 characters before
   * querying the data source to return suggested items.
   *
   * @var int
   */
  public $multiSelectMinQueryLength;
  /**
   * Required. The name that identifies the selection input in a form input
   * event. For details about working with form inputs, see [Receive form
   * data](https://developers.google.com/workspace/chat/read-form-data).
   *
   * @var string
   */
  public $name;
  protected $onChangeActionType = GoogleAppsCardV1Action::class;
  protected $onChangeActionDataType = '';
  protected $platformDataSourceType = GoogleAppsCardV1PlatformDataSource::class;
  protected $platformDataSourceDataType = '';
  /**
   * The type of items that are displayed to users in a `SelectionInput` widget.
   * Selection types support different types of interactions. For example, users
   * can select one or more checkboxes, but they can only select one value from
   * a dropdown menu.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. The data source configs for the selection control. This field
   * provides more fine-grained control over the data source. If specified, the
   * `multi_select_max_selected_items` field, `multi_select_min_query_length`
   * field, `external_data_source` field and `platform_data_source` field are
   * ignored. Available for Google Workspace add-ons that extend Google
   * Workspace Studio. Available for the `Dropdown widget` widget in Google Chat
   * apps as part of the [Developer Preview
   * Program](https://developers.google.com/workspace/preview). For the
   * `Dropdown` widget in Google Chat apps, only one `DataSourceConfig` is
   * supported. If multiple `DataSourceConfig`s are set, only the first one is
   * used.
   *
   * @param GoogleAppsCardV1DataSourceConfig[] $dataSourceConfigs
   */
  public function setDataSourceConfigs($dataSourceConfigs)
  {
    $this->dataSourceConfigs = $dataSourceConfigs;
  }
  /**
   * @return GoogleAppsCardV1DataSourceConfig[]
   */
  public function getDataSourceConfigs()
  {
    return $this->dataSourceConfigs;
  }
  /**
   * An external data source, such as a relational database.
   *
   * @param GoogleAppsCardV1Action $externalDataSource
   */
  public function setExternalDataSource(GoogleAppsCardV1Action $externalDataSource)
  {
    $this->externalDataSource = $externalDataSource;
  }
  /**
   * @return GoogleAppsCardV1Action
   */
  public function getExternalDataSource()
  {
    return $this->externalDataSource;
  }
  /**
   * Optional. Text that appears below the selection input field meant to assist
   * users by prompting them to enter a certain value. This text is always
   * visible. Available for Google Workspace add-ons that extend Google
   * Workspace Studio. Unavailable for Google Chat apps.
   *
   * @param string $hintText
   */
  public function setHintText($hintText)
  {
    $this->hintText = $hintText;
  }
  /**
   * @return string
   */
  public function getHintText()
  {
    return $this->hintText;
  }
  /**
   * An array of selectable items. For example, an array of radio buttons or
   * checkboxes. Supports up to 100 items.
   *
   * @param GoogleAppsCardV1SelectionItem[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return GoogleAppsCardV1SelectionItem[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * The text that appears above the selection input field in the user
   * interface. Specify text that helps the user enter the information your app
   * needs. For example, if users are selecting the urgency of a work ticket
   * from a drop-down menu, the label might be "Urgency" or "Select urgency".
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * For multiselect menus, the maximum number of items that a user can select.
   * Minimum value is 1 item. If unspecified, defaults to 3 items.
   *
   * @param int $multiSelectMaxSelectedItems
   */
  public function setMultiSelectMaxSelectedItems($multiSelectMaxSelectedItems)
  {
    $this->multiSelectMaxSelectedItems = $multiSelectMaxSelectedItems;
  }
  /**
   * @return int
   */
  public function getMultiSelectMaxSelectedItems()
  {
    return $this->multiSelectMaxSelectedItems;
  }
  /**
   * For multiselect menus, the number of text characters that a user inputs
   * before the menu returns suggested selection items. If unset, the
   * multiselect menu uses the following default values: * If the menu uses a
   * static array of `SelectionInput` items, defaults to 0 characters and
   * immediately populates items from the array. * If the menu uses a dynamic
   * data source (`multi_select_data_source`), defaults to 3 characters before
   * querying the data source to return suggested items.
   *
   * @param int $multiSelectMinQueryLength
   */
  public function setMultiSelectMinQueryLength($multiSelectMinQueryLength)
  {
    $this->multiSelectMinQueryLength = $multiSelectMinQueryLength;
  }
  /**
   * @return int
   */
  public function getMultiSelectMinQueryLength()
  {
    return $this->multiSelectMinQueryLength;
  }
  /**
   * Required. The name that identifies the selection input in a form input
   * event. For details about working with form inputs, see [Receive form
   * data](https://developers.google.com/workspace/chat/read-form-data).
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
   * If specified, the form is submitted when the selection changes. If not
   * specified, you must specify a separate button that submits the form. For
   * details about working with form inputs, see [Receive form
   * data](https://developers.google.com/workspace/chat/read-form-data).
   *
   * @param GoogleAppsCardV1Action $onChangeAction
   */
  public function setOnChangeAction(GoogleAppsCardV1Action $onChangeAction)
  {
    $this->onChangeAction = $onChangeAction;
  }
  /**
   * @return GoogleAppsCardV1Action
   */
  public function getOnChangeAction()
  {
    return $this->onChangeAction;
  }
  /**
   * A data source from Google Workspace.
   *
   * @param GoogleAppsCardV1PlatformDataSource $platformDataSource
   */
  public function setPlatformDataSource(GoogleAppsCardV1PlatformDataSource $platformDataSource)
  {
    $this->platformDataSource = $platformDataSource;
  }
  /**
   * @return GoogleAppsCardV1PlatformDataSource
   */
  public function getPlatformDataSource()
  {
    return $this->platformDataSource;
  }
  /**
   * The type of items that are displayed to users in a `SelectionInput` widget.
   * Selection types support different types of interactions. For example, users
   * can select one or more checkboxes, but they can only select one value from
   * a dropdown menu.
   *
   * Accepted values: CHECK_BOX, RADIO_BUTTON, SWITCH, DROPDOWN, MULTI_SELECT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1SelectionInput::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1SelectionInput');
