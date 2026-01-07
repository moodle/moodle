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

namespace Google\Service\Forms;

class Form extends \Google\Collection
{
  protected $collection_key = 'items';
  /**
   * Output only. The form ID.
   *
   * @var string
   */
  public $formId;
  protected $infoType = Info::class;
  protected $infoDataType = '';
  protected $itemsType = Item::class;
  protected $itemsDataType = 'array';
  /**
   * Output only. The ID of the linked Google Sheet which is accumulating
   * responses from this Form (if such a Sheet exists).
   *
   * @var string
   */
  public $linkedSheetId;
  protected $publishSettingsType = PublishSettings::class;
  protected $publishSettingsDataType = '';
  /**
   * Output only. The form URI to share with responders. This opens a page that
   * allows the user to submit responses but not edit the questions. For forms
   * that have publish_settings value set, this is the published form URI.
   *
   * @var string
   */
  public $responderUri;
  /**
   * Output only. The revision ID of the form. Used in the WriteControl in
   * update requests to identify the revision on which the changes are based.
   * The format of the revision ID may change over time, so it should be treated
   * opaquely. A returned revision ID is only guaranteed to be valid for 24
   * hours after it has been returned and cannot be shared across users. If the
   * revision ID is unchanged between calls, then the form *content* has not
   * changed. Conversely, a changed ID (for the same form and user) usually
   * means the form *content* has been updated; however, a changed ID can also
   * be due to internal factors such as ID format changes. Form content excludes
   * form metadata, including: * sharing settings (who has access to the form) *
   * publish_settings (if the form supports publishing and if it is published)
   *
   * @var string
   */
  public $revisionId;
  protected $settingsType = FormSettings::class;
  protected $settingsDataType = '';

  /**
   * Output only. The form ID.
   *
   * @param string $formId
   */
  public function setFormId($formId)
  {
    $this->formId = $formId;
  }
  /**
   * @return string
   */
  public function getFormId()
  {
    return $this->formId;
  }
  /**
   * Required. The title and description of the form.
   *
   * @param Info $info
   */
  public function setInfo(Info $info)
  {
    $this->info = $info;
  }
  /**
   * @return Info
   */
  public function getInfo()
  {
    return $this->info;
  }
  /**
   * Required. A list of the form's items, which can include section headers,
   * questions, embedded media, etc.
   *
   * @param Item[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return Item[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Output only. The ID of the linked Google Sheet which is accumulating
   * responses from this Form (if such a Sheet exists).
   *
   * @param string $linkedSheetId
   */
  public function setLinkedSheetId($linkedSheetId)
  {
    $this->linkedSheetId = $linkedSheetId;
  }
  /**
   * @return string
   */
  public function getLinkedSheetId()
  {
    return $this->linkedSheetId;
  }
  /**
   * Output only. The publishing settings for a form. This field isn't set for
   * legacy forms because they don't have the publish_settings field. All newly
   * created forms support publish settings. Forms with publish_settings value
   * set can call SetPublishSettings API to publish or unpublish the form.
   *
   * @param PublishSettings $publishSettings
   */
  public function setPublishSettings(PublishSettings $publishSettings)
  {
    $this->publishSettings = $publishSettings;
  }
  /**
   * @return PublishSettings
   */
  public function getPublishSettings()
  {
    return $this->publishSettings;
  }
  /**
   * Output only. The form URI to share with responders. This opens a page that
   * allows the user to submit responses but not edit the questions. For forms
   * that have publish_settings value set, this is the published form URI.
   *
   * @param string $responderUri
   */
  public function setResponderUri($responderUri)
  {
    $this->responderUri = $responderUri;
  }
  /**
   * @return string
   */
  public function getResponderUri()
  {
    return $this->responderUri;
  }
  /**
   * Output only. The revision ID of the form. Used in the WriteControl in
   * update requests to identify the revision on which the changes are based.
   * The format of the revision ID may change over time, so it should be treated
   * opaquely. A returned revision ID is only guaranteed to be valid for 24
   * hours after it has been returned and cannot be shared across users. If the
   * revision ID is unchanged between calls, then the form *content* has not
   * changed. Conversely, a changed ID (for the same form and user) usually
   * means the form *content* has been updated; however, a changed ID can also
   * be due to internal factors such as ID format changes. Form content excludes
   * form metadata, including: * sharing settings (who has access to the form) *
   * publish_settings (if the form supports publishing and if it is published)
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * The form's settings. This must be updated with UpdateSettingsRequest; it is
   * ignored during CreateForm and UpdateFormInfoRequest.
   *
   * @param FormSettings $settings
   */
  public function setSettings(FormSettings $settings)
  {
    $this->settings = $settings;
  }
  /**
   * @return FormSettings
   */
  public function getSettings()
  {
    return $this->settings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Form::class, 'Google_Service_Forms_Form');
