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
   * The "text" collection of methods.
   * Typical usage is:
   *  <code>
   *   $freebaseService = new Google_FreebaseService(...);
   *   $text = $freebaseService->text;
   *  </code>
   */
  class Google_TextServiceResource extends Google_ServiceResource {


    /**
     * Returns blob attached to node at specified id as HTML (text.get)
     *
     * @param string $id The id of the item that you want data about
     * @param array $optParams Optional parameters.
     *
     * @opt_param string maxlength The max number of characters to return. Valid only for 'plain' format.
     * @opt_param string format Sanitizing transformation.
     * @return Google_ContentserviceGet
     */
    public function get($id, $optParams = array()) {
      $params = array('id' => $id);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_ContentserviceGet($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for Google_Freebase (v1).
 *
 * <p>
 * Lets you access the Freebase repository of open data.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="http://wiki.freebase.com/wiki/API" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_FreebaseService extends Google_Service {
  public $text;
  /**
   * Constructs the internal representation of the Freebase service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'freebase/v1/';
    $this->version = 'v1';
    $this->serviceName = 'freebase';

    $client->addService($this->serviceName, $this->version);
    $this->text = new Google_TextServiceResource($this, $this->serviceName, 'text', json_decode('{"methods": {"get": {"httpMethod": "GET", "response": {"$ref": "ContentserviceGet"}, "id": "freebase.text.get", "parameters": {"maxlength": {"type": "integer", "location": "query", "format": "uint32"}, "id": {"repeated": true, "required": true, "type": "string", "location": "path"}, "format": {"default": "plain", "enum": ["html", "plain", "raw"], "type": "string", "location": "query"}}, "path": "text{/id*}"}}}', true));
  }
}

class Google_ContentserviceGet extends Google_Model {
  public $result;
  public function setResult($result) {
    $this->result = $result;
  }
  public function getResult() {
    return $this->result;
  }
}
