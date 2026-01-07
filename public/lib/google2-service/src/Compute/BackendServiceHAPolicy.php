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

class BackendServiceHAPolicy extends \Google\Model
{
  public const FAST_IPM_OVE_DISABLED = 'DISABLED';
  public const FAST_IPM_OVE_GARP_RA = 'GARP_RA';
  /**
   * Specifies whether fast IP move is enabled, and if so, the mechanism to
   * achieve it.
   *
   * Supported values are:        - DISABLED: Fast IP Move is disabled. You can
   * only use the    haPolicy.leader API to update the leader.    - >GARP_RA:
   * Provides a method to very quickly define a new network    endpoint as the
   * leader. This method is faster than updating the leader    using the
   * haPolicy.leader API. Fast IP move works as follows: The VM    hosting the
   * network endpoint that should become the new leader sends    either a
   * Gratuitous ARP (GARP) packet (IPv4) or an ICMPv6 Router
   * Advertisement(RA) packet (IPv6).  Google Cloud immediately but
   * temporarily associates the forwarding rule IP address with that VM, and
   * both new and in-flight packets are quickly delivered to that VM.
   *
   * Note the important properties of the Fast IP Move functionality:        -
   * The GARP/RA-initiated re-routing stays active for approximately 20
   * minutes. After triggering fast failover, you must also    appropriately set
   * the haPolicy.leader.    -  The new leader instance should continue to send
   * GARP/RA packets    periodically every 10 seconds until at least 10 minutes
   * after updating    the haPolicy.leader (but stop immediately if it is no
   * longer the leader).    - After triggering a fast failover, we recommend
   * that you wait at least    3 seconds before sending another GARP/RA packet
   * from a different VM    instance to avoid race conditions.    - Don't send
   * GARP/RA packets from different VM    instances at the same time. If
   * multiple instances continue to send    GARP/RA packets, traffic might be
   * routed to different destinations in an    alternating order. This condition
   * ceases when a single instance    issues a GARP/RA packet.    - The GARP/RA
   * request always takes priority over the leader API.    Using the
   * haPolicy.leader API to change the leader to a different    instance will
   * have no effect until the GARP/RA request becomes    inactive.    - The
   * GARP/RA packets should follow the GARP/RA    Packet Specifications..    -
   * When multiple forwarding rules refer to a regional backend service,    you
   * need only send a GARP or RA packet for a single forwarding rule    virtual
   * IP. The virtual IPs for all forwarding rules targeting the same    backend
   * service will also be moved to the sender of the GARP or RA    packet.
   *
   * The following are the Fast IP Move limitations (that is, when fastIPMove is
   * not DISABLED):        - Multiple forwarding rules cannot use the same IP
   * address if one of    them refers to a regional backend service with
   * fastIPMove.    - The regional backend service must set the network field,
   * and all    NEGs must belong to that network. However, individual    NEGs
   * can belong to different subnetworks of that network.     - The maximum
   * number of network endpoints across all backends of a    backend service
   * with fastIPMove is 32.    - The maximum number of backend services with
   * fastIPMove that can have    the same network endpoint attached to one of
   * its backends is 64.    - The maximum number of backend services with
   * fastIPMove in a VPC in a    region is 64.    - The network endpoints that
   * are attached to a backend of a backend    service with fastIPMove cannot
   * resolve to Gen3+ machines for IPv6.    - Traffic directed to the leader by
   * a static route next hop will not be    redirected to a new leader by fast
   * failover. Such traffic will only be    redirected once an haPolicy.leader
   * update has taken effect. Only traffic    to the forwarding rule's virtual
   * IP will be redirected to a new leader by    fast failover.
   *
   * haPolicy.fastIPMove can be set only at backend service creation time. Once
   * set, it cannot be updated.
   *
   * By default, fastIpMove is set to DISABLED.
   *
   * @var string
   */
  public $fastIPMove;
  protected $leaderType = BackendServiceHAPolicyLeader::class;
  protected $leaderDataType = '';

  /**
   * Specifies whether fast IP move is enabled, and if so, the mechanism to
   * achieve it.
   *
   * Supported values are:        - DISABLED: Fast IP Move is disabled. You can
   * only use the    haPolicy.leader API to update the leader.    - >GARP_RA:
   * Provides a method to very quickly define a new network    endpoint as the
   * leader. This method is faster than updating the leader    using the
   * haPolicy.leader API. Fast IP move works as follows: The VM    hosting the
   * network endpoint that should become the new leader sends    either a
   * Gratuitous ARP (GARP) packet (IPv4) or an ICMPv6 Router
   * Advertisement(RA) packet (IPv6).  Google Cloud immediately but
   * temporarily associates the forwarding rule IP address with that VM, and
   * both new and in-flight packets are quickly delivered to that VM.
   *
   * Note the important properties of the Fast IP Move functionality:        -
   * The GARP/RA-initiated re-routing stays active for approximately 20
   * minutes. After triggering fast failover, you must also    appropriately set
   * the haPolicy.leader.    -  The new leader instance should continue to send
   * GARP/RA packets    periodically every 10 seconds until at least 10 minutes
   * after updating    the haPolicy.leader (but stop immediately if it is no
   * longer the leader).    - After triggering a fast failover, we recommend
   * that you wait at least    3 seconds before sending another GARP/RA packet
   * from a different VM    instance to avoid race conditions.    - Don't send
   * GARP/RA packets from different VM    instances at the same time. If
   * multiple instances continue to send    GARP/RA packets, traffic might be
   * routed to different destinations in an    alternating order. This condition
   * ceases when a single instance    issues a GARP/RA packet.    - The GARP/RA
   * request always takes priority over the leader API.    Using the
   * haPolicy.leader API to change the leader to a different    instance will
   * have no effect until the GARP/RA request becomes    inactive.    - The
   * GARP/RA packets should follow the GARP/RA    Packet Specifications..    -
   * When multiple forwarding rules refer to a regional backend service,    you
   * need only send a GARP or RA packet for a single forwarding rule    virtual
   * IP. The virtual IPs for all forwarding rules targeting the same    backend
   * service will also be moved to the sender of the GARP or RA    packet.
   *
   * The following are the Fast IP Move limitations (that is, when fastIPMove is
   * not DISABLED):        - Multiple forwarding rules cannot use the same IP
   * address if one of    them refers to a regional backend service with
   * fastIPMove.    - The regional backend service must set the network field,
   * and all    NEGs must belong to that network. However, individual    NEGs
   * can belong to different subnetworks of that network.     - The maximum
   * number of network endpoints across all backends of a    backend service
   * with fastIPMove is 32.    - The maximum number of backend services with
   * fastIPMove that can have    the same network endpoint attached to one of
   * its backends is 64.    - The maximum number of backend services with
   * fastIPMove in a VPC in a    region is 64.    - The network endpoints that
   * are attached to a backend of a backend    service with fastIPMove cannot
   * resolve to Gen3+ machines for IPv6.    - Traffic directed to the leader by
   * a static route next hop will not be    redirected to a new leader by fast
   * failover. Such traffic will only be    redirected once an haPolicy.leader
   * update has taken effect. Only traffic    to the forwarding rule's virtual
   * IP will be redirected to a new leader by    fast failover.
   *
   * haPolicy.fastIPMove can be set only at backend service creation time. Once
   * set, it cannot be updated.
   *
   * By default, fastIpMove is set to DISABLED.
   *
   * Accepted values: DISABLED, GARP_RA
   *
   * @param self::FAST_IPM_OVE_* $fastIPMove
   */
  public function setFastIPMove($fastIPMove)
  {
    $this->fastIPMove = $fastIPMove;
  }
  /**
   * @return self::FAST_IPM_OVE_*
   */
  public function getFastIPMove()
  {
    return $this->fastIPMove;
  }
  /**
   * Selects one of the network endpoints attached to the backend NEGs of this
   * service as the active endpoint (the leader) that receives all traffic.
   *
   * When the leader changes, there is no connection draining to persist
   * existing connections on the old leader.
   *
   * You are responsible for selecting a suitable endpoint as the leader. For
   * example, preferring a healthy endpoint over unhealthy ones. Note that this
   * service does not track backend endpoint health, and selects the configured
   * leader unconditionally.
   *
   * @param BackendServiceHAPolicyLeader $leader
   */
  public function setLeader(BackendServiceHAPolicyLeader $leader)
  {
    $this->leader = $leader;
  }
  /**
   * @return BackendServiceHAPolicyLeader
   */
  public function getLeader()
  {
    return $this->leader;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackendServiceHAPolicy::class, 'Google_Service_Compute_BackendServiceHAPolicy');
