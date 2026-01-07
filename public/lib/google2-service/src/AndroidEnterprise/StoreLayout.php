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

class StoreLayout extends \Google\Model
{
  public const STORE_LAYOUT_TYPE_unknown = 'unknown';
  public const STORE_LAYOUT_TYPE_basic = 'basic';
  public const STORE_LAYOUT_TYPE_custom = 'custom';
  /**
   * The ID of the store page to be used as the homepage. The homepage is the
   * first page shown in the managed Google Play Store. Not specifying a
   * homepage is equivalent to setting the store layout type to "basic".
   *
   * @var string
   */
  public $homepageId;
  /**
   * The store layout type. By default, this value is set to "basic" if the
   * homepageId field is not set, and to "custom" otherwise. If set to "basic",
   * the layout will consist of all approved apps that have been whitelisted for
   * the user.
   *
   * @var string
   */
  public $storeLayoutType;

  /**
   * The ID of the store page to be used as the homepage. The homepage is the
   * first page shown in the managed Google Play Store. Not specifying a
   * homepage is equivalent to setting the store layout type to "basic".
   *
   * @param string $homepageId
   */
  public function setHomepageId($homepageId)
  {
    $this->homepageId = $homepageId;
  }
  /**
   * @return string
   */
  public function getHomepageId()
  {
    return $this->homepageId;
  }
  /**
   * The store layout type. By default, this value is set to "basic" if the
   * homepageId field is not set, and to "custom" otherwise. If set to "basic",
   * the layout will consist of all approved apps that have been whitelisted for
   * the user.
   *
   * Accepted values: unknown, basic, custom
   *
   * @param self::STORE_LAYOUT_TYPE_* $storeLayoutType
   */
  public function setStoreLayoutType($storeLayoutType)
  {
    $this->storeLayoutType = $storeLayoutType;
  }
  /**
   * @return self::STORE_LAYOUT_TYPE_*
   */
  public function getStoreLayoutType()
  {
    return $this->storeLayoutType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StoreLayout::class, 'Google_Service_AndroidEnterprise_StoreLayout');
