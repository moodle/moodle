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

class Hl7V2Store extends \Google\Collection
{
  protected $collection_key = 'notificationConfigs';
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
   * Identifier. Resource name of the HL7v2 store, of the form `projects/{projec
   * t_id}/locations/{location_id}/datasets/{dataset_id}/hl7V2Stores/{hl7v2_stor
   * e_id}`.
   *
   * @var string
   */
  public $name;
  protected $notificationConfigsType = Hl7V2NotificationConfig::class;
  protected $notificationConfigsDataType = 'array';
  protected $parserConfigType = ParserConfig::class;
  protected $parserConfigDataType = '';
  /**
   * Optional. Determines whether to reject duplicate messages. A duplicate
   * message is a message with the same raw bytes as a message that has already
   * been ingested/created in this HL7v2 store. The default value is false,
   * meaning that the store accepts the duplicate messages and it also returns
   * the same ACK message in the IngestMessageResponse as has been returned
   * previously. Note that only one resource is created in the store. When this
   * field is set to true, CreateMessage/IngestMessage requests with a duplicate
   * message will be rejected by the store, and IngestMessageErrorDetail returns
   * a NACK message upon rejection.
   *
   * @var bool
   */
  public $rejectDuplicateMessage;

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
   * Identifier. Resource name of the HL7v2 store, of the form `projects/{projec
   * t_id}/locations/{location_id}/datasets/{dataset_id}/hl7V2Stores/{hl7v2_stor
   * e_id}`.
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
   * Optional. A list of notification configs. Each configuration uses a filter
   * to determine whether to publish a message (both Ingest & Create) on the
   * corresponding notification destination. Only the message name is sent as
   * part of the notification. Supplied by the client.
   *
   * @param Hl7V2NotificationConfig[] $notificationConfigs
   */
  public function setNotificationConfigs($notificationConfigs)
  {
    $this->notificationConfigs = $notificationConfigs;
  }
  /**
   * @return Hl7V2NotificationConfig[]
   */
  public function getNotificationConfigs()
  {
    return $this->notificationConfigs;
  }
  /**
   * Optional. The configuration for the parser. It determines how the server
   * parses the messages.
   *
   * @param ParserConfig $parserConfig
   */
  public function setParserConfig(ParserConfig $parserConfig)
  {
    $this->parserConfig = $parserConfig;
  }
  /**
   * @return ParserConfig
   */
  public function getParserConfig()
  {
    return $this->parserConfig;
  }
  /**
   * Optional. Determines whether to reject duplicate messages. A duplicate
   * message is a message with the same raw bytes as a message that has already
   * been ingested/created in this HL7v2 store. The default value is false,
   * meaning that the store accepts the duplicate messages and it also returns
   * the same ACK message in the IngestMessageResponse as has been returned
   * previously. Note that only one resource is created in the store. When this
   * field is set to true, CreateMessage/IngestMessage requests with a duplicate
   * message will be rejected by the store, and IngestMessageErrorDetail returns
   * a NACK message upon rejection.
   *
   * @param bool $rejectDuplicateMessage
   */
  public function setRejectDuplicateMessage($rejectDuplicateMessage)
  {
    $this->rejectDuplicateMessage = $rejectDuplicateMessage;
  }
  /**
   * @return bool
   */
  public function getRejectDuplicateMessage()
  {
    return $this->rejectDuplicateMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Hl7V2Store::class, 'Google_Service_CloudHealthcare_Hl7V2Store');
