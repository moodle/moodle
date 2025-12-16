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

class MaterializedView extends \Google\Model
{
  protected $clusterStatesType = GoogleBigtableAdminV2MaterializedViewClusterState::class;
  protected $clusterStatesDataType = 'map';
  /**
   * Set to true to make the MaterializedView protected against deletion. Views:
   * `SCHEMA_VIEW`, `REPLICATION_VIEW`, `FULL`.
   *
   * @var bool
   */
  public $deletionProtection;
  /**
   * Optional. The etag for this materialized view. This may be sent on update
   * requests to ensure that the client has an up-to-date value before
   * proceeding. The server returns an ABORTED error on a mismatched etag.
   * Views: `SCHEMA_VIEW`, `REPLICATION_VIEW`, `FULL`.
   *
   * @var string
   */
  public $etag;
  /**
   * Identifier. The unique name of the materialized view. Format: `projects/{pr
   * oject}/instances/{instance}/materializedViews/{materialized_view}` Views:
   * `SCHEMA_VIEW`, `REPLICATION_VIEW`, `FULL`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Immutable. The materialized view's select query. Views:
   * `SCHEMA_VIEW`, `FULL`.
   *
   * @var string
   */
  public $query;

  /**
   * Output only. Map from cluster ID to per-cluster materialized view state. If
   * it could not be determined whether or not the materialized view has data in
   * a particular cluster (for example, if its zone is unavailable), then there
   * will be an entry for the cluster with `STATE_NOT_KNOWN` state. Views:
   * `REPLICATION_VIEW`, `FULL`.
   *
   * @param GoogleBigtableAdminV2MaterializedViewClusterState[] $clusterStates
   */
  public function setClusterStates($clusterStates)
  {
    $this->clusterStates = $clusterStates;
  }
  /**
   * @return GoogleBigtableAdminV2MaterializedViewClusterState[]
   */
  public function getClusterStates()
  {
    return $this->clusterStates;
  }
  /**
   * Set to true to make the MaterializedView protected against deletion. Views:
   * `SCHEMA_VIEW`, `REPLICATION_VIEW`, `FULL`.
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
   * Optional. The etag for this materialized view. This may be sent on update
   * requests to ensure that the client has an up-to-date value before
   * proceeding. The server returns an ABORTED error on a mismatched etag.
   * Views: `SCHEMA_VIEW`, `REPLICATION_VIEW`, `FULL`.
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
   * Identifier. The unique name of the materialized view. Format: `projects/{pr
   * oject}/instances/{instance}/materializedViews/{materialized_view}` Views:
   * `SCHEMA_VIEW`, `REPLICATION_VIEW`, `FULL`.
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
   * Required. Immutable. The materialized view's select query. Views:
   * `SCHEMA_VIEW`, `FULL`.
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
class_alias(MaterializedView::class, 'Google_Service_BigtableAdmin_MaterializedView');
