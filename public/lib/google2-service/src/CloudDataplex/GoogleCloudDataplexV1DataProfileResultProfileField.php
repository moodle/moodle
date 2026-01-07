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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataProfileResultProfileField extends \Google\Model
{
  /**
   * Output only. The mode of the field. Possible values include: REQUIRED, if
   * it is a required field. NULLABLE, if it is an optional field. REPEATED, if
   * it is a repeated field.
   *
   * @var string
   */
  public $mode;
  /**
   * Output only. The name of the field.
   *
   * @var string
   */
  public $name;
  protected $profileType = GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfo::class;
  protected $profileDataType = '';
  /**
   * Output only. The data type retrieved from the schema of the data source.
   * For instance, for a BigQuery native table, it is the BigQuery Table Schema
   * (https://cloud.google.com/bigquery/docs/reference/rest/v2/tables#tablefield
   * schema). For a Dataplex Universal Catalog Entity, it is the Entity Schema (
   * https://cloud.google.com/dataplex/docs/reference/rpc/google.cloud.dataplex.
   * v1#type_3).
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The mode of the field. Possible values include: REQUIRED, if
   * it is a required field. NULLABLE, if it is an optional field. REPEATED, if
   * it is a repeated field.
   *
   * @param string $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return string
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Output only. The name of the field.
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
   * Output only. Profile information for the corresponding field.
   *
   * @param GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfo $profile
   */
  public function setProfile(GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfo $profile)
  {
    $this->profile = $profile;
  }
  /**
   * @return GoogleCloudDataplexV1DataProfileResultProfileFieldProfileInfo
   */
  public function getProfile()
  {
    return $this->profile;
  }
  /**
   * Output only. The data type retrieved from the schema of the data source.
   * For instance, for a BigQuery native table, it is the BigQuery Table Schema
   * (https://cloud.google.com/bigquery/docs/reference/rest/v2/tables#tablefield
   * schema). For a Dataplex Universal Catalog Entity, it is the Entity Schema (
   * https://cloud.google.com/dataplex/docs/reference/rpc/google.cloud.dataplex.
   * v1#type_3).
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
class_alias(GoogleCloudDataplexV1DataProfileResultProfileField::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataProfileResultProfileField');
