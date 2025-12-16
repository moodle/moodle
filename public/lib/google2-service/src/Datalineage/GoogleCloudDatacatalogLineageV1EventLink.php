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

namespace Google\Service\Datalineage;

class GoogleCloudDatacatalogLineageV1EventLink extends \Google\Model
{
  protected $sourceType = GoogleCloudDatacatalogLineageV1EntityReference::class;
  protected $sourceDataType = '';
  protected $targetType = GoogleCloudDatacatalogLineageV1EntityReference::class;
  protected $targetDataType = '';

  /**
   * Required. Reference to the source entity
   *
   * @param GoogleCloudDatacatalogLineageV1EntityReference $source
   */
  public function setSource(GoogleCloudDatacatalogLineageV1EntityReference $source)
  {
    $this->source = $source;
  }
  /**
   * @return GoogleCloudDatacatalogLineageV1EntityReference
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Required. Reference to the target entity
   *
   * @param GoogleCloudDatacatalogLineageV1EntityReference $target
   */
  public function setTarget(GoogleCloudDatacatalogLineageV1EntityReference $target)
  {
    $this->target = $target;
  }
  /**
   * @return GoogleCloudDatacatalogLineageV1EntityReference
   */
  public function getTarget()
  {
    return $this->target;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogLineageV1EventLink::class, 'Google_Service_Datalineage_GoogleCloudDatacatalogLineageV1EventLink');
