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

class MultiplexedSessionPrecommitToken extends \Google\Model
{
  /**
   * Opaque precommit token.
   *
   * @var string
   */
  public $precommitToken;
  /**
   * An incrementing seq number is generated on every precommit token that is
   * returned. Clients should remember the precommit token with the highest
   * sequence number from the current transaction attempt.
   *
   * @var int
   */
  public $seqNum;

  /**
   * Opaque precommit token.
   *
   * @param string $precommitToken
   */
  public function setPrecommitToken($precommitToken)
  {
    $this->precommitToken = $precommitToken;
  }
  /**
   * @return string
   */
  public function getPrecommitToken()
  {
    return $this->precommitToken;
  }
  /**
   * An incrementing seq number is generated on every precommit token that is
   * returned. Clients should remember the precommit token with the highest
   * sequence number from the current transaction attempt.
   *
   * @param int $seqNum
   */
  public function setSeqNum($seqNum)
  {
    $this->seqNum = $seqNum;
  }
  /**
   * @return int
   */
  public function getSeqNum()
  {
    return $this->seqNum;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MultiplexedSessionPrecommitToken::class, 'Google_Service_Spanner_MultiplexedSessionPrecommitToken');
