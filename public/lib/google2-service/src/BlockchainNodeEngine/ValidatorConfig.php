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

namespace Google\Service\BlockchainNodeEngine;

class ValidatorConfig extends \Google\Collection
{
  protected $collection_key = 'mevRelayUrls';
  /**
   * An Ethereum address which the beacon client will send fee rewards to if no
   * recipient is configured in the validator client. See https://lighthouse-
   * book.sigmaprime.io/suggested-fee-recipient.html or
   * https://docs.prylabs.network/docs/execution-node/fee-recipient for examples
   * of how this is used. Note that while this is often described as
   * "suggested", as we run the execution node we can trust the execution node,
   * and therefore this is considered enforced.
   *
   * @var string
   */
  public $beaconFeeRecipient;
  /**
   * Immutable. When true, deploys a GCP-managed validator client alongside the
   * beacon client.
   *
   * @deprecated
   * @var bool
   */
  public $managedValidatorClient;
  /**
   * URLs for MEV-relay services to use for block building. When set, a GCP-
   * managed MEV-boost service is configured on the beacon client.
   *
   * @var string[]
   */
  public $mevRelayUrls;

  /**
   * An Ethereum address which the beacon client will send fee rewards to if no
   * recipient is configured in the validator client. See https://lighthouse-
   * book.sigmaprime.io/suggested-fee-recipient.html or
   * https://docs.prylabs.network/docs/execution-node/fee-recipient for examples
   * of how this is used. Note that while this is often described as
   * "suggested", as we run the execution node we can trust the execution node,
   * and therefore this is considered enforced.
   *
   * @param string $beaconFeeRecipient
   */
  public function setBeaconFeeRecipient($beaconFeeRecipient)
  {
    $this->beaconFeeRecipient = $beaconFeeRecipient;
  }
  /**
   * @return string
   */
  public function getBeaconFeeRecipient()
  {
    return $this->beaconFeeRecipient;
  }
  /**
   * Immutable. When true, deploys a GCP-managed validator client alongside the
   * beacon client.
   *
   * @deprecated
   * @param bool $managedValidatorClient
   */
  public function setManagedValidatorClient($managedValidatorClient)
  {
    $this->managedValidatorClient = $managedValidatorClient;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getManagedValidatorClient()
  {
    return $this->managedValidatorClient;
  }
  /**
   * URLs for MEV-relay services to use for block building. When set, a GCP-
   * managed MEV-boost service is configured on the beacon client.
   *
   * @param string[] $mevRelayUrls
   */
  public function setMevRelayUrls($mevRelayUrls)
  {
    $this->mevRelayUrls = $mevRelayUrls;
  }
  /**
   * @return string[]
   */
  public function getMevRelayUrls()
  {
    return $this->mevRelayUrls;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValidatorConfig::class, 'Google_Service_BlockchainNodeEngine_ValidatorConfig');
