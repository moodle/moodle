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

namespace Google\Service\Games;

class UnlinkPersonaRequest extends \Google\Model
{
  /**
   * Value of the 'persona' field as it was provided by the client in
   * LinkPersona RPC
   *
   * @var string
   */
  public $persona;
  /**
   * Required. Opaque server-generated string that encodes all the necessary
   * information to identify the PGS player / Google user and application.
   *
   * @var string
   */
  public $sessionId;
  /**
   * Value of the Recall token as it was provided by the client in LinkPersona
   * RPC
   *
   * @var string
   */
  public $token;

  /**
   * Value of the 'persona' field as it was provided by the client in
   * LinkPersona RPC
   *
   * @param string $persona
   */
  public function setPersona($persona)
  {
    $this->persona = $persona;
  }
  /**
   * @return string
   */
  public function getPersona()
  {
    return $this->persona;
  }
  /**
   * Required. Opaque server-generated string that encodes all the necessary
   * information to identify the PGS player / Google user and application.
   *
   * @param string $sessionId
   */
  public function setSessionId($sessionId)
  {
    $this->sessionId = $sessionId;
  }
  /**
   * @return string
   */
  public function getSessionId()
  {
    return $this->sessionId;
  }
  /**
   * Value of the Recall token as it was provided by the client in LinkPersona
   * RPC
   *
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }
  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UnlinkPersonaRequest::class, 'Google_Service_Games_UnlinkPersonaRequest');
