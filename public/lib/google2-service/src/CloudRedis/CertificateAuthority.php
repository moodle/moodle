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

namespace Google\Service\CloudRedis;

class CertificateAuthority extends \Google\Model
{
  protected $managedServerCaType = ManagedCertificateAuthority::class;
  protected $managedServerCaDataType = '';
  /**
   * Identifier. Unique name of the resource in this scope including project,
   * location and cluster using the form: `projects/{project}/locations/{locatio
   * n}/clusters/{cluster}/certificateAuthority`
   *
   * @var string
   */
  public $name;

  /**
   * @param ManagedCertificateAuthority $managedServerCa
   */
  public function setManagedServerCa(ManagedCertificateAuthority $managedServerCa)
  {
    $this->managedServerCa = $managedServerCa;
  }
  /**
   * @return ManagedCertificateAuthority
   */
  public function getManagedServerCa()
  {
    return $this->managedServerCa;
  }
  /**
   * Identifier. Unique name of the resource in this scope including project,
   * location and cluster using the form: `projects/{project}/locations/{locatio
   * n}/clusters/{cluster}/certificateAuthority`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CertificateAuthority::class, 'Google_Service_CloudRedis_CertificateAuthority');
