<?php
/*
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


  /**
   * The "licenseAssignments" collection of methods.
   * Typical usage is:
   *  <code>
   *   $licensingService = new Google_LicensingService(...);
   *   $licenseAssignments = $licensingService->licenseAssignments;
   *  </code>
   */
  class Google_LicenseAssignmentsServiceResource extends Google_ServiceResource {


    /**
     * Assign License. (licenseAssignments.insert)
     *
     * @param string $productId Name for product
     * @param string $skuId Name for sku
     * @param Google_LicenseAssignmentInsert $postBody
     * @param array $optParams Optional parameters.
     * @return Google_LicenseAssignment
     */
    public function insert($productId, $skuId, Google_LicenseAssignmentInsert $postBody, $optParams = array()) {
      $params = array('productId' => $productId, 'skuId' => $skuId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_LicenseAssignment($data);
      } else {
        return $data;
      }
    }
    /**
     * Get license assignment of a particular product and sku for a user (licenseAssignments.get)
     *
     * @param string $productId Name for product
     * @param string $skuId Name for sku
     * @param string $userId email id or unique Id of the user
     * @param array $optParams Optional parameters.
     * @return Google_LicenseAssignment
     */
    public function get($productId, $skuId, $userId, $optParams = array()) {
      $params = array('productId' => $productId, 'skuId' => $skuId, 'userId' => $userId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_LicenseAssignment($data);
      } else {
        return $data;
      }
    }
    /**
     * List license assignments for given product and sku of the customer.
     * (licenseAssignments.listForProductAndSku)
     *
     * @param string $productId Name for product
     * @param string $skuId Name for sku
     * @param string $customerId CustomerId represents the customer for whom licenseassignments are queried
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken Token to fetch the next page.Optional. By default server will return first page
     * @opt_param string maxResults Maximum number of campaigns to return at one time. Must be positive. Optional. Default value is 100.
     * @return Google_LicenseAssignmentList
     */
    public function listForProductAndSku($productId, $skuId, $customerId, $optParams = array()) {
      $params = array('productId' => $productId, 'skuId' => $skuId, 'customerId' => $customerId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('listForProductAndSku', array($params));
      if ($this->useObjects()) {
        return new Google_LicenseAssignmentList($data);
      } else {
        return $data;
      }
    }
    /**
     * List license assignments for given product of the customer. (licenseAssignments.listForProduct)
     *
     * @param string $productId Name for product
     * @param string $customerId CustomerId represents the customer for whom licenseassignments are queried
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken Token to fetch the next page.Optional. By default server will return first page
     * @opt_param string maxResults Maximum number of campaigns to return at one time. Must be positive. Optional. Default value is 100.
     * @return Google_LicenseAssignmentList
     */
    public function listForProduct($productId, $customerId, $optParams = array()) {
      $params = array('productId' => $productId, 'customerId' => $customerId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('listForProduct', array($params));
      if ($this->useObjects()) {
        return new Google_LicenseAssignmentList($data);
      } else {
        return $data;
      }
    }
    /**
     * Assign License. (licenseAssignments.update)
     *
     * @param string $productId Name for product
     * @param string $skuId Name for sku for which license would be revoked
     * @param string $userId email id or unique Id of the user
     * @param Google_LicenseAssignment $postBody
     * @param array $optParams Optional parameters.
     * @return Google_LicenseAssignment
     */
    public function update($productId, $skuId, $userId, Google_LicenseAssignment $postBody, $optParams = array()) {
      $params = array('productId' => $productId, 'skuId' => $skuId, 'userId' => $userId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_LicenseAssignment($data);
      } else {
        return $data;
      }
    }
    /**
     * Assign License. This method supports patch semantics. (licenseAssignments.patch)
     *
     * @param string $productId Name for product
     * @param string $skuId Name for sku for which license would be revoked
     * @param string $userId email id or unique Id of the user
     * @param Google_LicenseAssignment $postBody
     * @param array $optParams Optional parameters.
     * @return Google_LicenseAssignment
     */
    public function patch($productId, $skuId, $userId, Google_LicenseAssignment $postBody, $optParams = array()) {
      $params = array('productId' => $productId, 'skuId' => $skuId, 'userId' => $userId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_LicenseAssignment($data);
      } else {
        return $data;
      }
    }
    /**
     * Revoke License. (licenseAssignments.delete)
     *
     * @param string $productId Name for product
     * @param string $skuId Name for sku
     * @param string $userId email id or unique Id of the user
     * @param array $optParams Optional parameters.
     */
    public function delete($productId, $skuId, $userId, $optParams = array()) {
      $params = array('productId' => $productId, 'skuId' => $skuId, 'userId' => $userId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

/**
 * Service definition for Google_Licensing (v1).
 *
 * <p>
 * Licensing API to view and manage license for your domain.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://developers.google.com/google-apps/licensing/" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_LicensingService extends Google_Service {
  public $licenseAssignments;
  /**
   * Constructs the internal representation of the Licensing service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'apps/licensing/v1/product/';
    $this->version = 'v1';
    $this->serviceName = 'licensing';

    $client->addService($this->serviceName, $this->version);
    $this->licenseAssignments = new Google_LicenseAssignmentsServiceResource($this, $this->serviceName, 'licenseAssignments', json_decode('{"methods": {"insert": {"parameters": {"skuId": {"required": true, "type": "string", "location": "path"}, "productId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "LicenseAssignmentInsert"}, "id": "licensing.licenseAssignments.insert", "httpMethod": "POST", "path": "{productId}/sku/{skuId}/user", "response": {"$ref": "LicenseAssignment"}}, "get": {"httpMethod": "GET", "response": {"$ref": "LicenseAssignment"}, "id": "licensing.licenseAssignments.get", "parameters": {"skuId": {"required": true, "type": "string", "location": "path"}, "userId": {"required": true, "type": "string", "location": "path"}, "productId": {"required": true, "type": "string", "location": "path"}}, "path": "{productId}/sku/{skuId}/user/{userId}"}, "listForProductAndSku": {"httpMethod": "GET", "response": {"$ref": "LicenseAssignmentList"}, "id": "licensing.licenseAssignments.listForProductAndSku", "parameters": {"pageToken": {"default": "", "type": "string", "location": "query"}, "skuId": {"required": true, "type": "string", "location": "path"}, "customerId": {"required": true, "type": "string", "location": "query"}, "maxResults": {"format": "uint32", "default": "100", "maximum": "1000", "minimum": "1", "location": "query", "type": "integer"}, "productId": {"required": true, "type": "string", "location": "path"}}, "path": "{productId}/sku/{skuId}/users"}, "listForProduct": {"httpMethod": "GET", "response": {"$ref": "LicenseAssignmentList"}, "id": "licensing.licenseAssignments.listForProduct", "parameters": {"pageToken": {"default": "", "type": "string", "location": "query"}, "customerId": {"required": true, "type": "string", "location": "query"}, "maxResults": {"format": "uint32", "default": "100", "maximum": "1000", "minimum": "1", "location": "query", "type": "integer"}, "productId": {"required": true, "type": "string", "location": "path"}}, "path": "{productId}/users"}, "update": {"parameters": {"skuId": {"required": true, "type": "string", "location": "path"}, "userId": {"required": true, "type": "string", "location": "path"}, "productId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "LicenseAssignment"}, "id": "licensing.licenseAssignments.update", "httpMethod": "PUT", "path": "{productId}/sku/{skuId}/user/{userId}", "response": {"$ref": "LicenseAssignment"}}, "patch": {"parameters": {"skuId": {"required": true, "type": "string", "location": "path"}, "userId": {"required": true, "type": "string", "location": "path"}, "productId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "LicenseAssignment"}, "id": "licensing.licenseAssignments.patch", "httpMethod": "PATCH", "path": "{productId}/sku/{skuId}/user/{userId}", "response": {"$ref": "LicenseAssignment"}}, "delete": {"httpMethod": "DELETE", "id": "licensing.licenseAssignments.delete", "parameters": {"skuId": {"required": true, "type": "string", "location": "path"}, "userId": {"required": true, "type": "string", "location": "path"}, "productId": {"required": true, "type": "string", "location": "path"}}, "path": "{productId}/sku/{skuId}/user/{userId}"}}}', true));

  }
}

class Google_LicenseAssignment extends Google_Model {
  public $skuId;
  public $kind;
  public $userId;
  public $etags;
  public $selfLink;
  public $productId;
  public function setSkuId($skuId) {
    $this->skuId = $skuId;
  }
  public function getSkuId() {
    return $this->skuId;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setUserId($userId) {
    $this->userId = $userId;
  }
  public function getUserId() {
    return $this->userId;
  }
  public function setEtags($etags) {
    $this->etags = $etags;
  }
  public function getEtags() {
    return $this->etags;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setProductId($productId) {
    $this->productId = $productId;
  }
  public function getProductId() {
    return $this->productId;
  }
}

class Google_LicenseAssignmentInsert extends Google_Model {
  public $userId;
  public function setUserId($userId) {
    $this->userId = $userId;
  }
  public function getUserId() {
    return $this->userId;
  }
}

class Google_LicenseAssignmentList extends Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'Google_LicenseAssignment';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems(/* array(Google_LicenseAssignment) */ $items) {
    $this->assertIsArray($items, 'Google_LicenseAssignment', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}
