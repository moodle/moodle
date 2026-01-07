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

namespace Google\Service\AndroidEnterprise;

class AdministratorWebTokenSpec extends \Google\Collection
{
  protected $collection_key = 'permission';
  protected $managedConfigurationsType = AdministratorWebTokenSpecManagedConfigurations::class;
  protected $managedConfigurationsDataType = '';
  /**
   * The URI of the parent frame hosting the iframe. To prevent XSS, the iframe
   * may not be hosted at other URIs. This URI must be https. Use whitespaces to
   * separate multiple parent URIs.
   *
   * @var string
   */
  public $parent;
  /**
   * Deprecated. Use PlaySearch.approveApps.
   *
   * @var string[]
   */
  public $permission;
  protected $playSearchType = AdministratorWebTokenSpecPlaySearch::class;
  protected $playSearchDataType = '';
  protected $privateAppsType = AdministratorWebTokenSpecPrivateApps::class;
  protected $privateAppsDataType = '';
  protected $storeBuilderType = AdministratorWebTokenSpecStoreBuilder::class;
  protected $storeBuilderDataType = '';
  protected $webAppsType = AdministratorWebTokenSpecWebApps::class;
  protected $webAppsDataType = '';
  protected $zeroTouchType = AdministratorWebTokenSpecZeroTouch::class;
  protected $zeroTouchDataType = '';

  /**
   * Options for displaying the Managed Configuration page.
   *
   * @param AdministratorWebTokenSpecManagedConfigurations $managedConfigurations
   */
  public function setManagedConfigurations(AdministratorWebTokenSpecManagedConfigurations $managedConfigurations)
  {
    $this->managedConfigurations = $managedConfigurations;
  }
  /**
   * @return AdministratorWebTokenSpecManagedConfigurations
   */
  public function getManagedConfigurations()
  {
    return $this->managedConfigurations;
  }
  /**
   * The URI of the parent frame hosting the iframe. To prevent XSS, the iframe
   * may not be hosted at other URIs. This URI must be https. Use whitespaces to
   * separate multiple parent URIs.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Deprecated. Use PlaySearch.approveApps.
   *
   * @param string[] $permission
   */
  public function setPermission($permission)
  {
    $this->permission = $permission;
  }
  /**
   * @return string[]
   */
  public function getPermission()
  {
    return $this->permission;
  }
  /**
   * Options for displaying the managed Play Search apps page.
   *
   * @param AdministratorWebTokenSpecPlaySearch $playSearch
   */
  public function setPlaySearch(AdministratorWebTokenSpecPlaySearch $playSearch)
  {
    $this->playSearch = $playSearch;
  }
  /**
   * @return AdministratorWebTokenSpecPlaySearch
   */
  public function getPlaySearch()
  {
    return $this->playSearch;
  }
  /**
   * Options for displaying the Private Apps page.
   *
   * @param AdministratorWebTokenSpecPrivateApps $privateApps
   */
  public function setPrivateApps(AdministratorWebTokenSpecPrivateApps $privateApps)
  {
    $this->privateApps = $privateApps;
  }
  /**
   * @return AdministratorWebTokenSpecPrivateApps
   */
  public function getPrivateApps()
  {
    return $this->privateApps;
  }
  /**
   * Options for displaying the Organize apps page.
   *
   * @param AdministratorWebTokenSpecStoreBuilder $storeBuilder
   */
  public function setStoreBuilder(AdministratorWebTokenSpecStoreBuilder $storeBuilder)
  {
    $this->storeBuilder = $storeBuilder;
  }
  /**
   * @return AdministratorWebTokenSpecStoreBuilder
   */
  public function getStoreBuilder()
  {
    return $this->storeBuilder;
  }
  /**
   * Options for displaying the Web Apps page.
   *
   * @param AdministratorWebTokenSpecWebApps $webApps
   */
  public function setWebApps(AdministratorWebTokenSpecWebApps $webApps)
  {
    $this->webApps = $webApps;
  }
  /**
   * @return AdministratorWebTokenSpecWebApps
   */
  public function getWebApps()
  {
    return $this->webApps;
  }
  /**
   * Options for displaying the Zero Touch page.
   *
   * @param AdministratorWebTokenSpecZeroTouch $zeroTouch
   */
  public function setZeroTouch(AdministratorWebTokenSpecZeroTouch $zeroTouch)
  {
    $this->zeroTouch = $zeroTouch;
  }
  /**
   * @return AdministratorWebTokenSpecZeroTouch
   */
  public function getZeroTouch()
  {
    return $this->zeroTouch;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdministratorWebTokenSpec::class, 'Google_Service_AndroidEnterprise_AdministratorWebTokenSpec');
