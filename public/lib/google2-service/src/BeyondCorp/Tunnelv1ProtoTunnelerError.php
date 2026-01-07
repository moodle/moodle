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

namespace Google\Service\BeyondCorp;

class Tunnelv1ProtoTunnelerError extends \Google\Model
{
  /**
   * Original raw error
   *
   * @var string
   */
  public $err;
  /**
   * retryable isn't used for now, but we may want to reuse it in the future.
   *
   * @var bool
   */
  public $retryable;

  /**
   * Original raw error
   *
   * @param string $err
   */
  public function setErr($err)
  {
    $this->err = $err;
  }
  /**
   * @return string
   */
  public function getErr()
  {
    return $this->err;
  }
  /**
   * retryable isn't used for now, but we may want to reuse it in the future.
   *
   * @param bool $retryable
   */
  public function setRetryable($retryable)
  {
    $this->retryable = $retryable;
  }
  /**
   * @return bool
   */
  public function getRetryable()
  {
    return $this->retryable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Tunnelv1ProtoTunnelerError::class, 'Google_Service_BeyondCorp_Tunnelv1ProtoTunnelerError');
