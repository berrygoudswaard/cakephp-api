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

    public function initialize(array $config)
    {
        $this->config = $config;
        $this->controller = $this->_registry->getController();
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
            '_serialize' => ['message', 'code', 'data', 'errors']
        ]);

        $this->setCors();
        $this->response = $this->response->withStatus($this->statusCode);
        $this->controller->set($data);
    }

    public function setCors()
    {
        if (!isset($this->config['cors'])) {
            return;
        }

        $cors = [];
        $originHeader = $this->controller->request->getHeader('origin');
        $origin = reset ($originHeader);

        if (($allowHeaders = $this->config['cors']['allowHeaders'])) {
            $this->response = $this->response->withHeader('Access-Control-Allow-Headers', implode($allowHeaders, ','));
        }

        if (($allowMethods = $this->config['cors']['allowMethods'])) {
            $this->response = $this->response->withHeader('Access-Control-Allow-Methods', implode($allowMethods, ','));
        }

        if (($allowCredentials = $this->config['cors']['allowCredentials'])) {
            $this->response = $this->response->withHeader('Access-Control-Allow-Credentials', $allowCredentials);
        }

        if (($allowOrigins = $this->config['cors']['allowOrigins']) && in_array($origin, $allowOrigins)) {
            $this->response = $this->response->withHeader('Access-Control-Allow-Origin', $origin);
        }
    }
}
