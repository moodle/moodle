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

class InsertCheckoutSettingsRequest extends \Google\Model
{
  protected $uriSettingsType = UrlSettings::class;
  protected $uriSettingsDataType = '';

  /**
   * Required. The `UrlSettings` for the request. The presence of URL settings
   * indicates `Checkout` enrollment.
   *
   * @param UrlSettings $uriSettings
   */
  public function setUriSettings(UrlSettings $uriSettings)
  {
    $this->uriSettings = $uriSettings;
  }
  /**
   * @return UrlSettings
   */
  public function getUriSettings()
  {
    return $this->uriSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InsertCheckoutSettingsRequest::class, 'Google_Service_ShoppingContent_InsertCheckoutSettingsRequest');
