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
   * The "taskqueues" collection of methods.
   * Typical usage is:
   *  <code>
   *   $taskqueueService = new Google_TaskqueueService(...);
   *   $taskqueues = $taskqueueService->taskqueues;
   *  </code>
   */
  class Google_TaskqueuesServiceResource extends Google_ServiceResource {


    /**
     * Get detailed information about a TaskQueue. (taskqueues.get)
     *
     * @param string $project The project under which the queue lies.
     * @param string $taskqueue The id of the taskqueue to get the properties of.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool getStats Whether to get stats. Optional.
     * @return Google_TaskQueue
     */
    public function get($project, $taskqueue, $optParams = array()) {
      $params = array('project' => $project, 'taskqueue' => $taskqueue);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_TaskQueue($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "tasks" collection of methods.
   * Typical usage is:
   *  <code>
   *   $taskqueueService = new Google_TaskqueueService(...);
   *   $tasks = $taskqueueService->tasks;
   *  </code>
   */
  class Google_TasksServiceResource extends Google_ServiceResource {


    /**
     * Insert a new task in a TaskQueue (tasks.insert)
     *
     * @param string $project The project under which the queue lies
     * @param string $taskqueue The taskqueue to insert the task into
     * @param Google_Task $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Task
     */
    public function insert($project, $taskqueue, Google_Task $postBody, $optParams = array()) {
      $params = array('project' => $project, 'taskqueue' => $taskqueue, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Task($data);
      } else {
        return $data;
      }
    }
    /**
     * Get a particular task from a TaskQueue. (tasks.get)
     *
     * @param string $project The project under which the queue lies.
     * @param string $taskqueue The taskqueue in which the task belongs.
     * @param string $task The task to get properties of.
     * @param array $optParams Optional parameters.
     * @return Google_Task
     */
    public function get($project, $taskqueue, $task, $optParams = array()) {
      $params = array('project' => $project, 'taskqueue' => $taskqueue, 'task' => $task);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Task($data);
      } else {
        return $data;
      }
    }
    /**
     * List Tasks in a TaskQueue (tasks.list)
     *
     * @param string $project The project under which the queue lies.
     * @param string $taskqueue The id of the taskqueue to list tasks from.
     * @param array $optParams Optional parameters.
     * @return Google_Tasks2
     */
    public function listTasks($project, $taskqueue, $optParams = array()) {
      $params = array('project' => $project, 'taskqueue' => $taskqueue);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_Tasks2($data);
      } else {
        return $data;
      }
    }
    /**
     * Update tasks that are leased out of a TaskQueue. (tasks.update)
     *
     * @param string $project The project under which the queue lies.
     * @param string $taskqueue
     * @param string $task
     * @param int $newLeaseSeconds The new lease in seconds.
     * @param Google_Task $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Task
     */
    public function update($project, $taskqueue, $task, $newLeaseSeconds, Google_Task $postBody, $optParams = array()) {
      $params = array('project' => $project, 'taskqueue' => $taskqueue, 'task' => $task, 'newLeaseSeconds' => $newLeaseSeconds, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Task($data);
      } else {
        return $data;
      }
    }
    /**
     * Update tasks that are leased out of a TaskQueue. This method supports patch semantics.
     * (tasks.patch)
     *
     * @param string $project The project under which the queue lies.
     * @param string $taskqueue
     * @param string $task
     * @param int $newLeaseSeconds The new lease in seconds.
     * @param Google_Task $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Task
     */
    public function patch($project, $taskqueue, $task, $newLeaseSeconds, Google_Task $postBody, $optParams = array()) {
      $params = array('project' => $project, 'taskqueue' => $taskqueue, 'task' => $task, 'newLeaseSeconds' => $newLeaseSeconds, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Task($data);
      } else {
        return $data;
      }
    }
    /**
     * Delete a task from a TaskQueue. (tasks.delete)
     *
     * @param string $project The project under which the queue lies.
     * @param string $taskqueue The taskqueue to delete a task from.
     * @param string $task The id of the task to delete.
     * @param array $optParams Optional parameters.
     */
    public function delete($project, $taskqueue, $task, $optParams = array()) {
      $params = array('project' => $project, 'taskqueue' => $taskqueue, 'task' => $task);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
    /**
     * Lease 1 or more tasks from a TaskQueue. (tasks.lease)
     *
     * @param string $project The project under which the queue lies.
     * @param string $taskqueue The taskqueue to lease a task from.
     * @param int $numTasks The number of tasks to lease.
     * @param int $leaseSecs The lease in seconds.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool groupByTag When true, all returned tasks will have the same tag
     * @opt_param string tag The tag allowed for tasks in the response. Must only be specified if group_by_tag is true. If group_by_tag is true and tag is not specified the tag will be that of the oldest task by eta, i.e. the first available tag
     * @return Google_Tasks
     */
    public function lease($project, $taskqueue, $numTasks, $leaseSecs, $optParams = array()) {
      $params = array('project' => $project, 'taskqueue' => $taskqueue, 'numTasks' => $numTasks, 'leaseSecs' => $leaseSecs);
      $params = array_merge($params, $optParams);
      $data = $this->__call('lease', array($params));
      if ($this->useObjects()) {
        return new Google_Tasks($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for Google_Taskqueue (v1beta2).
 *
 * <p>
 * Lets you access a Google App Engine Pull Task Queue over REST.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="http://code.google.com/appengine/docs/python/taskqueue/rest.html" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_TaskqueueService extends Google_Service {
  public $taskqueues;
  public $tasks;
  /**
   * Constructs the internal representation of the Taskqueue service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'taskqueue/v1beta2/projects/';
    $this->version = 'v1beta2';
    $this->serviceName = 'taskqueue';

    $client->addService($this->serviceName, $this->version);
    $this->taskqueues = new Google_TaskqueuesServiceResource($this, $this->serviceName, 'taskqueues', json_decode('{"methods": {"get": {"scopes": ["https://www.googleapis.com/auth/taskqueue", "https://www.googleapis.com/auth/taskqueue.consumer"], "parameters": {"project": {"required": true, "type": "string", "location": "path"}, "taskqueue": {"required": true, "type": "string", "location": "path"}, "getStats": {"type": "boolean", "location": "query"}}, "id": "taskqueue.taskqueues.get", "httpMethod": "GET", "path": "{project}/taskqueues/{taskqueue}", "response": {"$ref": "TaskQueue"}}}}', true));
    $this->tasks = new Google_TasksServiceResource($this, $this->serviceName, 'tasks', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/taskqueue", "https://www.googleapis.com/auth/taskqueue.consumer"], "parameters": {"project": {"required": true, "type": "string", "location": "path"}, "taskqueue": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Task"}, "response": {"$ref": "Task"}, "httpMethod": "POST", "path": "{project}/taskqueues/{taskqueue}/tasks", "id": "taskqueue.tasks.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/taskqueue", "https://www.googleapis.com/auth/taskqueue.consumer"], "parameters": {"project": {"required": true, "type": "string", "location": "path"}, "taskqueue": {"required": true, "type": "string", "location": "path"}, "task": {"required": true, "type": "string", "location": "path"}}, "id": "taskqueue.tasks.get", "httpMethod": "GET", "path": "{project}/taskqueues/{taskqueue}/tasks/{task}", "response": {"$ref": "Task"}}, "list": {"scopes": ["https://www.googleapis.com/auth/taskqueue", "https://www.googleapis.com/auth/taskqueue.consumer"], "parameters": {"project": {"required": true, "type": "string", "location": "path"}, "taskqueue": {"required": true, "type": "string", "location": "path"}}, "id": "taskqueue.tasks.list", "httpMethod": "GET", "path": "{project}/taskqueues/{taskqueue}/tasks", "response": {"$ref": "Tasks2"}}, "update": {"scopes": ["https://www.googleapis.com/auth/taskqueue", "https://www.googleapis.com/auth/taskqueue.consumer"], "parameters": {"project": {"required": true, "type": "string", "location": "path"}, "taskqueue": {"required": true, "type": "string", "location": "path"}, "task": {"required": true, "type": "string", "location": "path"}, "newLeaseSeconds": {"required": true, "type": "integer", "location": "query", "format": "int32"}}, "request": {"$ref": "Task"}, "response": {"$ref": "Task"}, "httpMethod": "POST", "path": "{project}/taskqueues/{taskqueue}/tasks/{task}", "id": "taskqueue.tasks.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/taskqueue", "https://www.googleapis.com/auth/taskqueue.consumer"], "parameters": {"project": {"required": true, "type": "string", "location": "path"}, "taskqueue": {"required": true, "type": "string", "location": "path"}, "task": {"required": true, "type": "string", "location": "path"}, "newLeaseSeconds": {"required": true, "type": "integer", "location": "query", "format": "int32"}}, "request": {"$ref": "Task"}, "response": {"$ref": "Task"}, "httpMethod": "PATCH", "path": "{project}/taskqueues/{taskqueue}/tasks/{task}", "id": "taskqueue.tasks.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/taskqueue", "https://www.googleapis.com/auth/taskqueue.consumer"], "path": "{project}/taskqueues/{taskqueue}/tasks/{task}", "id": "taskqueue.tasks.delete", "parameters": {"project": {"required": true, "type": "string", "location": "path"}, "taskqueue": {"required": true, "type": "string", "location": "path"}, "task": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}, "lease": {"scopes": ["https://www.googleapis.com/auth/taskqueue", "https://www.googleapis.com/auth/taskqueue.consumer"], "parameters": {"groupByTag": {"type": "boolean", "location": "query"}, "leaseSecs": {"required": true, "type": "integer", "location": "query", "format": "int32"}, "project": {"required": true, "type": "string", "location": "path"}, "taskqueue": {"required": true, "type": "string", "location": "path"}, "tag": {"type": "string", "location": "query"}, "numTasks": {"required": true, "type": "integer", "location": "query", "format": "int32"}}, "id": "taskqueue.tasks.lease", "httpMethod": "POST", "path": "{project}/taskqueues/{taskqueue}/tasks/lease", "response": {"$ref": "Tasks"}}}}', true));

  }
}

class Google_Task extends Google_Model {
  public $kind;
  public $leaseTimestamp;
  public $id;
  public $tag;
  public $payloadBase64;
  public $queueName;
  public $enqueueTimestamp;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setLeaseTimestamp($leaseTimestamp) {
    $this->leaseTimestamp = $leaseTimestamp;
  }
  public function getLeaseTimestamp() {
    return $this->leaseTimestamp;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setTag($tag) {
    $this->tag = $tag;
  }
  public function getTag() {
    return $this->tag;
  }
  public function setPayloadBase64($payloadBase64) {
    $this->payloadBase64 = $payloadBase64;
  }
  public function getPayloadBase64() {
    return $this->payloadBase64;
  }
  public function setQueueName($queueName) {
    $this->queueName = $queueName;
  }
  public function getQueueName() {
    return $this->queueName;
  }
  public function setEnqueueTimestamp($enqueueTimestamp) {
    $this->enqueueTimestamp = $enqueueTimestamp;
  }
  public function getEnqueueTimestamp() {
    return $this->enqueueTimestamp;
  }
}

class Google_TaskQueue extends Google_Model {
  public $kind;
  protected $__statsType = 'Google_TaskQueueStats';
  protected $__statsDataType = '';
  public $stats;
  public $id;
  public $maxLeases;
  protected $__aclType = 'Google_TaskQueueAcl';
  protected $__aclDataType = '';
  public $acl;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setStats(Google_TaskQueueStats $stats) {
    $this->stats = $stats;
  }
  public function getStats() {
    return $this->stats;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setMaxLeases($maxLeases) {
    $this->maxLeases = $maxLeases;
  }
  public function getMaxLeases() {
    return $this->maxLeases;
  }
  public function setAcl(Google_TaskQueueAcl $acl) {
    $this->acl = $acl;
  }
  public function getAcl() {
    return $this->acl;
  }
}

class Google_TaskQueueAcl extends Google_Model {
  public $consumerEmails;
  public $producerEmails;
  public $adminEmails;
  public function setConsumerEmails(/* array(Google_string) */ $consumerEmails) {
    $this->assertIsArray($consumerEmails, 'Google_string', __METHOD__);
    $this->consumerEmails = $consumerEmails;
  }
  public function getConsumerEmails() {
    return $this->consumerEmails;
  }
  public function setProducerEmails(/* array(Google_string) */ $producerEmails) {
    $this->assertIsArray($producerEmails, 'Google_string', __METHOD__);
    $this->producerEmails = $producerEmails;
  }
  public function getProducerEmails() {
    return $this->producerEmails;
  }
  public function setAdminEmails(/* array(Google_string) */ $adminEmails) {
    $this->assertIsArray($adminEmails, 'Google_string', __METHOD__);
    $this->adminEmails = $adminEmails;
  }
  public function getAdminEmails() {
    return $this->adminEmails;
  }
}

class Google_TaskQueueStats extends Google_Model {
  public $oldestTask;
  public $leasedLastMinute;
  public $totalTasks;
  public $leasedLastHour;
  public function setOldestTask($oldestTask) {
    $this->oldestTask = $oldestTask;
  }
  public function getOldestTask() {
    return $this->oldestTask;
  }
  public function setLeasedLastMinute($leasedLastMinute) {
    $this->leasedLastMinute = $leasedLastMinute;
  }
  public function getLeasedLastMinute() {
    return $this->leasedLastMinute;
  }
  public function setTotalTasks($totalTasks) {
    $this->totalTasks = $totalTasks;
  }
  public function getTotalTasks() {
    return $this->totalTasks;
  }
  public function setLeasedLastHour($leasedLastHour) {
    $this->leasedLastHour = $leasedLastHour;
  }
  public function getLeasedLastHour() {
    return $this->leasedLastHour;
  }
}

class Google_Tasks extends Google_Model {
  protected $__itemsType = 'Google_Task';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public function setItems(/* array(Google_Task) */ $items) {
    $this->assertIsArray($items, 'Google_Task', __METHOD__);
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
}

class Google_Tasks2 extends Google_Model {
  protected $__itemsType = 'Google_Task';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public function setItems(/* array(Google_Task) */ $items) {
    $this->assertIsArray($items, 'Google_Task', __METHOD__);
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
}
