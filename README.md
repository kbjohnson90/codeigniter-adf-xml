# CodeIgniter ADF XML

Submit Auto-Lead data using ADF XML, the industry standard data format.

## Installation

Download and merge the _application_ folder into your project.

Update _application/config/adf.php_

## Usage

```php
$this->load->library('adf');

$this->adf->customer('John Doe');

$this->adf->send();
```

## Example

```php
$this->adf->vehicle(array(
	'year' => '1967',
	'make' => 'Chevrolet',
	'model' => 'Camaro',
	'comments' => 'This is a vehicle comment.'
	), 'buy', 'used');

//$this->adf->customer('John Doe');
$this->adf->customer(array(
	'first' => 'John', 'last' => 'Doe'
	), 'This is a customer comment.');

$this->adf->email('noreply@fakemail.fake');

$this->adf->phone('111555555', 'voice', 'day');
$this->adf->phone('222555555', 'voice', 'evening');

$this->adf->address(array(
	'street' => '123 Easy Street',
	'postalcode' => '12345'
	), 'home');
```
