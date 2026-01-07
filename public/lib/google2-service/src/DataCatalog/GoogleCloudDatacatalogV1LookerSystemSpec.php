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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1LookerSystemSpec extends \Google\Model
{
  /**
   * Name of the parent Looker Instance. Empty if it does not exist.
   *
   * @var string
   */
  public $parentInstanceDisplayName;
  /**
   * ID of the parent Looker Instance. Empty if it does not exist. Example
   * value: `someinstance.looker.com`
   *
   * @var string
   */
  public $parentInstanceId;
  /**
   * Name of the parent Model. Empty if it does not exist.
   *
   * @var string
   */
  public $parentModelDisplayName;
  /**
   * ID of the parent Model. Empty if it does not exist.
   *
   * @var string
   */
  public $parentModelId;
  /**
   * Name of the parent View. Empty if it does not exist.
   *
   * @var string
   */
  public $parentViewDisplayName;
  /**
   * ID of the parent View. Empty if it does not exist.
   *
   * @var string
   */
  public $parentViewId;

  /**
   * Name of the parent Looker Instance. Empty if it does not exist.
   *
   * @param string $parentInstanceDisplayName
   */
  public function setParentInstanceDisplayName($parentInstanceDisplayName)
  {
    $this->parentInstanceDisplayName = $parentInstanceDisplayName;
  }
  /**
   * @return string
   */
  public function getParentInstanceDisplayName()
  {
    return $this->parentInstanceDisplayName;
  }
  /**
   * ID of the parent Looker Instance. Empty if it does not exist. Example
   * value: `someinstance.looker.com`
   *
   * @param string $parentInstanceId
   */
  public function setParentInstanceId($parentInstanceId)
  {
    $this->parentInstanceId = $parentInstanceId;
  }
  /**
   * @return string
   */
  public function getParentInstanceId()
  {
    return $this->parentInstanceId;
  }
  /**
   * Name of the parent Model. Empty if it does not exist.
   *
   * @param string $parentModelDisplayName
   */
  public function setParentModelDisplayName($parentModelDisplayName)
  {
    $this->parentModelDisplayName = $parentModelDisplayName;
  }
  /**
   * @return string
   */
  public function getParentModelDisplayName()
  {
    return $this->parentModelDisplayName;
  }
  /**
   * ID of the parent Model. Empty if it does not exist.
   *
   * @param string $parentModelId
   */
  public function setParentModelId($parentModelId)
  {
    $this->parentModelId = $parentModelId;
  }
  /**
   * @return string
   */
  public function getParentModelId()
  {
    return $this->parentModelId;
  }
  /**
   * Name of the parent View. Empty if it does not exist.
   *
   * @param string $parentViewDisplayName
   */
  public function setParentViewDisplayName($parentViewDisplayName)
  {
    $this->parentViewDisplayName = $parentViewDisplayName;
  }
  /**
   * @return string
   */
  public function getParentViewDisplayName()
  {
    return $this->parentViewDisplayName;
  }
  /**
   * ID of the parent View. Empty if it does not exist.
   *
   * @param string $parentViewId
   */
  public function setParentViewId($parentViewId)
  {
    $this->parentViewId = $parentViewId;
  }
  /**
   * @return string
   */
  public function getParentViewId()
  {
    return $this->parentViewId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1LookerSystemSpec::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1LookerSystemSpec');
