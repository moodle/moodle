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

namespace Google\Service\Reports;

class UsageReport extends \Google\Collection
{
  protected $collection_key = 'parameters';
  /**
   * Output only. The date of the report request.
   *
   * @var string
   */
  public $date;
  protected $entityType = UsageReportEntity::class;
  protected $entityDataType = '';
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The type of API resource. For a usage report, the value is
   * `admin#reports#usageReport`.
   *
   * @var string
   */
  public $kind;
  protected $parametersType = UsageReportParameters::class;
  protected $parametersDataType = 'array';

  /**
   * Output only. The date of the report request.
   *
   * @param string $date
   */
  public function setDate($date)
  {
    $this->date = $date;
  }
  /**
   * @return string
   */
  public function getDate()
  {
    return $this->date;
  }
  /**
   * Output only. Information about the type of the item.
   *
   * @param UsageReportEntity $entity
   */
  public function setEntity(UsageReportEntity $entity)
  {
    $this->entity = $entity;
  }
  /**
   * @return UsageReportEntity
   */
  public function getEntity()
  {
    return $this->entity;
  }
  /**
   * ETag of the resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The type of API resource. For a usage report, the value is
   * `admin#reports#usageReport`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Output only. Parameter value pairs for various applications. For the Entity
   * Usage Report parameters and values, see [the Entity Usage parameters refere
   * nce](https://developers.google.com/workspace/admin/reports/v1/reference/usa
   * ge-ref-appendix-a/entities).
   *
   * @param UsageReportParameters[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return UsageReportParameters[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsageReport::class, 'Google_Service_Reports_UsageReport');
