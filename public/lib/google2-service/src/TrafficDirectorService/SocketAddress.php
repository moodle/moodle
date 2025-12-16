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

namespace Google\Service\TrafficDirectorService;

class SocketAddress extends \Google\Model
{
  public const PROTOCOL_TCP = 'TCP';
  public const PROTOCOL_UDP = 'UDP';
  /**
   * The address for this socket. :ref:`Listeners ` will bind to the address. An
   * empty address is not allowed. Specify ``0.0.0.0`` or ``::`` to bind to any
   * address. [#comment:TODO(zuercher) reinstate when implemented: It is
   * possible to distinguish a Listener address via the prefix/suffix matching
   * in :ref:`FilterChainMatch `.] When used within an upstream :ref:`BindConfig
   * `, the address controls the source address of outbound connections. For
   * :ref:`clusters `, the cluster type determines whether the address must be
   * an IP (``STATIC`` or ``EDS`` clusters) or a hostname resolved by DNS
   * (``STRICT_DNS`` or ``LOGICAL_DNS`` clusters). Address resolution can be
   * customized via :ref:`resolver_name `.
   *
   * @var string
   */
  public $address;
  /**
   * When binding to an IPv6 address above, this enables `IPv4 compatibility `_.
   * Binding to ``::`` will allow both IPv4 and IPv6 connections, with peer IPv4
   * addresses mapped into IPv6 space as ``::FFFF:``.
   *
   * @var bool
   */
  public $ipv4Compat;
  /**
   * This is only valid if :ref:`resolver_name ` is specified below and the
   * named resolver is capable of named port resolution.
   *
   * @var string
   */
  public $namedPort;
  /**
   * Filepath that specifies the Linux network namespace this socket will be
   * created in (see ``man 7 network_namespaces``). If this field is set, Envoy
   * will create the socket in the specified network namespace. .. note::
   * Setting this parameter requires Envoy to run with the ``CAP_NET_ADMIN``
   * capability. .. attention:: Network namespaces are only configurable on
   * Linux. Otherwise, this field has no effect.
   *
   * @var string
   */
  public $networkNamespaceFilepath;
  /**
   * @var string
   */
  public $portValue;
  /**
   * @var string
   */
  public $protocol;
  /**
   * The name of the custom resolver. This must have been registered with Envoy.
   * If this is empty, a context dependent default applies. If the address is a
   * concrete IP address, no resolution will occur. If address is a hostname
   * this should be set for resolution other than DNS. Specifying a custom
   * resolver with ``STRICT_DNS`` or ``LOGICAL_DNS`` will generate an error at
   * runtime.
   *
   * @var string
   */
  public $resolverName;

  /**
   * The address for this socket. :ref:`Listeners ` will bind to the address. An
   * empty address is not allowed. Specify ``0.0.0.0`` or ``::`` to bind to any
   * address. [#comment:TODO(zuercher) reinstate when implemented: It is
   * possible to distinguish a Listener address via the prefix/suffix matching
   * in :ref:`FilterChainMatch `.] When used within an upstream :ref:`BindConfig
   * `, the address controls the source address of outbound connections. For
   * :ref:`clusters `, the cluster type determines whether the address must be
   * an IP (``STATIC`` or ``EDS`` clusters) or a hostname resolved by DNS
   * (``STRICT_DNS`` or ``LOGICAL_DNS`` clusters). Address resolution can be
   * customized via :ref:`resolver_name `.
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * When binding to an IPv6 address above, this enables `IPv4 compatibility `_.
   * Binding to ``::`` will allow both IPv4 and IPv6 connections, with peer IPv4
   * addresses mapped into IPv6 space as ``::FFFF:``.
   *
   * @param bool $ipv4Compat
   */
  public function setIpv4Compat($ipv4Compat)
  {
    $this->ipv4Compat = $ipv4Compat;
  }
  /**
   * @return bool
   */
  public function getIpv4Compat()
  {
    return $this->ipv4Compat;
  }
  /**
   * This is only valid if :ref:`resolver_name ` is specified below and the
   * named resolver is capable of named port resolution.
   *
   * @param string $namedPort
   */
  public function setNamedPort($namedPort)
  {
    $this->namedPort = $namedPort;
  }
  /**
   * @return string
   */
  public function getNamedPort()
  {
    return $this->namedPort;
  }
  /**
   * Filepath that specifies the Linux network namespace this socket will be
   * created in (see ``man 7 network_namespaces``). If this field is set, Envoy
   * will create the socket in the specified network namespace. .. note::
   * Setting this parameter requires Envoy to run with the ``CAP_NET_ADMIN``
   * capability. .. attention:: Network namespaces are only configurable on
   * Linux. Otherwise, this field has no effect.
   *
   * @param string $networkNamespaceFilepath
   */
  public function setNetworkNamespaceFilepath($networkNamespaceFilepath)
  {
    $this->networkNamespaceFilepath = $networkNamespaceFilepath;
  }
  /**
   * @return string
   */
  public function getNetworkNamespaceFilepath()
  {
    return $this->networkNamespaceFilepath;
  }
  /**
   * @param string $portValue
   */
  public function setPortValue($portValue)
  {
    $this->portValue = $portValue;
  }
  /**
   * @return string
   */
  public function getPortValue()
  {
    return $this->portValue;
  }
  /**
   * @param self::PROTOCOL_* $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return self::PROTOCOL_*
   */
  public function getProtocol()
  {
    return $this->protocol;
  }
  /**
   * The name of the custom resolver. This must have been registered with Envoy.
   * If this is empty, a context dependent default applies. If the address is a
   * concrete IP address, no resolution will occur. If address is a hostname
   * this should be set for resolution other than DNS. Specifying a custom
   * resolver with ``STRICT_DNS`` or ``LOGICAL_DNS`` will generate an error at
   * runtime.
   *
   * @param string $resolverName
   */
  public function setResolverName($resolverName)
  {
    $this->resolverName = $resolverName;
  }
  /**
   * @return string
   */
  public function getResolverName()
  {
    return $this->resolverName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SocketAddress::class, 'Google_Service_TrafficDirectorService_SocketAddress');
