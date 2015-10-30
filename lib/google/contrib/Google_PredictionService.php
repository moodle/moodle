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
   * The "trainedmodels" collection of methods.
   * Typical usage is:
   *  <code>
   *   $predictionService = new Google_PredictionService(...);
   *   $trainedmodels = $predictionService->trainedmodels;
   *  </code>
   */
  class Google_TrainedmodelsServiceResource extends Google_ServiceResource {


    /**
     * Submit model id and request a prediction (trainedmodels.predict)
     *
     * @param string $id The unique name for the predictive model.
     * @param Google_Input $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Output
     */
    public function predict($id, Google_Input $postBody, $optParams = array()) {
      $params = array('id' => $id, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('predict', array($params));
      if ($this->useObjects()) {
        return new Google_Output($data);
      } else {
        return $data;
      }
    }
    /**
     * Begin training your model. (trainedmodels.insert)
     *
     * @param Google_Training $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Training
     */
    public function insert(Google_Training $postBody, $optParams = array()) {
      $params = array('postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Training($data);
      } else {
        return $data;
      }
    }
    /**
     * Check training status of your model. (trainedmodels.get)
     *
     * @param string $id The unique name for the predictive model.
     * @param array $optParams Optional parameters.
     * @return Google_Training
     */
    public function get($id, $optParams = array()) {
      $params = array('id' => $id);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Training($data);
      } else {
        return $data;
      }
    }
    /**
     * Add new data to a trained model. (trainedmodels.update)
     *
     * @param string $id The unique name for the predictive model.
     * @param Google_Update $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Training
     */
    public function update($id, Google_Update $postBody, $optParams = array()) {
      $params = array('id' => $id, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Training($data);
      } else {
        return $data;
      }
    }
    /**
     * Delete a trained model. (trainedmodels.delete)
     *
     * @param string $id The unique name for the predictive model.
     * @param array $optParams Optional parameters.
     */
    public function delete($id, $optParams = array()) {
      $params = array('id' => $id);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "hostedmodels" collection of methods.
   * Typical usage is:
   *  <code>
   *   $predictionService = new Google_PredictionService(...);
   *   $hostedmodels = $predictionService->hostedmodels;
   *  </code>
   */
  class Google_HostedmodelsServiceResource extends Google_ServiceResource {


    /**
     * Submit input and request an output against a hosted model. (hostedmodels.predict)
     *
     * @param string $hostedModelName The name of a hosted model.
     * @param Google_Input $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Output
     */
    public function predict($hostedModelName, Google_Input $postBody, $optParams = array()) {
      $params = array('hostedModelName' => $hostedModelName, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('predict', array($params));
      if ($this->useObjects()) {
        return new Google_Output($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for Google_Prediction (v1.4).
 *
 * <p>
 * Lets you access a cloud hosted machine learning service that makes it easy to build smart apps
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="http://code.google.com/apis/predict/docs/developer-guide.html" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_PredictionService extends Google_Service {
  public $trainedmodels;
  public $hostedmodels;
  /**
   * Constructs the internal representation of the Prediction service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'prediction/v1.4/';
    $this->version = 'v1.4';
    $this->serviceName = 'prediction';

    $client->addService($this->serviceName, $this->version);
    $this->trainedmodels = new Google_TrainedmodelsServiceResource($this, $this->serviceName, 'trainedmodels', json_decode('{"methods": {"predict": {"scopes": ["https://www.googleapis.com/auth/prediction"], "parameters": {"id": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Input"}, "response": {"$ref": "Output"}, "httpMethod": "POST", "path": "trainedmodels/{id}/predict", "id": "prediction.trainedmodels.predict"}, "insert": {"scopes": ["https://www.googleapis.com/auth/devstorage.read_only", "https://www.googleapis.com/auth/prediction"], "request": {"$ref": "Training"}, "response": {"$ref": "Training"}, "httpMethod": "POST", "path": "trainedmodels", "id": "prediction.trainedmodels.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/prediction"], "parameters": {"id": {"required": true, "type": "string", "location": "path"}}, "id": "prediction.trainedmodels.get", "httpMethod": "GET", "path": "trainedmodels/{id}", "response": {"$ref": "Training"}}, "update": {"scopes": ["https://www.googleapis.com/auth/prediction"], "parameters": {"id": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Update"}, "response": {"$ref": "Training"}, "httpMethod": "PUT", "path": "trainedmodels/{id}", "id": "prediction.trainedmodels.update"}, "delete": {"scopes": ["https://www.googleapis.com/auth/prediction"], "path": "trainedmodels/{id}", "id": "prediction.trainedmodels.delete", "parameters": {"id": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->hostedmodels = new Google_HostedmodelsServiceResource($this, $this->serviceName, 'hostedmodels', json_decode('{"methods": {"predict": {"scopes": ["https://www.googleapis.com/auth/prediction"], "parameters": {"hostedModelName": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Input"}, "response": {"$ref": "Output"}, "httpMethod": "POST", "path": "hostedmodels/{hostedModelName}/predict", "id": "prediction.hostedmodels.predict"}}}', true));

  }
}

class Google_Input extends Google_Model {
  protected $__inputType = 'Google_InputInput';
  protected $__inputDataType = '';
  public $input;
  public function setInput(Google_InputInput $input) {
    $this->input = $input;
  }
  public function getInput() {
    return $this->input;
  }
}

class Google_InputInput extends Google_Model {
  public $csvInstance;
  public function setCsvInstance(/* array(Google_object) */ $csvInstance) {
    $this->assertIsArray($csvInstance, 'Google_object', __METHOD__);
    $this->csvInstance = $csvInstance;
  }
  public function getCsvInstance() {
    return $this->csvInstance;
  }
}

class Google_Output extends Google_Model {
  public $kind;
  public $outputLabel;
  public $id;
  protected $__outputMultiType = 'Google_OutputOutputMulti';
  protected $__outputMultiDataType = 'array';
  public $outputMulti;
  public $outputValue;
  public $selfLink;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setOutputLabel($outputLabel) {
    $this->outputLabel = $outputLabel;
  }
  public function getOutputLabel() {
    return $this->outputLabel;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setOutputMulti(/* array(Google_OutputOutputMulti) */ $outputMulti) {
    $this->assertIsArray($outputMulti, 'Google_OutputOutputMulti', __METHOD__);
    $this->outputMulti = $outputMulti;
  }
  public function getOutputMulti() {
    return $this->outputMulti;
  }
  public function setOutputValue($outputValue) {
    $this->outputValue = $outputValue;
  }
  public function getOutputValue() {
    return $this->outputValue;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class Google_OutputOutputMulti extends Google_Model {
  public $score;
  public $label;
  public function setScore($score) {
    $this->score = $score;
  }
  public function getScore() {
    return $this->score;
  }
  public function setLabel($label) {
    $this->label = $label;
  }
  public function getLabel() {
    return $this->label;
  }
}

class Google_Training extends Google_Model {
  public $kind;
  public $storageDataLocation;
  public $storagePMMLModelLocation;
  protected $__dataAnalysisType = 'Google_TrainingDataAnalysis';
  protected $__dataAnalysisDataType = '';
  public $dataAnalysis;
  public $trainingStatus;
  protected $__modelInfoType = 'Google_TrainingModelInfo';
  protected $__modelInfoDataType = '';
  public $modelInfo;
  public $storagePMMLLocation;
  public $id;
  public $selfLink;
  public $utility;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setStorageDataLocation($storageDataLocation) {
    $this->storageDataLocation = $storageDataLocation;
  }
  public function getStorageDataLocation() {
    return $this->storageDataLocation;
  }
  public function setStoragePMMLModelLocation($storagePMMLModelLocation) {
    $this->storagePMMLModelLocation = $storagePMMLModelLocation;
  }
  public function getStoragePMMLModelLocation() {
    return $this->storagePMMLModelLocation;
  }
  public function setDataAnalysis(Google_TrainingDataAnalysis $dataAnalysis) {
    $this->dataAnalysis = $dataAnalysis;
  }
  public function getDataAnalysis() {
    return $this->dataAnalysis;
  }
  public function setTrainingStatus($trainingStatus) {
    $this->trainingStatus = $trainingStatus;
  }
  public function getTrainingStatus() {
    return $this->trainingStatus;
  }
  public function setModelInfo(Google_TrainingModelInfo $modelInfo) {
    $this->modelInfo = $modelInfo;
  }
  public function getModelInfo() {
    return $this->modelInfo;
  }
  public function setStoragePMMLLocation($storagePMMLLocation) {
    $this->storagePMMLLocation = $storagePMMLLocation;
  }
  public function getStoragePMMLLocation() {
    return $this->storagePMMLLocation;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setUtility(/* array(Google_double) */ $utility) {
    $this->assertIsArray($utility, 'Google_double', __METHOD__);
    $this->utility = $utility;
  }
  public function getUtility() {
    return $this->utility;
  }
}

class Google_TrainingDataAnalysis extends Google_Model {
  public $warnings;
  public function setWarnings(/* array(Google_string) */ $warnings) {
    $this->assertIsArray($warnings, 'Google_string', __METHOD__);
    $this->warnings = $warnings;
  }
  public function getWarnings() {
    return $this->warnings;
  }
}

class Google_TrainingModelInfo extends Google_Model {
  public $confusionMatrixRowTotals;
  public $numberLabels;
  public $confusionMatrix;
  public $meanSquaredError;
  public $modelType;
  public $numberInstances;
  public $classWeightedAccuracy;
  public $classificationAccuracy;
  public function setConfusionMatrixRowTotals($confusionMatrixRowTotals) {
    $this->confusionMatrixRowTotals = $confusionMatrixRowTotals;
  }
  public function getConfusionMatrixRowTotals() {
    return $this->confusionMatrixRowTotals;
  }
  public function setNumberLabels($numberLabels) {
    $this->numberLabels = $numberLabels;
  }
  public function getNumberLabels() {
    return $this->numberLabels;
  }
  public function setConfusionMatrix($confusionMatrix) {
    $this->confusionMatrix = $confusionMatrix;
  }
  public function getConfusionMatrix() {
    return $this->confusionMatrix;
  }
  public function setMeanSquaredError($meanSquaredError) {
    $this->meanSquaredError = $meanSquaredError;
  }
  public function getMeanSquaredError() {
    return $this->meanSquaredError;
  }
  public function setModelType($modelType) {
    $this->modelType = $modelType;
  }
  public function getModelType() {
    return $this->modelType;
  }
  public function setNumberInstances($numberInstances) {
    $this->numberInstances = $numberInstances;
  }
  public function getNumberInstances() {
    return $this->numberInstances;
  }
  public function setClassWeightedAccuracy($classWeightedAccuracy) {
    $this->classWeightedAccuracy = $classWeightedAccuracy;
  }
  public function getClassWeightedAccuracy() {
    return $this->classWeightedAccuracy;
  }
  public function setClassificationAccuracy($classificationAccuracy) {
    $this->classificationAccuracy = $classificationAccuracy;
  }
  public function getClassificationAccuracy() {
    return $this->classificationAccuracy;
  }
}

class Google_Update extends Google_Model {
  public $csvInstance;
  public $label;
  public function setCsvInstance(/* array(Google_object) */ $csvInstance) {
    $this->assertIsArray($csvInstance, 'Google_object', __METHOD__);
    $this->csvInstance = $csvInstance;
  }
  public function getCsvInstance() {
    return $this->csvInstance;
  }
  public function setLabel($label) {
    $this->label = $label;
  }
  public function getLabel() {
    return $this->label;
  }
}
