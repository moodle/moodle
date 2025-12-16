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

namespace Google\Service\CertificateManager;

class DnsResourceRecord extends \Google\Model
{
  /**
   * Output only. Data of the DNS Resource Record.
   *
   * @var string
   */
  public $data;
  /**
   * Output only. Fully qualified name of the DNS Resource Record. e.g. `_acme-
   * challenge.example.com`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Type of the DNS Resource Record. Currently always set to
   * "CNAME".
   *
   * @var string
   */
  public $type;

  /**
   * Output only. Data of the DNS Resource Record.
   *
   * @param string $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Output only. Fully qualified name of the DNS Resource Record. e.g. `_acme-
   * challenge.example.com`
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
  /**
   * Output only. Type of the DNS Resource Record. Currently always set to
   * "CNAME".
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DnsResourceRecord::class, 'Google_Service_CertificateManager_DnsResourceRecord');
