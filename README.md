# CakePHP API Component
This plugin contains a component that make it easier to create API output with CakePHP

## Requirements

* CakePHP 3.1+

## Installation
```sh
composer require berrygoudswaard/cakephp-api
```

## Usage

In your app's `config/bootstrap.php` add:

```php
Plugin::load('BerryGoudswaard/Api');
```

Then load the component where you need it by adding the following code to your controller:

```php
$this->loadComponent('BerryGoudswaard/Api.Api');
```

## Output data

```php
public function index()
{
    $tags = $this->Tags->find();

    $this->Api->addData('tags', $tags);
    return $this->Api->output();
}
```

The code above will output something like: 

```json
{
    "message": "OK",
    "code": 200,
    "data": {
        "tags": {
            "items": [
                {
                    "id": 1,
                    "tag": "cakephp"
                }, {
                    "id": 2,
                    "tag": "plugin"
                }
            ]
        }
    }
}
```
