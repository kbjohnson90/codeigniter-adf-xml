# CodeIgniter ADF XML

Submit Auto-Lead data using ADF XML, the industry standard data format.

## Installation

Download and merge the _application_ folder into your project.

Update _application/config/adf.php_

## Usage

```php
$this->load->library('adf');

$this->adf->init($customer, $vehicle, $provider);
$this->adf->send();
```
