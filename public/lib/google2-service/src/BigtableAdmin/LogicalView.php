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

namespace Google\Service\BigtableAdmin;

class LogicalView extends \Google\Model
{
  /**
   * Optional. Set to true to make the LogicalView protected against deletion.
   *
   * @var bool
   */
  public $deletionProtection;
  /**
   * Optional. The etag for this logical view. This may be sent on update
   * requests to ensure that the client has an up-to-date value before
   * proceeding. The server returns an ABORTED error on a mismatched etag.
   *
   * @var string
   */
  public $etag;
  /**
   * Identifier. The unique name of the logical view. Format:
   * `projects/{project}/instances/{instance}/logicalViews/{logical_view}`
   *
   * @var string
   */
  public $name;
  /**
   * Required. The logical view's select query.
   *
   * @var string
   */
  public $query;

  /**
   * Optional. Set to true to make the LogicalView protected against deletion.
   *
   * @param bool $deletionProtection
   */
  public function setDeletionProtection($deletionProtection)
  {
    $this->deletionProtection = $deletionProtection;
  }
  /**
   * @return bool
   */
  public function getDeletionProtection()
  {
    return $this->deletionProtection;
  }
  /**
   * Optional. The etag for this logical view. This may be sent on update
   * requests to ensure that the client has an up-to-date value before
   * proceeding. The server returns an ABORTED error on a mismatched etag.
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
   * Identifier. The unique name of the logical view. Format:
   * `projects/{project}/instances/{instance}/logicalViews/{logical_view}`
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
   * Required. The logical view's select query.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LogicalView::class, 'Google_Service_BigtableAdmin_LogicalView');
