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

namespace Google\Service\CloudDomains;

class ConfigureContactSettingsRequest extends \Google\Collection
{
  protected $collection_key = 'contactNotices';
  /**
   * The list of contact notices that the caller acknowledges. The notices
   * needed here depend on the values specified in `contact_settings`.
   *
   * @var string[]
   */
  public $contactNotices;
  protected $contactSettingsType = ContactSettings::class;
  protected $contactSettingsDataType = '';
  /**
   * Required. The field mask describing which fields to update as a comma-
   * separated list. For example, if only the registrant contact is being
   * updated, the `update_mask` is `"registrant_contact"`.
   *
   * @var string
   */
  public $updateMask;
  /**
   * Validate the request without actually updating the contact settings.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * The list of contact notices that the caller acknowledges. The notices
   * needed here depend on the values specified in `contact_settings`.
   *
   * @param string[] $contactNotices
   */
  public function setContactNotices($contactNotices)
  {
    $this->contactNotices = $contactNotices;
  }
  /**
   * @return string[]
   */
  public function getContactNotices()
  {
    return $this->contactNotices;
  }
  /**
   * Fields of the `ContactSettings` to update.
   *
   * @param ContactSettings $contactSettings
   */
  public function setContactSettings(ContactSettings $contactSettings)
  {
    $this->contactSettings = $contactSettings;
  }
  /**
   * @return ContactSettings
   */
  public function getContactSettings()
  {
    return $this->contactSettings;
  }
  /**
   * Required. The field mask describing which fields to update as a comma-
   * separated list. For example, if only the registrant contact is being
   * updated, the `update_mask` is `"registrant_contact"`.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
  /**
   * Validate the request without actually updating the contact settings.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigureContactSettingsRequest::class, 'Google_Service_CloudDomains_ConfigureContactSettingsRequest');
