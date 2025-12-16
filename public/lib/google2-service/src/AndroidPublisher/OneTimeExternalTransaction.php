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

namespace Google\Service\AndroidPublisher;

class OneTimeExternalTransaction extends \Google\Model
{
  /**
   * Input only. Provided during the call to Create. Retrieved from the client
   * when the alternative billing flow is launched.
   *
   * @var string
   */
  public $externalTransactionToken;

  /**
   * Input only. Provided during the call to Create. Retrieved from the client
   * when the alternative billing flow is launched.
   *
   * @param string $externalTransactionToken
   */
  public function setExternalTransactionToken($externalTransactionToken)
  {
    $this->externalTransactionToken = $externalTransactionToken;
  }
  /**
   * @return string
   */
  public function getExternalTransactionToken()
  {
    return $this->externalTransactionToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OneTimeExternalTransaction::class, 'Google_Service_AndroidPublisher_OneTimeExternalTransaction');
