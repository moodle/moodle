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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaDataSharingSettings extends \Google\Model
{
  /**
   * Output only. Resource name. Format: accounts/{account}/dataSharingSettings
   * Example: "accounts/1000/dataSharingSettings"
   *
   * @var string
   */
  public $name;
  /**
   * Deprecated. This field is no longer used and always returns false.
   *
   * @deprecated
   * @var bool
   */
  public $sharingWithGoogleAnySalesEnabled;
  /**
   * Allows Google access to your Google Analytics account data, including
   * account usage and configuration data, product spending, and users
   * associated with your Google Analytics account, so that Google can help you
   * make the most of Google products, providing you with insights, offers,
   * recommendations, and optimization tips across Google Analytics and other
   * Google products for business. This field maps to the "Recommendations for
   * your business" field in the Google Analytics Admin UI.
   *
   * @var bool
   */
  public $sharingWithGoogleAssignedSalesEnabled;
  /**
   * Allows Google to use the data to improve other Google products or services.
   * This fields maps to the "Google products & services" field in the Google
   * Analytics Admin UI.
   *
   * @var bool
   */
  public $sharingWithGoogleProductsEnabled;
  /**
   * Allows Google technical support representatives access to your Google
   * Analytics data and account when necessary to provide service and find
   * solutions to technical issues. This field maps to the "Technical support"
   * field in the Google Analytics Admin UI.
   *
   * @var bool
   */
  public $sharingWithGoogleSupportEnabled;
  /**
   * Enable features like predictions, modeled data, and benchmarking that can
   * provide you with richer business insights when you contribute aggregated
   * measurement data. The data you share (including information about the
   * property from which it is shared) is aggregated and de-identified before
   * being used to generate business insights. This field maps to the "Modeling
   * contributions & business insights" field in the Google Analytics Admin UI.
   *
   * @var bool
   */
  public $sharingWithOthersEnabled;

  /**
   * Output only. Resource name. Format: accounts/{account}/dataSharingSettings
   * Example: "accounts/1000/dataSharingSettings"
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
   * Deprecated. This field is no longer used and always returns false.
   *
   * @deprecated
   * @param bool $sharingWithGoogleAnySalesEnabled
   */
  public function setSharingWithGoogleAnySalesEnabled($sharingWithGoogleAnySalesEnabled)
  {
    $this->sharingWithGoogleAnySalesEnabled = $sharingWithGoogleAnySalesEnabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getSharingWithGoogleAnySalesEnabled()
  {
    return $this->sharingWithGoogleAnySalesEnabled;
  }
  /**
   * Allows Google access to your Google Analytics account data, including
   * account usage and configuration data, product spending, and users
   * associated with your Google Analytics account, so that Google can help you
   * make the most of Google products, providing you with insights, offers,
   * recommendations, and optimization tips across Google Analytics and other
   * Google products for business. This field maps to the "Recommendations for
   * your business" field in the Google Analytics Admin UI.
   *
   * @param bool $sharingWithGoogleAssignedSalesEnabled
   */
  public function setSharingWithGoogleAssignedSalesEnabled($sharingWithGoogleAssignedSalesEnabled)
  {
    $this->sharingWithGoogleAssignedSalesEnabled = $sharingWithGoogleAssignedSalesEnabled;
  }
  /**
   * @return bool
   */
  public function getSharingWithGoogleAssignedSalesEnabled()
  {
    return $this->sharingWithGoogleAssignedSalesEnabled;
  }
  /**
   * Allows Google to use the data to improve other Google products or services.
   * This fields maps to the "Google products & services" field in the Google
   * Analytics Admin UI.
   *
   * @param bool $sharingWithGoogleProductsEnabled
   */
  public function setSharingWithGoogleProductsEnabled($sharingWithGoogleProductsEnabled)
  {
    $this->sharingWithGoogleProductsEnabled = $sharingWithGoogleProductsEnabled;
  }
  /**
   * @return bool
   */
  public function getSharingWithGoogleProductsEnabled()
  {
    return $this->sharingWithGoogleProductsEnabled;
  }
  /**
   * Allows Google technical support representatives access to your Google
   * Analytics data and account when necessary to provide service and find
   * solutions to technical issues. This field maps to the "Technical support"
   * field in the Google Analytics Admin UI.
   *
   * @param bool $sharingWithGoogleSupportEnabled
   */
  public function setSharingWithGoogleSupportEnabled($sharingWithGoogleSupportEnabled)
  {
    $this->sharingWithGoogleSupportEnabled = $sharingWithGoogleSupportEnabled;
  }
  /**
   * @return bool
   */
  public function getSharingWithGoogleSupportEnabled()
  {
    return $this->sharingWithGoogleSupportEnabled;
  }
  /**
   * Enable features like predictions, modeled data, and benchmarking that can
   * provide you with richer business insights when you contribute aggregated
   * measurement data. The data you share (including information about the
   * property from which it is shared) is aggregated and de-identified before
   * being used to generate business insights. This field maps to the "Modeling
   * contributions & business insights" field in the Google Analytics Admin UI.
   *
   * @param bool $sharingWithOthersEnabled
   */
  public function setSharingWithOthersEnabled($sharingWithOthersEnabled)
  {
    $this->sharingWithOthersEnabled = $sharingWithOthersEnabled;
  }
  /**
   * @return bool
   */
  public function getSharingWithOthersEnabled()
  {
    return $this->sharingWithOthersEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaDataSharingSettings::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaDataSharingSettings');
