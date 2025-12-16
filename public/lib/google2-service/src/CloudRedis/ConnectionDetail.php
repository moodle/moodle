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

class ConnectionDetail extends \Google\Model
{
  protected $pscAutoConnectionType = PscAutoConnection::class;
  protected $pscAutoConnectionDataType = '';
  protected $pscConnectionType = PscConnection::class;
  protected $pscConnectionDataType = '';

  /**
   * Detailed information of a PSC connection that is created through service
   * connectivity automation.
   *
   * @param PscAutoConnection $pscAutoConnection
   */
  public function setPscAutoConnection(PscAutoConnection $pscAutoConnection)
  {
    $this->pscAutoConnection = $pscAutoConnection;
  }
  /**
   * @return PscAutoConnection
   */
  public function getPscAutoConnection()
  {
    return $this->pscAutoConnection;
  }
  /**
   * Detailed information of a PSC connection that is created by the customer
   * who owns the cluster.
   *
   * @param PscConnection $pscConnection
   */
  public function setPscConnection(PscConnection $pscConnection)
  {
    $this->pscConnection = $pscConnection;
  }
  /**
   * @return PscConnection
   */
  public function getPscConnection()
  {
    return $this->pscConnection;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConnectionDetail::class, 'Google_Service_CloudRedis_ConnectionDetail');
