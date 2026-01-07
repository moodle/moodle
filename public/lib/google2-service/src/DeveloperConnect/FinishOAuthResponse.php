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

namespace Google\Service\DeveloperConnect;

class FinishOAuthResponse extends \Google\Model
{
  protected $exchangeErrorType = ExchangeError::class;
  protected $exchangeErrorDataType = '';

  /**
   * The error resulted from exchanging OAuth tokens from the service provider.
   *
   * @param ExchangeError $exchangeError
   */
  public function setExchangeError(ExchangeError $exchangeError)
  {
    $this->exchangeError = $exchangeError;
  }
  /**
   * @return ExchangeError
   */
  public function getExchangeError()
  {
    return $this->exchangeError;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FinishOAuthResponse::class, 'Google_Service_DeveloperConnect_FinishOAuthResponse');
