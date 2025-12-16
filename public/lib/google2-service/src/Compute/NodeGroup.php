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

class NodeGroup extends \Google\Model
{
  /**
   * VMs are eligible to receive infrastructure and hypervisor updates as they
   * become available.  This may result in more maintenance operations (live
   * migrations or terminations) for the VM than the PERIODIC andRECURRENT
   * options.
   */
  public const MAINTENANCE_INTERVAL_AS_NEEDED = 'AS_NEEDED';
  /**
   * VMs receive infrastructure and hypervisor updates on a periodic basis,
   * minimizing the number of maintenance operations (live migrations or
   * terminations) on an individual VM.  This may mean a VM will take longer to
   * receive an update than if it was configured forAS_NEEDED.  Security updates
   * will still be applied as soon as they are available. RECURRENT is used for
   * GEN3 and Slice of Hardware VMs.
   */
  public const MAINTENANCE_INTERVAL_RECURRENT = 'RECURRENT';
  /**
   * Allow the node and corresponding instances to retain default maintenance
   * behavior.
   */
  public const MAINTENANCE_POLICY_DEFAULT = 'DEFAULT';
  public const MAINTENANCE_POLICY_MAINTENANCE_POLICY_UNSPECIFIED = 'MAINTENANCE_POLICY_UNSPECIFIED';
  /**
   * When maintenance must be done on a node, the instances on that node will be
   * moved to other nodes in the group. Instances with onHostMaintenance =
   * MIGRATE will live migrate to their destinations while instances with
   * onHostMaintenance = TERMINATE will terminate and then restart on their
   * destination nodes if automaticRestart = true.
   */
  public const MAINTENANCE_POLICY_MIGRATE_WITHIN_NODE_GROUP = 'MIGRATE_WITHIN_NODE_GROUP';
  /**
   * Instances in this group will restart on the same node when maintenance has
   * completed. Instances must have onHostMaintenance = TERMINATE, and they will
   * only restart if automaticRestart = true.
   */
  public const MAINTENANCE_POLICY_RESTART_IN_PLACE = 'RESTART_IN_PLACE';
  public const STATUS_CREATING = 'CREATING';
  public const STATUS_DELETING = 'DELETING';
  public const STATUS_INVALID = 'INVALID';
  public const STATUS_READY = 'READY';
  protected $autoscalingPolicyType = NodeGroupAutoscalingPolicy::class;
  protected $autoscalingPolicyDataType = '';
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
   * @var string
   */
  public $fingerprint;
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] The type of the resource.
   * Alwayscompute#nodeGroup for node group.
   *
   * @var string
   */
  public $kind;
  /**
   * An opaque location hint used to place the Node close to other resources.
   * This field is for use by internal tools that use the public API. The
   * location hint here on the NodeGroup overrides any location_hint present in
   * the NodeTemplate.
   *
   * @var string
   */
  public $locationHint;
  /**
   * Specifies the frequency of planned maintenance events. The accepted values
   * are: `AS_NEEDED` and `RECURRENT`.
   *
   * @var string
   */
  public $maintenanceInterval;
  /**
   * Specifies how to handle instances when a node in the group undergoes
   * maintenance. Set to one of: DEFAULT,RESTART_IN_PLACE, or
   * MIGRATE_WITHIN_NODE_GROUP. The default value is DEFAULT. For more
   * information, see Maintenance policies.
   *
   * @var string
   */
  public $maintenancePolicy;
  protected $maintenanceWindowType = NodeGroupMaintenanceWindow::class;
  protected $maintenanceWindowDataType = '';
  /**
   * The name of the resource, provided by the client when initially creating
   * the resource. The resource name must be 1-63 characters long, and comply
   * withRFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
   *
   * @var string
   */
  public $name;
  /**
   * URL of the node template to create the node group from.
   *
   * @var string
   */
  public $nodeTemplate;
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  protected $shareSettingsType = ShareSettings::class;
  protected $shareSettingsDataType = '';
  /**
   * Output only. [Output Only] The total number of nodes in the node group.
   *
   * @var int
   */
  public $size;
  /**
   * @var string
   */
  public $status;
  /**
   * Output only. [Output Only] The name of the zone where the node group
   * resides, such as us-central1-a.
   *
   * @var string
   */
  public $zone;

  /**
   * Specifies how autoscaling should behave.
   *
   * @param NodeGroupAutoscalingPolicy $autoscalingPolicy
   */
  public function setAutoscalingPolicy(NodeGroupAutoscalingPolicy $autoscalingPolicy)
  {
    $this->autoscalingPolicy = $autoscalingPolicy;
  }
  /**
   * @return NodeGroupAutoscalingPolicy
   */
  public function getAutoscalingPolicy()
  {
    return $this->autoscalingPolicy;
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
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
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
   * Output only. [Output Only] The type of the resource.
   * Alwayscompute#nodeGroup for node group.
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
   * An opaque location hint used to place the Node close to other resources.
   * This field is for use by internal tools that use the public API. The
   * location hint here on the NodeGroup overrides any location_hint present in
   * the NodeTemplate.
   *
   * @param string $locationHint
   */
  public function setLocationHint($locationHint)
  {
    $this->locationHint = $locationHint;
  }
  /**
   * @return string
   */
  public function getLocationHint()
  {
    return $this->locationHint;
  }
  /**
   * Specifies the frequency of planned maintenance events. The accepted values
   * are: `AS_NEEDED` and `RECURRENT`.
   *
   * Accepted values: AS_NEEDED, RECURRENT
   *
   * @param self::MAINTENANCE_INTERVAL_* $maintenanceInterval
   */
  public function setMaintenanceInterval($maintenanceInterval)
  {
    $this->maintenanceInterval = $maintenanceInterval;
  }
  /**
   * @return self::MAINTENANCE_INTERVAL_*
   */
  public function getMaintenanceInterval()
  {
    return $this->maintenanceInterval;
  }
  /**
   * Specifies how to handle instances when a node in the group undergoes
   * maintenance. Set to one of: DEFAULT,RESTART_IN_PLACE, or
   * MIGRATE_WITHIN_NODE_GROUP. The default value is DEFAULT. For more
   * information, see Maintenance policies.
   *
   * Accepted values: DEFAULT, MAINTENANCE_POLICY_UNSPECIFIED,
   * MIGRATE_WITHIN_NODE_GROUP, RESTART_IN_PLACE
   *
   * @param self::MAINTENANCE_POLICY_* $maintenancePolicy
   */
  public function setMaintenancePolicy($maintenancePolicy)
  {
    $this->maintenancePolicy = $maintenancePolicy;
  }
  /**
   * @return self::MAINTENANCE_POLICY_*
   */
  public function getMaintenancePolicy()
  {
    return $this->maintenancePolicy;
  }
  /**
   * @param NodeGroupMaintenanceWindow $maintenanceWindow
   */
  public function setMaintenanceWindow(NodeGroupMaintenanceWindow $maintenanceWindow)
  {
    $this->maintenanceWindow = $maintenanceWindow;
  }
  /**
   * @return NodeGroupMaintenanceWindow
   */
  public function getMaintenanceWindow()
  {
    return $this->maintenanceWindow;
  }
  /**
   * The name of the resource, provided by the client when initially creating
   * the resource. The resource name must be 1-63 characters long, and comply
   * withRFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
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
   * URL of the node template to create the node group from.
   *
   * @param string $nodeTemplate
   */
  public function setNodeTemplate($nodeTemplate)
  {
    $this->nodeTemplate = $nodeTemplate;
  }
  /**
   * @return string
   */
  public function getNodeTemplate()
  {
    return $this->nodeTemplate;
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
  /**
   * Share-settings for the node group
   *
   * @param ShareSettings $shareSettings
   */
  public function setShareSettings(ShareSettings $shareSettings)
  {
    $this->shareSettings = $shareSettings;
  }
  /**
   * @return ShareSettings
   */
  public function getShareSettings()
  {
    return $this->shareSettings;
  }
  /**
   * Output only. [Output Only] The total number of nodes in the node group.
   *
   * @param int $size
   */
  public function setSize($size)
  {
    $this->size = $size;
  }
  /**
   * @return int
   */
  public function getSize()
  {
    return $this->size;
  }
  /**
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. [Output Only] The name of the zone where the node group
   * resides, such as us-central1-a.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodeGroup::class, 'Google_Service_Compute_NodeGroup');
