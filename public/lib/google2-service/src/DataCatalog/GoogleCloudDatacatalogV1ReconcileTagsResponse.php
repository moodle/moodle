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

class GoogleCloudDatacatalogV1ReconcileTagsResponse extends \Google\Model
{
  /**
   * Number of tags created in the request.
   *
   * @var string
   */
  public $createdTagsCount;
  /**
   * Number of tags deleted in the request.
   *
   * @var string
   */
  public $deletedTagsCount;
  /**
   * Number of tags updated in the request.
   *
   * @var string
   */
  public $updatedTagsCount;

  /**
   * Number of tags created in the request.
   *
   * @param string $createdTagsCount
   */
  public function setCreatedTagsCount($createdTagsCount)
  {
    $this->createdTagsCount = $createdTagsCount;
  }
  /**
   * @return string
   */
  public function getCreatedTagsCount()
  {
    return $this->createdTagsCount;
  }
  /**
   * Number of tags deleted in the request.
   *
   * @param string $deletedTagsCount
   */
  public function setDeletedTagsCount($deletedTagsCount)
  {
    $this->deletedTagsCount = $deletedTagsCount;
  }
  /**
   * @return string
   */
  public function getDeletedTagsCount()
  {
    return $this->deletedTagsCount;
  }
  /**
   * Number of tags updated in the request.
   *
   * @param string $updatedTagsCount
   */
  public function setUpdatedTagsCount($updatedTagsCount)
  {
    $this->updatedTagsCount = $updatedTagsCount;
  }
  /**
   * @return string
   */
  public function getUpdatedTagsCount()
  {
    return $this->updatedTagsCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1ReconcileTagsResponse::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1ReconcileTagsResponse');
