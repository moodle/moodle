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

namespace Google\Service\CloudSearch;

class HistoryRecord extends \Google\Model
{
  protected $clientContextType = ClientContext::class;
  protected $clientContextDataType = '';
  protected $filterUpdateType = FilterUpdate::class;
  protected $filterUpdateDataType = '';
  protected $imapUpdateType = ImapUpdate::class;
  protected $imapUpdateDataType = '';
  protected $labelUpdateType = LabelUpdate::class;
  protected $labelUpdateDataType = '';
  protected $prefUpdateType = PrefUpdate::class;
  protected $prefUpdateDataType = '';
  /**
   * @var string
   */
  public $recordId;
  protected $threadUpdateType = ThreadUpdate::class;
  protected $threadUpdateDataType = '';
  protected $transactionContextType = TransactionContext::class;
  protected $transactionContextDataType = '';
  protected $txnDebugInfoType = TransactionDebugInfo::class;
  protected $txnDebugInfoDataType = '';
  /**
   * @var string
   */
  public $type;

  /**
   * @param ClientContext
   */
  public function setClientContext(ClientContext $clientContext)
  {
    $this->clientContext = $clientContext;
  }
  /**
   * @return ClientContext
   */
  public function getClientContext()
  {
    return $this->clientContext;
  }
  /**
   * @param FilterUpdate
   */
  public function setFilterUpdate(FilterUpdate $filterUpdate)
  {
    $this->filterUpdate = $filterUpdate;
  }
  /**
   * @return FilterUpdate
   */
  public function getFilterUpdate()
  {
    return $this->filterUpdate;
  }
  /**
   * @param ImapUpdate
   */
  public function setImapUpdate(ImapUpdate $imapUpdate)
  {
    $this->imapUpdate = $imapUpdate;
  }
  /**
   * @return ImapUpdate
   */
  public function getImapUpdate()
  {
    return $this->imapUpdate;
  }
  /**
   * @param LabelUpdate
   */
  public function setLabelUpdate(LabelUpdate $labelUpdate)
  {
    $this->labelUpdate = $labelUpdate;
  }
  /**
   * @return LabelUpdate
   */
  public function getLabelUpdate()
  {
    return $this->labelUpdate;
  }
  /**
   * @param PrefUpdate
   */
  public function setPrefUpdate(PrefUpdate $prefUpdate)
  {
    $this->prefUpdate = $prefUpdate;
  }
  /**
   * @return PrefUpdate
   */
  public function getPrefUpdate()
  {
    return $this->prefUpdate;
  }
  /**
   * @param string
   */
  public function setRecordId($recordId)
  {
    $this->recordId = $recordId;
  }
  /**
   * @return string
   */
  public function getRecordId()
  {
    return $this->recordId;
  }
  /**
   * @param ThreadUpdate
   */
  public function setThreadUpdate(ThreadUpdate $threadUpdate)
  {
    $this->threadUpdate = $threadUpdate;
  }
  /**
   * @return ThreadUpdate
   */
  public function getThreadUpdate()
  {
    return $this->threadUpdate;
  }
  /**
   * @param TransactionContext
   */
  public function setTransactionContext(TransactionContext $transactionContext)
  {
    $this->transactionContext = $transactionContext;
  }
  /**
   * @return TransactionContext
   */
  public function getTransactionContext()
  {
    return $this->transactionContext;
  }
  /**
   * @param TransactionDebugInfo
   */
  public function setTxnDebugInfo(TransactionDebugInfo $txnDebugInfo)
  {
    $this->txnDebugInfo = $txnDebugInfo;
  }
  /**
   * @return TransactionDebugInfo
   */
  public function getTxnDebugInfo()
  {
    return $this->txnDebugInfo;
  }
  /**
   * @param string
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HistoryRecord::class, 'Google_Service_CloudSearch_HistoryRecord');
