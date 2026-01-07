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

namespace Google\Service\ManagedKafka;

class CertificateAuthorityServiceConfig extends \Google\Model
{
  /**
   * Required. The name of the CA pool to pull CA certificates from. Structured
   * like: projects/{project}/locations/{location}/caPools/{ca_pool}. The CA
   * pool does not need to be in the same project or location as the Kafka
   * cluster.
   *
   * @var string
   */
  public $caPool;

  /**
   * Required. The name of the CA pool to pull CA certificates from. Structured
   * like: projects/{project}/locations/{location}/caPools/{ca_pool}. The CA
   * pool does not need to be in the same project or location as the Kafka
   * cluster.
   *
   * @param string $caPool
   */
  public function setCaPool($caPool)
  {
    $this->caPool = $caPool;
  }
  /**
   * @return string
   */
  public function getCaPool()
  {
    return $this->caPool;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CertificateAuthorityServiceConfig::class, 'Google_Service_ManagedKafka_CertificateAuthorityServiceConfig');
