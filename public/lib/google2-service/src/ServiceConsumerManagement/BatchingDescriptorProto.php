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

namespace Google\Service\ServiceConsumerManagement;

class BatchingDescriptorProto extends \Google\Collection
{
  protected $collection_key = 'discriminatorFields';
  /**
   * The repeated field in the request message to be aggregated by batching.
   *
   * @var string
   */
  public $batchedField;
  /**
   * A list of the fields in the request message. Two requests will be batched
   * together only if the values of every field specified in
   * `request_discriminator_fields` is equal between the two requests.
   *
   * @var string[]
   */
  public $discriminatorFields;
  /**
   * Optional. When present, indicates the field in the response message to be
   * used to demultiplex the response into multiple response messages, in
   * correspondence with the multiple request messages originally batched
   * together.
   *
   * @var string
   */
  public $subresponseField;

  /**
   * The repeated field in the request message to be aggregated by batching.
   *
   * @param string $batchedField
   */
  public function setBatchedField($batchedField)
  {
    $this->batchedField = $batchedField;
  }
  /**
   * @return string
   */
  public function getBatchedField()
  {
    return $this->batchedField;
  }
  /**
   * A list of the fields in the request message. Two requests will be batched
   * together only if the values of every field specified in
   * `request_discriminator_fields` is equal between the two requests.
   *
   * @param string[] $discriminatorFields
   */
  public function setDiscriminatorFields($discriminatorFields)
  {
    $this->discriminatorFields = $discriminatorFields;
  }
  /**
   * @return string[]
   */
  public function getDiscriminatorFields()
  {
    return $this->discriminatorFields;
  }
  /**
   * Optional. When present, indicates the field in the response message to be
   * used to demultiplex the response into multiple response messages, in
   * correspondence with the multiple request messages originally batched
   * together.
   *
   * @param string $subresponseField
   */
  public function setSubresponseField($subresponseField)
  {
    $this->subresponseField = $subresponseField;
  }
  /**
   * @return string
   */
  public function getSubresponseField()
  {
    return $this->subresponseField;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchingDescriptorProto::class, 'Google_Service_ServiceConsumerManagement_BatchingDescriptorProto');
