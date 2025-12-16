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

namespace Google\Service\AndroidEnterprise;

class ServiceAccountKey extends \Google\Model
{
  /**
   * Google Credentials File format.
   */
  public const TYPE_googleCredentials = 'googleCredentials';
  /**
   * PKCS12 format. The password for the PKCS12 file is 'notasecret'. For more
   * information, see https://tools.ietf.org/html/rfc7292. The data for keys of
   * this type are base64 encoded according to RFC 4648 Section 4. See
   * http://tools.ietf.org/html/rfc4648#section-4.
   */
  public const TYPE_pkcs12 = 'pkcs12';
  /**
   * The body of the private key credentials file, in string format. This is
   * only populated when the ServiceAccountKey is created, and is not stored by
   * Google.
   *
   * @var string
   */
  public $data;
  /**
   * An opaque, unique identifier for this ServiceAccountKey. Assigned by the
   * server.
   *
   * @var string
   */
  public $id;
  /**
   * Public key data for the credentials file. This is an X.509 cert. If you are
   * using the googleCredentials key type, this is identical to the cert that
   * can be retrieved by using the X.509 cert url inside of the credentials
   * file.
   *
   * @var string
   */
  public $publicData;
  /**
   * The file format of the generated key data.
   *
   * @var string
   */
  public $type;

  /**
   * The body of the private key credentials file, in string format. This is
   * only populated when the ServiceAccountKey is created, and is not stored by
   * Google.
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
   * An opaque, unique identifier for this ServiceAccountKey. Assigned by the
   * server.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Public key data for the credentials file. This is an X.509 cert. If you are
   * using the googleCredentials key type, this is identical to the cert that
   * can be retrieved by using the X.509 cert url inside of the credentials
   * file.
   *
   * @param string $publicData
   */
  public function setPublicData($publicData)
  {
    $this->publicData = $publicData;
  }
  /**
   * @return string
   */
  public function getPublicData()
  {
    return $this->publicData;
  }
  /**
   * The file format of the generated key data.
   *
   * Accepted values: googleCredentials, pkcs12
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceAccountKey::class, 'Google_Service_AndroidEnterprise_ServiceAccountKey');
