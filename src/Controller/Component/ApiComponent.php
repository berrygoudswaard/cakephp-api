<?php
namespace BerryGoudswaard\Api\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\Query;
use Cake\ORM\Entity;
use Cake\Utility\Inflector;

class ApiComponent extends Component
{
    protected $controller;
    private $statusCode = 200;
    private $message = 'OK';
    private $data = [];
    private $errors = [];
    private $config;

    public function initialize(array $config): void
    {
        $this->config = $config;
        $this->controller = $this->getController();
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function addData($key, $data, $options = [])
    {
        $itemKey = ($data instanceof Entity) ? 'item' : 'items';
        $this->data[$key] = [$itemKey => $data];
    }

    public function addErrors(Entity $entity)
    {
        $underscoredName = Inflector::underscore($entity->getSource());
        foreach ($entity->getErrors() as $key => $errors) {
            $this->errors[$underscoredName][$key]= $errors;
        }
    }

    public function output()
    {
        $data = array_filter([
            'message' => $this->message,
            'code' => $this->statusCode,
            'data' => $this->data,
            'errors' => $this->errors,
        ]);

        $response = $this->setCors();
        $response = $response->withStatus($this->statusCode);
        $response = $response->withType('application/json');
        $response = $response->withStringBody(json_encode($data, true));
        $this->controller->setResponse($response);
        return $response;
    }

    public function setCors()
    {
        if (!isset($this->config['cors'])) {
            return;
        }

        $cors = [];
        $request = $this->controller->getRequest();
        $response = $this->controller->getResponse();
        $originHeader = $request->getHeader('origin');
        $origin = reset ($originHeader);

        if (($allowHeaders = $this->config['cors']['allowHeaders'])) {
            $response = $response->withHeader('Access-Control-Allow-Headers', implode($allowHeaders, ','));
        }

        if (($allowMethods = $this->config['cors']['allowMethods'])) {
            $response = $response->withHeader('Access-Control-Allow-Methods', implode($allowMethods, ','));
        }

        if (($allowCredentials = $this->config['cors']['allowCredentials'])) {
            $response = $response->withHeader('Access-Control-Allow-Credentials', $allowCredentials);
        }

        if (($allowOrigins = $this->config['cors']['allowOrigins']) && in_array($origin, $allowOrigins)) {
            $response = $response->withHeader('Access-Control-Allow-Origin', $origin);
        }

        $this->controller->setResponse($response);
        return $response;
    }
}
