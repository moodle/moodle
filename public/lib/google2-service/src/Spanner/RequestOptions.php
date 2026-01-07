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

class RequestOptions extends \Google\Model
{
  /**
   * `PRIORITY_UNSPECIFIED` is equivalent to `PRIORITY_HIGH`.
   */
  public const PRIORITY_PRIORITY_UNSPECIFIED = 'PRIORITY_UNSPECIFIED';
  /**
   * This specifies that the request is low priority.
   */
  public const PRIORITY_PRIORITY_LOW = 'PRIORITY_LOW';
  /**
   * This specifies that the request is medium priority.
   */
  public const PRIORITY_PRIORITY_MEDIUM = 'PRIORITY_MEDIUM';
  /**
   * This specifies that the request is high priority.
   */
  public const PRIORITY_PRIORITY_HIGH = 'PRIORITY_HIGH';
  protected $clientContextType = ClientContext::class;
  protected $clientContextDataType = '';
  /**
   * Priority for the request.
   *
   * @var string
   */
  public $priority;
  /**
   * A per-request tag which can be applied to queries or reads, used for
   * statistics collection. Both `request_tag` and `transaction_tag` can be
   * specified for a read or query that belongs to a transaction. This field is
   * ignored for requests where it's not applicable (for example,
   * `CommitRequest`). Legal characters for `request_tag` values are all
   * printable characters (ASCII 32 - 126) and the length of a request_tag is
   * limited to 50 characters. Values that exceed this limit are truncated. Any
   * leading underscore (_) characters are removed from the string.
   *
   * @var string
   */
  public $requestTag;
  /**
   * A tag used for statistics collection about this transaction. Both
   * `request_tag` and `transaction_tag` can be specified for a read or query
   * that belongs to a transaction. The value of transaction_tag should be the
   * same for all requests belonging to the same transaction. If this request
   * doesn't belong to any transaction, `transaction_tag` is ignored. Legal
   * characters for `transaction_tag` values are all printable characters (ASCII
   * 32 - 126) and the length of a `transaction_tag` is limited to 50
   * characters. Values that exceed this limit are truncated. Any leading
   * underscore (_) characters are removed from the string.
   *
   * @var string
   */
  public $transactionTag;

  /**
   * Optional. Optional context that may be needed for some requests.
   *
   * @param ClientContext $clientContext
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
   * Priority for the request.
   *
   * Accepted values: PRIORITY_UNSPECIFIED, PRIORITY_LOW, PRIORITY_MEDIUM,
   * PRIORITY_HIGH
   *
   * @param self::PRIORITY_* $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return self::PRIORITY_*
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * A per-request tag which can be applied to queries or reads, used for
   * statistics collection. Both `request_tag` and `transaction_tag` can be
   * specified for a read or query that belongs to a transaction. This field is
   * ignored for requests where it's not applicable (for example,
   * `CommitRequest`). Legal characters for `request_tag` values are all
   * printable characters (ASCII 32 - 126) and the length of a request_tag is
   * limited to 50 characters. Values that exceed this limit are truncated. Any
   * leading underscore (_) characters are removed from the string.
   *
   * @param string $requestTag
   */
  public function setRequestTag($requestTag)
  {
    $this->requestTag = $requestTag;
  }
  /**
   * @return string
   */
  public function getRequestTag()
  {
    return $this->requestTag;
  }
  /**
   * A tag used for statistics collection about this transaction. Both
   * `request_tag` and `transaction_tag` can be specified for a read or query
   * that belongs to a transaction. The value of transaction_tag should be the
   * same for all requests belonging to the same transaction. If this request
   * doesn't belong to any transaction, `transaction_tag` is ignored. Legal
   * characters for `transaction_tag` values are all printable characters (ASCII
   * 32 - 126) and the length of a `transaction_tag` is limited to 50
   * characters. Values that exceed this limit are truncated. Any leading
   * underscore (_) characters are removed from the string.
   *
   * @param string $transactionTag
   */
  public function setTransactionTag($transactionTag)
  {
    $this->transactionTag = $transactionTag;
  }
  /**
   * @return string
   */
  public function getTransactionTag()
  {
    return $this->transactionTag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RequestOptions::class, 'Google_Service_Spanner_RequestOptions');
