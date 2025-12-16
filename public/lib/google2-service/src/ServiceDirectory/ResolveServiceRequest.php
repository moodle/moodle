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

namespace Google\Service\ServiceDirectory;

class ResolveServiceRequest extends \Google\Model
{
  /**
   * Optional. The filter applied to the endpoints of the resolved service.
   * General `filter` string syntax: ` ()` * `` can be `name`, `address`,
   * `port`, or `annotations.` for map field * `` can be `<`, `>`, `<=`, `>=`,
   * `!=`, `=`, `:`. Of which `:` means `HAS`, and is roughly the same as `=` *
   * `` must be the same data type as field * `` can be `AND`, `OR`, `NOT`
   * Examples of valid filters: * `annotations.owner` returns endpoints that
   * have a annotation with the key `owner`, this is the same as
   * `annotations:owner` * `annotations.protocol=gRPC` returns endpoints that
   * have key/value `protocol=gRPC` * `address=192.108.1.105` returns endpoints
   * that have this address * `port>8080` returns endpoints that have port
   * number larger than 8080 * `name>projects/my-project/locations/us-
   * east1/namespaces/my-namespace/services/my-service/endpoints/endpoint-c`
   * returns endpoints that have name that is alphabetically later than the
   * string, so "endpoint-e" is returned but "endpoint-a" is not *
   * `name=projects/my-project/locations/us-central1/namespaces/my-
   * namespace/services/my-service/endpoints/ep-1` returns the endpoint that has
   * an endpoint_id equal to `ep-1` * `annotations.owner!=sd AND
   * annotations.foo=bar` returns endpoints that have `owner` in annotation key
   * but value is not `sd` AND have key/value `foo=bar` * `doesnotexist.foo=bar`
   * returns an empty list. Note that endpoint doesn't have a field called
   * "doesnotexist". Since the filter does not match any endpoint, it returns no
   * results For more information about filtering, see [API
   * Filtering](https://aip.dev/160).
   *
   * @var string
   */
  public $endpointFilter;
  /**
   * Optional. The maximum number of endpoints to return. Defaults to 25.
   * Maximum is 100. If a value less than one is specified, the Default is used.
   * If a value greater than the Maximum is specified, the Maximum is used.
   *
   * @var int
   */
  public $maxEndpoints;

  /**
   * Optional. The filter applied to the endpoints of the resolved service.
   * General `filter` string syntax: ` ()` * `` can be `name`, `address`,
   * `port`, or `annotations.` for map field * `` can be `<`, `>`, `<=`, `>=`,
   * `!=`, `=`, `:`. Of which `:` means `HAS`, and is roughly the same as `=` *
   * `` must be the same data type as field * `` can be `AND`, `OR`, `NOT`
   * Examples of valid filters: * `annotations.owner` returns endpoints that
   * have a annotation with the key `owner`, this is the same as
   * `annotations:owner` * `annotations.protocol=gRPC` returns endpoints that
   * have key/value `protocol=gRPC` * `address=192.108.1.105` returns endpoints
   * that have this address * `port>8080` returns endpoints that have port
   * number larger than 8080 * `name>projects/my-project/locations/us-
   * east1/namespaces/my-namespace/services/my-service/endpoints/endpoint-c`
   * returns endpoints that have name that is alphabetically later than the
   * string, so "endpoint-e" is returned but "endpoint-a" is not *
   * `name=projects/my-project/locations/us-central1/namespaces/my-
   * namespace/services/my-service/endpoints/ep-1` returns the endpoint that has
   * an endpoint_id equal to `ep-1` * `annotations.owner!=sd AND
   * annotations.foo=bar` returns endpoints that have `owner` in annotation key
   * but value is not `sd` AND have key/value `foo=bar` * `doesnotexist.foo=bar`
   * returns an empty list. Note that endpoint doesn't have a field called
   * "doesnotexist". Since the filter does not match any endpoint, it returns no
   * results For more information about filtering, see [API
   * Filtering](https://aip.dev/160).
   *
   * @param string $endpointFilter
   */
  public function setEndpointFilter($endpointFilter)
  {
    $this->endpointFilter = $endpointFilter;
  }
  /**
   * @return string
   */
  public function getEndpointFilter()
  {
    return $this->endpointFilter;
  }
  /**
   * Optional. The maximum number of endpoints to return. Defaults to 25.
   * Maximum is 100. If a value less than one is specified, the Default is used.
   * If a value greater than the Maximum is specified, the Maximum is used.
   *
   * @param int $maxEndpoints
   */
  public function setMaxEndpoints($maxEndpoints)
  {
    $this->maxEndpoints = $maxEndpoints;
  }
  /**
   * @return int
   */
  public function getMaxEndpoints()
  {
    return $this->maxEndpoints;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResolveServiceRequest::class, 'Google_Service_ServiceDirectory_ResolveServiceRequest');
