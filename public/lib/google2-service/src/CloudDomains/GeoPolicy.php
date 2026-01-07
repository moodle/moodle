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

class GeoPolicy extends \Google\Collection
{
  protected $collection_key = 'item';
  /**
   * Without fencing, if health check fails for all configured items in the
   * current geo bucket, we failover to the next nearest geo bucket. With
   * fencing, if health checking is enabled, as long as some targets in the
   * current geo bucket are healthy, we return only the healthy targets.
   * However, if all targets are unhealthy, we don't failover to the next
   * nearest bucket; instead, we return all the items in the current bucket even
   * when all targets are unhealthy.
   *
   * @var bool
   */
  public $enableFencing;
  protected $itemType = GeoPolicyItem::class;
  protected $itemDataType = 'array';

  /**
   * Without fencing, if health check fails for all configured items in the
   * current geo bucket, we failover to the next nearest geo bucket. With
   * fencing, if health checking is enabled, as long as some targets in the
   * current geo bucket are healthy, we return only the healthy targets.
   * However, if all targets are unhealthy, we don't failover to the next
   * nearest bucket; instead, we return all the items in the current bucket even
   * when all targets are unhealthy.
   *
   * @param bool $enableFencing
   */
  public function setEnableFencing($enableFencing)
  {
    $this->enableFencing = $enableFencing;
  }
  /**
   * @return bool
   */
  public function getEnableFencing()
  {
    return $this->enableFencing;
  }
  /**
   * The primary geo routing configuration. If there are multiple items with the
   * same location, an error is returned instead.
   *
   * @param GeoPolicyItem[] $item
   */
  public function setItem($item)
  {
    $this->item = $item;
  }
  /**
   * @return GeoPolicyItem[]
   */
  public function getItem()
  {
    return $this->item;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GeoPolicy::class, 'Google_Service_CloudDomains_GeoPolicy');
