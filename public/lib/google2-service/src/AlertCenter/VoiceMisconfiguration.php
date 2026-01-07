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

namespace Google\Service\AlertCenter;

class VoiceMisconfiguration extends \Google\Model
{
  /**
   * Entity type wasn't set.
   */
  public const ENTITY_TYPE_ENTITY_TYPE_UNSPECIFIED = 'ENTITY_TYPE_UNSPECIFIED';
  /**
   * Invalid auto attendant.
   */
  public const ENTITY_TYPE_AUTO_ATTENDANT = 'AUTO_ATTENDANT';
  /**
   * Invalid ring group.
   */
  public const ENTITY_TYPE_RING_GROUP = 'RING_GROUP';
  /**
   * Name of the entity whose configuration is now invalid.
   *
   * @var string
   */
  public $entityName;
  /**
   * Type of the entity whose configuration is now invalid.
   *
   * @var string
   */
  public $entityType;
  /**
   * Link that the admin can follow to fix the issue.
   *
   * @var string
   */
  public $fixUri;
  protected $membersMisconfigurationType = TransferMisconfiguration::class;
  protected $membersMisconfigurationDataType = '';
  protected $transferMisconfigurationType = TransferMisconfiguration::class;
  protected $transferMisconfigurationDataType = '';
  protected $voicemailMisconfigurationType = VoicemailMisconfiguration::class;
  protected $voicemailMisconfigurationDataType = '';

  /**
   * Name of the entity whose configuration is now invalid.
   *
   * @param string $entityName
   */
  public function setEntityName($entityName)
  {
    $this->entityName = $entityName;
  }
  /**
   * @return string
   */
  public function getEntityName()
  {
    return $this->entityName;
  }
  /**
   * Type of the entity whose configuration is now invalid.
   *
   * Accepted values: ENTITY_TYPE_UNSPECIFIED, AUTO_ATTENDANT, RING_GROUP
   *
   * @param self::ENTITY_TYPE_* $entityType
   */
  public function setEntityType($entityType)
  {
    $this->entityType = $entityType;
  }
  /**
   * @return self::ENTITY_TYPE_*
   */
  public function getEntityType()
  {
    return $this->entityType;
  }
  /**
   * Link that the admin can follow to fix the issue.
   *
   * @param string $fixUri
   */
  public function setFixUri($fixUri)
  {
    $this->fixUri = $fixUri;
  }
  /**
   * @return string
   */
  public function getFixUri()
  {
    return $this->fixUri;
  }
  /**
   * Issue(s) with members of a ring group.
   *
   * @param TransferMisconfiguration $membersMisconfiguration
   */
  public function setMembersMisconfiguration(TransferMisconfiguration $membersMisconfiguration)
  {
    $this->membersMisconfiguration = $membersMisconfiguration;
  }
  /**
   * @return TransferMisconfiguration
   */
  public function getMembersMisconfiguration()
  {
    return $this->membersMisconfiguration;
  }
  /**
   * Issue(s) with transferring or forwarding to an external entity.
   *
   * @param TransferMisconfiguration $transferMisconfiguration
   */
  public function setTransferMisconfiguration(TransferMisconfiguration $transferMisconfiguration)
  {
    $this->transferMisconfiguration = $transferMisconfiguration;
  }
  /**
   * @return TransferMisconfiguration
   */
  public function getTransferMisconfiguration()
  {
    return $this->transferMisconfiguration;
  }
  /**
   * Issue(s) with sending to voicemail.
   *
   * @param VoicemailMisconfiguration $voicemailMisconfiguration
   */
  public function setVoicemailMisconfiguration(VoicemailMisconfiguration $voicemailMisconfiguration)
  {
    $this->voicemailMisconfiguration = $voicemailMisconfiguration;
  }
  /**
   * @return VoicemailMisconfiguration
   */
  public function getVoicemailMisconfiguration()
  {
    return $this->voicemailMisconfiguration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VoiceMisconfiguration::class, 'Google_Service_AlertCenter_VoiceMisconfiguration');
