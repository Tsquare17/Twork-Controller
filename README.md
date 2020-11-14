# Twork Controller

## Make use of template controllers for WordPress.

### Installation
    composer require twork/controller

### Usage
```php
<?php

$config = require 'config.php';

new ControllerDispatcher([
    'templates' => [
        'front-page' => FrontPageController::class,
        'index' => IndexController::class,
        'page' => PageController::class,
    ]);
```
