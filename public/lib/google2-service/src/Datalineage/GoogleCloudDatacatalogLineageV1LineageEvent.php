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

class GoogleCloudDatacatalogLineageV1LineageEvent extends \Google\Collection
{
  protected $collection_key = 'links';
  /**
   * Optional. The end of the transformation which resulted in this lineage
   * event. For streaming scenarios, it should be the end of the period from
   * which the lineage is being reported.
   *
   * @var string
   */
  public $endTime;
  protected $linksType = GoogleCloudDatacatalogLineageV1EventLink::class;
  protected $linksDataType = 'array';
  /**
   * Immutable. The resource name of the lineage event. Format: `projects/{proje
   * ct}/locations/{location}/processes/{process}/runs/{run}/lineageEvents/{line
   * age_event}`. Can be specified or auto-assigned. {lineage_event} must be not
   * longer than 200 characters and only contain characters in a set:
   * `a-zA-Z0-9_-:.`
   *
   * @var string
   */
  public $name;
  /**
   * Required. The beginning of the transformation which resulted in this
   * lineage event. For streaming scenarios, it should be the beginning of the
   * period from which the lineage is being reported.
   *
   * @var string
   */
  public $startTime;

  /**
   * Optional. The end of the transformation which resulted in this lineage
   * event. For streaming scenarios, it should be the end of the period from
   * which the lineage is being reported.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Optional. List of source-target pairs. Can't contain more than 100 tuples.
   *
   * @param GoogleCloudDatacatalogLineageV1EventLink[] $links
   */
  public function setLinks($links)
  {
    $this->links = $links;
  }
  /**
   * @return GoogleCloudDatacatalogLineageV1EventLink[]
   */
  public function getLinks()
  {
    return $this->links;
  }
  /**
   * Immutable. The resource name of the lineage event. Format: `projects/{proje
   * ct}/locations/{location}/processes/{process}/runs/{run}/lineageEvents/{line
   * age_event}`. Can be specified or auto-assigned. {lineage_event} must be not
   * longer than 200 characters and only contain characters in a set:
   * `a-zA-Z0-9_-:.`
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
   * Required. The beginning of the transformation which resulted in this
   * lineage event. For streaming scenarios, it should be the beginning of the
   * period from which the lineage is being reported.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogLineageV1LineageEvent::class, 'Google_Service_Datalineage_GoogleCloudDatacatalogLineageV1LineageEvent');
