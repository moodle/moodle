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

namespace Google\Service\Spanner;

class TransactionSelector extends \Google\Model
{
  protected $beginType = TransactionOptions::class;
  protected $beginDataType = '';
  /**
   * Execute the read or SQL query in a previously-started transaction.
   *
   * @var string
   */
  public $id;
  protected $singleUseType = TransactionOptions::class;
  protected $singleUseDataType = '';

  /**
   * Begin a new transaction and execute this read or SQL query in it. The
   * transaction ID of the new transaction is returned in
   * ResultSetMetadata.transaction, which is a Transaction.
   *
   * @param TransactionOptions $begin
   */
  public function setBegin(TransactionOptions $begin)
  {
    $this->begin = $begin;
  }
  /**
   * @return TransactionOptions
   */
  public function getBegin()
  {
    return $this->begin;
  }
  /**
   * Execute the read or SQL query in a previously-started transaction.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Execute the read or SQL query in a temporary transaction. This is the most
   * efficient way to execute a transaction that consists of a single SQL query.
   *
   * @param TransactionOptions $singleUse
   */
  public function setSingleUse(TransactionOptions $singleUse)
  {
    $this->singleUse = $singleUse;
  }
  /**
   * @return TransactionOptions
   */
  public function getSingleUse()
  {
    return $this->singleUse;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransactionSelector::class, 'Google_Service_Spanner_TransactionSelector');
