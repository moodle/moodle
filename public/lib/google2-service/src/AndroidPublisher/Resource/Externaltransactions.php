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

namespace Google\Service\AndroidPublisher\Resource;

use Google\Service\AndroidPublisher\ExternalTransaction;
use Google\Service\AndroidPublisher\RefundExternalTransactionRequest;

/**
 * The "externaltransactions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $androidpublisherService = new Google\Service\AndroidPublisher(...);
 *   $externaltransactions = $androidpublisherService->externaltransactions;
 *  </code>
 */
class Externaltransactions extends \Google\Service\Resource
{
  /**
   * Creates a new external transaction.
   * (externaltransactions.createexternaltransaction)
   *
   * @param string $parent Required. The parent resource where this external
   * transaction will be created. Format: applications/{package_name}
   * @param ExternalTransaction $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string externalTransactionId Required. The id to use for the
   * external transaction. Must be unique across all other transactions for the
   * app. This value should be 1-63 characters and valid characters are
   * /a-zA-Z0-9_-/. Do not use this field to store any Personally Identifiable
   * Information (PII) such as emails. Attempting to store PII in this field may
   * result in requests being blocked.
   * @return ExternalTransaction
   * @throws \Google\Service\Exception
   */
  public function createexternaltransaction($parent, ExternalTransaction $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('createexternaltransaction', [$params], ExternalTransaction::class);
  }
  /**
   * Gets an existing external transaction.
   * (externaltransactions.getexternaltransaction)
   *
   * @param string $name Required. The name of the external transaction to
   * retrieve. Format:
   * applications/{package_name}/externalTransactions/{external_transaction}
   * @param array $optParams Optional parameters.
   * @return ExternalTransaction
   * @throws \Google\Service\Exception
   */
  public function getexternaltransaction($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getexternaltransaction', [$params], ExternalTransaction::class);
  }
  /**
   * Refunds or partially refunds an existing external transaction.
   * (externaltransactions.refundexternaltransaction)
   *
   * @param string $name Required. The name of the external transaction that will
   * be refunded. Format:
   * applications/{package_name}/externalTransactions/{external_transaction}
   * @param RefundExternalTransactionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ExternalTransaction
   * @throws \Google\Service\Exception
   */
  public function refundexternaltransaction($name, RefundExternalTransactionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('refundexternaltransaction', [$params], ExternalTransaction::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Externaltransactions::class, 'Google_Service_AndroidPublisher_Resource_Externaltransactions');
