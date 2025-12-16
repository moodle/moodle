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

namespace Google\Service\CloudHealthcare;

class Dataset extends \Google\Model
{
  protected $encryptionSpecType = EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Identifier. Resource name of the dataset, of the form
   * `projects/{project_id}/locations/{location_id}/datasets/{dataset_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Whether the dataset satisfies zone isolation.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Whether the dataset satisfies zone separation.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Optional. The default timezone used by this dataset. Must be a either a
   * valid IANA time zone name such as "America/New_York" or empty, which
   * defaults to UTC. This is used for parsing times in resources, such as HL7
   * messages, where no explicit timezone is specified.
   *
   * @var string
   */
  public $timeZone;

  /**
   * Optional. Customer-managed encryption key spec for a Dataset. If set, this
   * Dataset and all of its sub-resources will be secured by this key. If empty,
   * the Dataset is secured by the default Google encryption key.
   *
   * @param EncryptionSpec $encryptionSpec
   */
  public function setEncryptionSpec(EncryptionSpec $encryptionSpec)
  {
    $this->encryptionSpec = $encryptionSpec;
  }
  /**
   * @return EncryptionSpec
   */
  public function getEncryptionSpec()
  {
    return $this->encryptionSpec;
  }
  /**
   * Identifier. Resource name of the dataset, of the form
   * `projects/{project_id}/locations/{location_id}/datasets/{dataset_id}`.
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
   * Output only. Whether the dataset satisfies zone isolation.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Whether the dataset satisfies zone separation.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Optional. The default timezone used by this dataset. Must be a either a
   * valid IANA time zone name such as "America/New_York" or empty, which
   * defaults to UTC. This is used for parsing times in resources, such as HL7
   * messages, where no explicit timezone is specified.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Dataset::class, 'Google_Service_CloudHealthcare_Dataset');
