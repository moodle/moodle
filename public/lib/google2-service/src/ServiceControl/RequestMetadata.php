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

namespace Google\Service\ServiceControl;

class RequestMetadata extends \Google\Model
{
  /**
   * The IP address of the caller. For a caller from the internet, this will be
   * the public IPv4 or IPv6 address. For calls made from inside Google's
   * internal production network from one GCP service to another, `caller_ip`
   * will be redacted to "private". For a caller from a Compute Engine VM with a
   * external IP address, `caller_ip` will be the VM's external IP address. For
   * a caller from a Compute Engine VM without a external IP address, if the VM
   * is in the same organization (or project) as the accessed resource,
   * `caller_ip` will be the VM's internal IPv4 address, otherwise `caller_ip`
   * will be redacted to "gce-internal-ip". See
   * https://cloud.google.com/compute/docs/vpc/ for more information.
   *
   * @var string
   */
  public $callerIp;
  /**
   * The network of the caller. Set only if the network host project is part of
   * the same GCP organization (or project) as the accessed resource. See
   * https://cloud.google.com/compute/docs/vpc/ for more information. This is a
   * scheme-less URI full resource name. For example:
   * "//compute.googleapis.com/projects/PROJECT_ID/global/networks/NETWORK_ID"
   *
   * @var string
   */
  public $callerNetwork;
  /**
   * The user agent of the caller. This information is not authenticated and
   * should be treated accordingly. For example: + `google-api-python-
   * client/1.4.0`: The request was made by the Google API client for Python. +
   * `Cloud SDK Command Line Tool apitools-client/1.0 gcloud/0.9.62`: The
   * request was made by the Google Cloud SDK CLI (gcloud). + `AppEngine-Google;
   * (+http://code.google.com/appengine; appid: s~my-project`: The request was
   * made from the `my-project` App Engine app.
   *
   * @var string
   */
  public $callerSuppliedUserAgent;
  protected $destinationAttributesType = Peer::class;
  protected $destinationAttributesDataType = '';
  protected $requestAttributesType = Request::class;
  protected $requestAttributesDataType = '';

  /**
   * The IP address of the caller. For a caller from the internet, this will be
   * the public IPv4 or IPv6 address. For calls made from inside Google's
   * internal production network from one GCP service to another, `caller_ip`
   * will be redacted to "private". For a caller from a Compute Engine VM with a
   * external IP address, `caller_ip` will be the VM's external IP address. For
   * a caller from a Compute Engine VM without a external IP address, if the VM
   * is in the same organization (or project) as the accessed resource,
   * `caller_ip` will be the VM's internal IPv4 address, otherwise `caller_ip`
   * will be redacted to "gce-internal-ip". See
   * https://cloud.google.com/compute/docs/vpc/ for more information.
   *
   * @param string $callerIp
   */
  public function setCallerIp($callerIp)
  {
    $this->callerIp = $callerIp;
  }
  /**
   * @return string
   */
  public function getCallerIp()
  {
    return $this->callerIp;
  }
  /**
   * The network of the caller. Set only if the network host project is part of
   * the same GCP organization (or project) as the accessed resource. See
   * https://cloud.google.com/compute/docs/vpc/ for more information. This is a
   * scheme-less URI full resource name. For example:
   * "//compute.googleapis.com/projects/PROJECT_ID/global/networks/NETWORK_ID"
   *
   * @param string $callerNetwork
   */
  public function setCallerNetwork($callerNetwork)
  {
    $this->callerNetwork = $callerNetwork;
  }
  /**
   * @return string
   */
  public function getCallerNetwork()
  {
    return $this->callerNetwork;
  }
  /**
   * The user agent of the caller. This information is not authenticated and
   * should be treated accordingly. For example: + `google-api-python-
   * client/1.4.0`: The request was made by the Google API client for Python. +
   * `Cloud SDK Command Line Tool apitools-client/1.0 gcloud/0.9.62`: The
   * request was made by the Google Cloud SDK CLI (gcloud). + `AppEngine-Google;
   * (+http://code.google.com/appengine; appid: s~my-project`: The request was
   * made from the `my-project` App Engine app.
   *
   * @param string $callerSuppliedUserAgent
   */
  public function setCallerSuppliedUserAgent($callerSuppliedUserAgent)
  {
    $this->callerSuppliedUserAgent = $callerSuppliedUserAgent;
  }
  /**
   * @return string
   */
  public function getCallerSuppliedUserAgent()
  {
    return $this->callerSuppliedUserAgent;
  }
  /**
   * The destination of a network activity, such as accepting a TCP connection.
   * In a multi hop network activity, the destination represents the receiver of
   * the last hop. Only two fields are used in this message, Peer.port and
   * Peer.ip. These fields are optionally populated by those services utilizing
   * the IAM condition feature.
   *
   * @param Peer $destinationAttributes
   */
  public function setDestinationAttributes(Peer $destinationAttributes)
  {
    $this->destinationAttributes = $destinationAttributes;
  }
  /**
   * @return Peer
   */
  public function getDestinationAttributes()
  {
    return $this->destinationAttributes;
  }
  /**
   * Request attributes used in IAM condition evaluation. This field contains
   * request attributes like request time and access levels associated with the
   * request. To get the whole view of the attributes used in IAM condition
   * evaluation, the user must also look into
   * `AuditLog.authentication_info.resource_attributes`.
   *
   * @param Request $requestAttributes
   */
  public function setRequestAttributes(Request $requestAttributes)
  {
    $this->requestAttributes = $requestAttributes;
  }
  /**
   * @return Request
   */
  public function getRequestAttributes()
  {
    return $this->requestAttributes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RequestMetadata::class, 'Google_Service_ServiceControl_RequestMetadata');
