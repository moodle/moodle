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

class Message extends \Google\Collection
{
  protected $collection_key = 'patientIds';
  /**
   * Output only. The datetime when the message was created. Set by the server.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Raw message bytes.
   *
   * @var string
   */
  public $data;
  /**
   * User-supplied key-value pairs used to organize HL7v2 stores. Label keys
   * must be between 1 and 63 characters long, have a UTF-8 encoding of maximum
   * 128 bytes, and must conform to the following PCRE regular expression:
   * \p{Ll}\p{Lo}{0,62} Label values are optional, must be between 1 and 63
   * characters long, have a UTF-8 encoding of maximum 128 bytes, and must
   * conform to the following PCRE regular expression:
   * [\p{Ll}\p{Lo}\p{N}_-]{0,63} No more than 64 labels can be associated with a
   * given store.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The message type for this message. MSH-9.1.
   *
   * @var string
   */
  public $messageType;
  /**
   * Output only. Resource name of the Message, of the form `projects/{project_i
   * d}/locations/{location_id}/datasets/{dataset_id}/hl7V2Stores/{hl7_v2_store_
   * id}/messages/{message_id}`.
   *
   * @var string
   */
  public $name;
  protected $parsedDataType = ParsedData::class;
  protected $parsedDataDataType = '';
  protected $patientIdsType = PatientId::class;
  protected $patientIdsDataType = 'array';
  protected $schematizedDataType = SchematizedData::class;
  protected $schematizedDataDataType = '';
  /**
   * Output only. The hospital that this message came from. MSH-4.
   *
   * @var string
   */
  public $sendFacility;
  /**
   * Output only. The datetime the sending application sent this message. MSH-7.
   *
   * @var string
   */
  public $sendTime;

  /**
   * Output only. The datetime when the message was created. Set by the server.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. Raw message bytes.
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
   * User-supplied key-value pairs used to organize HL7v2 stores. Label keys
   * must be between 1 and 63 characters long, have a UTF-8 encoding of maximum
   * 128 bytes, and must conform to the following PCRE regular expression:
   * \p{Ll}\p{Lo}{0,62} Label values are optional, must be between 1 and 63
   * characters long, have a UTF-8 encoding of maximum 128 bytes, and must
   * conform to the following PCRE regular expression:
   * [\p{Ll}\p{Lo}\p{N}_-]{0,63} No more than 64 labels can be associated with a
   * given store.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The message type for this message. MSH-9.1.
   *
   * @param string $messageType
   */
  public function setMessageType($messageType)
  {
    $this->messageType = $messageType;
  }
  /**
   * @return string
   */
  public function getMessageType()
  {
    return $this->messageType;
  }
  /**
   * Output only. Resource name of the Message, of the form `projects/{project_i
   * d}/locations/{location_id}/datasets/{dataset_id}/hl7V2Stores/{hl7_v2_store_
   * id}/messages/{message_id}`.
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
   * Output only. The parsed version of the raw message data.
   *
   * @param ParsedData $parsedData
   */
  public function setParsedData(ParsedData $parsedData)
  {
    $this->parsedData = $parsedData;
  }
  /**
   * @return ParsedData
   */
  public function getParsedData()
  {
    return $this->parsedData;
  }
  /**
   * Output only. All patient IDs listed in the PID-2, PID-3, and PID-4 segments
   * of this message.
   *
   * @param PatientId[] $patientIds
   */
  public function setPatientIds($patientIds)
  {
    $this->patientIds = $patientIds;
  }
  /**
   * @return PatientId[]
   */
  public function getPatientIds()
  {
    return $this->patientIds;
  }
  /**
   * Output only. The parsed version of the raw message data schematized
   * according to this store's schemas and type definitions.
   *
   * @param SchematizedData $schematizedData
   */
  public function setSchematizedData(SchematizedData $schematizedData)
  {
    $this->schematizedData = $schematizedData;
  }
  /**
   * @return SchematizedData
   */
  public function getSchematizedData()
  {
    return $this->schematizedData;
  }
  /**
   * Output only. The hospital that this message came from. MSH-4.
   *
   * @param string $sendFacility
   */
  public function setSendFacility($sendFacility)
  {
    $this->sendFacility = $sendFacility;
  }
  /**
   * @return string
   */
  public function getSendFacility()
  {
    return $this->sendFacility;
  }
  /**
   * Output only. The datetime the sending application sent this message. MSH-7.
   *
   * @param string $sendTime
   */
  public function setSendTime($sendTime)
  {
    $this->sendTime = $sendTime;
  }
  /**
   * @return string
   */
  public function getSendTime()
  {
    return $this->sendTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Message::class, 'Google_Service_CloudHealthcare_Message');
