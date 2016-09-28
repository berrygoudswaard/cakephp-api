<?php
namespace BerryGoudswaard\Api\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\Query;

class ApiComponent extends Component
{
    protected $controller;
    private $statusCode = 200;
    private $message = 'OK';
    private $data = [];
    private $errors = [];

    public function initialize(array $config)
    {
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
        $itemKey = ($data instanceof \Cake\ORM\Entity) ? 'item' : 'items';
        $this->data[$key] = [$itemKey => $data];
    }

    public function addErrors($model, $table)
    {
        foreach ($model->errors() as $key => $errors) {
            $this->errors[$table->table()][$key]= $errors;
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
        $this->response->statusCode($this->statusCode);
        $this->controller->set($data);
    }

    public function setCors()
    {
        $this->response->header([
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE',
            'Access-Control-Allow-Origin' => '*'
        ]);
    }
}
