<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* ADF
*
* Auto-Lead Data Format library for Code Igniter.
*
* @package codeigniter-adf-xml
* @author Kyle B. Johnson (http://www.kylebjohnson.me)
* @version 0.1.0
* @license The MIT License Copyright (c) 2014 Kyle B. Johnson
*/
class Adf {

	public $requestdate;
	public $vehicle;
	public $customer;
	public $vendor;
	public $provider;

	function __construct() {
		$this->ci =& get_instance();
		$this->ci->load->config('adf', TRUE);

		$this->requestdate = date("c");

		$this->vendor = $this->ci->config->item('dealer_name', 'adf');
	}

	public function init($customer, $vehicle = null, $provider = null) {
		$this->customer = $customer;
		if(isset($vehicle)) $this->vehicle = $vehicle;
		if(isset($provider)) $this->provider = $provider;
	}

	public function send() {
	    $to = $this->ci->config->item('crm_email', 'adf');
	    if (is_array($to)) $to = implode(',', $to);

	    $subject = $this->provider; //Promotion Title

	    $headers  = 'From: ' . $this->ci->config->item('from_email', 'adf') . "\r\n";
	    $headers .= "xml version: 1.0" . "\r\n";
	    $headers .= "Content-Type: application/xhtml+xml; encoding=ISO-8859-1" . "\r\n";

		return mail($to, $subject, $this->xml(), $headers);
	}

	public function xml()
	{
        $adf = new SimpleXMLElement('<?adf version="1.0"?><adf/>');

        $prospect = $adf->addChild('prospect');

        //Request Date
        $requestdate = $prospect->addChild('requestdate', $this->requestdate);

        //Vehicle
        if (isset($this->vehicle)) {
        	$vehicle = $prospect->addChild('vehicle');

        	if (isset($this->vehicle['year'])) $year = $vehicle->addChild('year', $this->vehicle['year']);
        	if (isset($this->vehicle['make'])) $year = $vehicle->addChild('make', $this->vehicle['make']);
        	if (isset($this->vehicle['model'])) $year = $vehicle->addChild('model', $this->vehicle['model']);
        	if (isset($this->vehicle['comments'])) $year = $vehicle->addChild('comments', $this->vehicle['comments']);
        }

        //Customer
        if (isset($this->customer)) {
        	$customer = $prospect->addChild('customer');
        	
        	if (isset($this->customer['contact'])) {

				$contact = $customer->addChild('contact');

				//Name
				if (isset($this->customer['contact']['name'])) {

					if (!is_array($this->customer['contact']['name'])) {
						$name = $contact->addChild('name', $this->customer['contact']['name']);
						$name->addAttribute('part', 'full');
					} else {
						foreach ($this->customer['contact']['name'] as $key => $value) {
							$name[$key] = $contact->addChild('name', $value);
							if (is_string($key))  $name[$key]->addAttribute('part', $key);
						}
					}
				}

        		//Phone
        		if (isset($this->customer['contact']['phone'])) {

					if (!is_array($this->customer['contact']['phone'])) {
						$phone = $contact->addChild('phone', $this->customer['contact']['phone']);
					} else {
						foreach ($this->customer['contact']['phone'] as $key => $value) {
							$phone[$key] = $contact->addChild('phone', $value);
							$phone[$key]->addAttribute('type', 'voice'); //TODO: find a way to specify non-voice numbers
							if (is_string($key)) $phone[$key]->addAttribute('time', $key);
						}
					}
        		}

        		//Email
        		if (isset($this->customer['contact']['email'])) {
        			$email = $contact->addChild('email', $this->customer['contact']['email']);
        		}

        		//Address
        		if (isset($this->customer['contact']['address'])) {
        			$address = $contact->addChild('address');

        			foreach ($this->customer['contact']['address'] as $key => $value) {
        				$$key = $address->addChild($key, $value);
        			}
        		}
        	}

        	if (isset($this->customer['comments'])) $comments = $customer->addChild('comments', $this->customer['comments']);
        }

		//Vendor
		if (isset($this->vendor)) {
			$vendor  = $prospect->addChild('vendor');
			$contact = $vendor->addChild('contact');
			$contact->addChild('name', $this->vendor);
		}

		//Provider
		if (isset($this->provider)) {
			$provider = $prospect->addChild('provider');
			$contact = $provider->addChild('contact');
			$contact->addChild('name', $this->provider);
		}

	    return $adf->saveXML();
	}

}

/* End of file adf.php */
/* Location: ./application/libraries/adf.php */
