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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2PurgeProductsRequest extends \Google\Model
{
  /**
   * Required. The filter string to specify the products to be deleted with a
   * length limit of 5,000 characters. Empty string filter is not allowed. "*"
   * implies delete all items in a branch. The eligible fields for filtering
   * are: * `availability`: Double quoted Product.availability string. *
   * `create_time` : in ISO 8601 "zulu" format. Supported syntax: * Comparators
   * (">", "<", ">=", "<=", "="). Examples: * create_time <=
   * "2015-02-13T17:05:46Z" * availability = "IN_STOCK" * Conjunctions ("AND")
   * Examples: * create_time <= "2015-02-13T17:05:46Z" AND availability =
   * "PREORDER" * Disjunctions ("OR") Examples: * create_time <=
   * "2015-02-13T17:05:46Z" OR availability = "IN_STOCK" * Can support nested
   * queries. Examples: * (create_time <= "2015-02-13T17:05:46Z" AND
   * availability = "PREORDER") OR (create_time >= "2015-02-14T13:03:32Z" AND
   * availability = "IN_STOCK") * Filter Limits: * Filter should not contain
   * more than 6 conditions. * Max nesting depth should not exceed 2 levels.
   * Examples queries: * Delete back order products created before a timestamp.
   * create_time <= "2015-02-13T17:05:46Z" OR availability = "BACKORDER"
   *
   * @var string
   */
  public $filter;
  /**
   * Actually perform the purge. If `force` is set to false, the method will
   * return the expected purge count without deleting any products.
   *
   * @var bool
   */
  public $force;

  /**
   * Required. The filter string to specify the products to be deleted with a
   * length limit of 5,000 characters. Empty string filter is not allowed. "*"
   * implies delete all items in a branch. The eligible fields for filtering
   * are: * `availability`: Double quoted Product.availability string. *
   * `create_time` : in ISO 8601 "zulu" format. Supported syntax: * Comparators
   * (">", "<", ">=", "<=", "="). Examples: * create_time <=
   * "2015-02-13T17:05:46Z" * availability = "IN_STOCK" * Conjunctions ("AND")
   * Examples: * create_time <= "2015-02-13T17:05:46Z" AND availability =
   * "PREORDER" * Disjunctions ("OR") Examples: * create_time <=
   * "2015-02-13T17:05:46Z" OR availability = "IN_STOCK" * Can support nested
   * queries. Examples: * (create_time <= "2015-02-13T17:05:46Z" AND
   * availability = "PREORDER") OR (create_time >= "2015-02-14T13:03:32Z" AND
   * availability = "IN_STOCK") * Filter Limits: * Filter should not contain
   * more than 6 conditions. * Max nesting depth should not exceed 2 levels.
   * Examples queries: * Delete back order products created before a timestamp.
   * create_time <= "2015-02-13T17:05:46Z" OR availability = "BACKORDER"
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Actually perform the purge. If `force` is set to false, the method will
   * return the expected purge count without deleting any products.
   *
   * @param bool $force
   */
  public function setForce($force)
  {
    $this->force = $force;
  }
  /**
   * @return bool
   */
  public function getForce()
  {
    return $this->force;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2PurgeProductsRequest::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2PurgeProductsRequest');
