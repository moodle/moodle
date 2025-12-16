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

namespace Google\Service\CloudRedis;

class ClusterEndpoint extends \Google\Collection
{
  protected $collection_key = 'connections';
  protected $connectionsType = ConnectionDetail::class;
  protected $connectionsDataType = 'array';

  /**
   * Required. A group of PSC connections. They are created in the same VPC
   * network, one for each service attachment in the cluster.
   *
   * @param ConnectionDetail[] $connections
   */
  public function setConnections($connections)
  {
    $this->connections = $connections;
  }
  /**
   * @return ConnectionDetail[]
   */
  public function getConnections()
  {
    return $this->connections;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterEndpoint::class, 'Google_Service_CloudRedis_ClusterEndpoint');
