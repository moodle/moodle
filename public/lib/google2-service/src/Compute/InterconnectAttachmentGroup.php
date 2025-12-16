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

namespace Google\Service\Compute;

class InterconnectAttachmentGroup extends \Google\Model
{
  protected $attachmentsType = InterconnectAttachmentGroupAttachment::class;
  protected $attachmentsDataType = 'map';
  protected $configuredType = InterconnectAttachmentGroupConfigured::class;
  protected $configuredDataType = '';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Opaque system-generated token that uniquely identifies the configuration.
   * If provided when patching a configuration in update mode, the provided
   * token must match the current token or the update is rejected. This provides
   * a reliable means of doing read-modify-write (optimistic locking) as
   * described byAIP 154.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. [Output Only] The unique identifier for the resource type. The
   * server generates this identifier.
   *
   * @var string
   */
  public $id;
  protected $intentType = InterconnectAttachmentGroupIntent::class;
  protected $intentDataType = '';
  /**
   * The URL of an InterconnectGroup that groups these Attachments'
   * Interconnects. Customers do not need to set this unless directed by Google
   * Support.
   *
   * @var string
   */
  public $interconnectGroup;
  /**
   * Output only. [Output Only] Type of the resource. Always
   * compute#interconnectAttachmentGroup.
   *
   * @var string
   */
  public $kind;
  protected $logicalStructureType = InterconnectAttachmentGroupLogicalStructure::class;
  protected $logicalStructureDataType = '';
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;

  /**
   * Attachments in the AttachmentGroup. Keys are arbitrary user-specified
   * strings. Users are encouraged, but not required, to use their preferred
   * format for resource links as keys. Note that there are add-members and
   * remove-members methods in gcloud. The size of this map is limited by an
   * "Attachments per group" quota.
   *
   * @param InterconnectAttachmentGroupAttachment[] $attachments
   */
  public function setAttachments($attachments)
  {
    $this->attachments = $attachments;
  }
  /**
   * @return InterconnectAttachmentGroupAttachment[]
   */
  public function getAttachments()
  {
    return $this->attachments;
  }
  /**
   * @param InterconnectAttachmentGroupConfigured $configured
   */
  public function setConfigured(InterconnectAttachmentGroupConfigured $configured)
  {
    $this->configured = $configured;
  }
  /**
   * @return InterconnectAttachmentGroupConfigured
   */
  public function getConfigured()
  {
    return $this->configured;
  }
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Opaque system-generated token that uniquely identifies the configuration.
   * If provided when patching a configuration in update mode, the provided
   * token must match the current token or the update is rejected. This provides
   * a reliable means of doing read-modify-write (optimistic locking) as
   * described byAIP 154.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. [Output Only] The unique identifier for the resource type. The
   * server generates this identifier.
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
   * @param InterconnectAttachmentGroupIntent $intent
   */
  public function setIntent(InterconnectAttachmentGroupIntent $intent)
  {
    $this->intent = $intent;
  }
  /**
   * @return InterconnectAttachmentGroupIntent
   */
  public function getIntent()
  {
    return $this->intent;
  }
  /**
   * The URL of an InterconnectGroup that groups these Attachments'
   * Interconnects. Customers do not need to set this unless directed by Google
   * Support.
   *
   * @param string $interconnectGroup
   */
  public function setInterconnectGroup($interconnectGroup)
  {
    $this->interconnectGroup = $interconnectGroup;
  }
  /**
   * @return string
   */
  public function getInterconnectGroup()
  {
    return $this->interconnectGroup;
  }
  /**
   * Output only. [Output Only] Type of the resource. Always
   * compute#interconnectAttachmentGroup.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * @param InterconnectAttachmentGroupLogicalStructure $logicalStructure
   */
  public function setLogicalStructure(InterconnectAttachmentGroupLogicalStructure $logicalStructure)
  {
    $this->logicalStructure = $logicalStructure;
  }
  /**
   * @return InterconnectAttachmentGroupLogicalStructure
   */
  public function getLogicalStructure()
  {
    return $this->logicalStructure;
  }
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
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
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectAttachmentGroup::class, 'Google_Service_Compute_InterconnectAttachmentGroup');
