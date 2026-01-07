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

namespace Google\Service\Datastream;

class PostgresqlSslConfig extends \Google\Model
{
  protected $serverAndClientVerificationType = ServerAndClientVerification::class;
  protected $serverAndClientVerificationDataType = '';
  protected $serverVerificationType = ServerVerification::class;
  protected $serverVerificationDataType = '';

  /**
   * If this field is set, the communication will be encrypted with TLS
   * encryption and both the server identity and the client identity will be
   * authenticated.
   *
   * @param ServerAndClientVerification $serverAndClientVerification
   */
  public function setServerAndClientVerification(ServerAndClientVerification $serverAndClientVerification)
  {
    $this->serverAndClientVerification = $serverAndClientVerification;
  }
  /**
   * @return ServerAndClientVerification
   */
  public function getServerAndClientVerification()
  {
    return $this->serverAndClientVerification;
  }
  /**
   * If this field is set, the communication will be encrypted with TLS
   * encryption and the server identity will be authenticated.
   *
   * @param ServerVerification $serverVerification
   */
  public function setServerVerification(ServerVerification $serverVerification)
  {
    $this->serverVerification = $serverVerification;
  }
  /**
   * @return ServerVerification
   */
  public function getServerVerification()
  {
    return $this->serverVerification;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PostgresqlSslConfig::class, 'Google_Service_Datastream_PostgresqlSslConfig');
