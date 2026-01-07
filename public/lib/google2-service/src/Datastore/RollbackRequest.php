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

namespace Google\Service\Datastore;

class RollbackRequest extends \Google\Model
{
  /**
   * The ID of the database against which to make the request. '(default)' is
   * not allowed; please use empty string '' to refer the default database.
   *
   * @var string
   */
  public $databaseId;
  /**
   * Required. The transaction identifier, returned by a call to
   * Datastore.BeginTransaction.
   *
   * @var string
   */
  public $transaction;

  /**
   * The ID of the database against which to make the request. '(default)' is
   * not allowed; please use empty string '' to refer the default database.
   *
   * @param string $databaseId
   */
  public function setDatabaseId($databaseId)
  {
    $this->databaseId = $databaseId;
  }
  /**
   * @return string
   */
  public function getDatabaseId()
  {
    return $this->databaseId;
  }
  /**
   * Required. The transaction identifier, returned by a call to
   * Datastore.BeginTransaction.
   *
   * @param string $transaction
   */
  public function setTransaction($transaction)
  {
    $this->transaction = $transaction;
  }
  /**
   * @return string
   */
  public function getTransaction()
  {
    return $this->transaction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RollbackRequest::class, 'Google_Service_Datastore_RollbackRequest');
