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

class GoogleAnalyticsAdminV1betaAccessFilterExpression extends \Google\Model
{
  protected $accessFilterType = GoogleAnalyticsAdminV1betaAccessFilter::class;
  protected $accessFilterDataType = '';
  protected $andGroupType = GoogleAnalyticsAdminV1betaAccessFilterExpressionList::class;
  protected $andGroupDataType = '';
  protected $notExpressionType = GoogleAnalyticsAdminV1betaAccessFilterExpression::class;
  protected $notExpressionDataType = '';
  protected $orGroupType = GoogleAnalyticsAdminV1betaAccessFilterExpressionList::class;
  protected $orGroupDataType = '';

  /**
   * A primitive filter. In the same FilterExpression, all of the filter's field
   * names need to be either all dimensions or all metrics.
   *
   * @param GoogleAnalyticsAdminV1betaAccessFilter $accessFilter
   */
  public function setAccessFilter(GoogleAnalyticsAdminV1betaAccessFilter $accessFilter)
  {
    $this->accessFilter = $accessFilter;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessFilter
   */
  public function getAccessFilter()
  {
    return $this->accessFilter;
  }
  /**
   * Each of the FilterExpressions in the and_group has an AND relationship.
   *
   * @param GoogleAnalyticsAdminV1betaAccessFilterExpressionList $andGroup
   */
  public function setAndGroup(GoogleAnalyticsAdminV1betaAccessFilterExpressionList $andGroup)
  {
    $this->andGroup = $andGroup;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessFilterExpressionList
   */
  public function getAndGroup()
  {
    return $this->andGroup;
  }
  /**
   * The FilterExpression is NOT of not_expression.
   *
   * @param GoogleAnalyticsAdminV1betaAccessFilterExpression $notExpression
   */
  public function setNotExpression(GoogleAnalyticsAdminV1betaAccessFilterExpression $notExpression)
  {
    $this->notExpression = $notExpression;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessFilterExpression
   */
  public function getNotExpression()
  {
    return $this->notExpression;
  }
  /**
   * Each of the FilterExpressions in the or_group has an OR relationship.
   *
   * @param GoogleAnalyticsAdminV1betaAccessFilterExpressionList $orGroup
   */
  public function setOrGroup(GoogleAnalyticsAdminV1betaAccessFilterExpressionList $orGroup)
  {
    $this->orGroup = $orGroup;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessFilterExpressionList
   */
  public function getOrGroup()
  {
    return $this->orGroup;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaAccessFilterExpression::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaAccessFilterExpression');
