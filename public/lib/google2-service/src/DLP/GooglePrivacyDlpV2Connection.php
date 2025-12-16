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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2Connection extends \Google\Collection
{
  /**
   * Unused
   */
  public const STATE_CONNECTION_STATE_UNSPECIFIED = 'CONNECTION_STATE_UNSPECIFIED';
  /**
   * The DLP API automatically created this connection during an initial scan,
   * and it is awaiting full configuration by a user.
   */
  public const STATE_MISSING_CREDENTIALS = 'MISSING_CREDENTIALS';
  /**
   * A configured connection that has not encountered any errors.
   */
  public const STATE_AVAILABLE = 'AVAILABLE';
  /**
   * A configured connection that encountered errors during its last use. It
   * will not be used again until it is set to AVAILABLE. If the resolution
   * requires external action, then the client must send a request to set the
   * status to AVAILABLE when the connection is ready for use. If the resolution
   * doesn't require external action, then any changes to the connection
   * properties will automatically mark it as AVAILABLE.
   */
  public const STATE_ERROR = 'ERROR';
  protected $collection_key = 'errors';
  protected $cloudSqlType = GooglePrivacyDlpV2CloudSqlProperties::class;
  protected $cloudSqlDataType = '';
  protected $errorsType = GooglePrivacyDlpV2Error::class;
  protected $errorsDataType = 'array';
  /**
   * Output only. Name of the connection:
   * `projects/{project}/locations/{location}/connections/{name}`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The connection's state in its lifecycle.
   *
   * @var string
   */
  public $state;

  /**
   * Connect to a Cloud SQL instance.
   *
   * @param GooglePrivacyDlpV2CloudSqlProperties $cloudSql
   */
  public function setCloudSql(GooglePrivacyDlpV2CloudSqlProperties $cloudSql)
  {
    $this->cloudSql = $cloudSql;
  }
  /**
   * @return GooglePrivacyDlpV2CloudSqlProperties
   */
  public function getCloudSql()
  {
    return $this->cloudSql;
  }
  /**
   * Output only. Set if status == ERROR, to provide additional details. Will
   * store the last 10 errors sorted with the most recent first.
   *
   * @param GooglePrivacyDlpV2Error[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return GooglePrivacyDlpV2Error[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Output only. Name of the connection:
   * `projects/{project}/locations/{location}/connections/{name}`.
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
   * Required. The connection's state in its lifecycle.
   *
   * Accepted values: CONNECTION_STATE_UNSPECIFIED, MISSING_CREDENTIALS,
   * AVAILABLE, ERROR
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2Connection::class, 'Google_Service_DLP_GooglePrivacyDlpV2Connection');
