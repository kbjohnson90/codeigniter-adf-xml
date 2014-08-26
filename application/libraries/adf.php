<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* ADF
*
* Auto-Lead Data Format library for Code Igniter.
*
* @package codeigniter-adf-xml
* @author Kyle B. Johnson (http://www.kylebjohnson.me)
* @version 0.2.2
* @license The MIT License Copyright (c) 2014 Kyle B. Johnson
*/
class Adf {

	public $adf;

	function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->config('adf', TRUE);

		$this->adf = new SimpleXMLElement('<?adf version="1.0"?><adf/>');
		$this->prospect = $this->adf->addChild('prospect');

		$this->requestdate_date = date('c');

		$this->vendor_name = $this->ci->config->item('vendor_name', 'adf');

		$this->provider_name = $this->ci->config->item('provider_name', 'adf');
	}

	/**
	 * Send ADF XML to CRM
	 */
	public function send()
	{
		$to = $this->ci->config->item('crm_email', 'adf');
		if (is_array($to)) $to = implode(',', $to);
		
		$subject = $this->provider_name; //Promotion Title
		
		$headers  = 'From: ' . $this->ci->config->item('from_email', 'adf') . "\r\n";
		$headers .= "xml version: 1.0" . "\r\n";
		$headers .= "Content-Type: application/xhtml+xml; encoding=ISO-8859-1" . "\r\n";
		
		return mail($to, $subject, $this->xml(), $headers);
	}

	/**
	 * Output ADF XML
	 */
	public function xml()
	{
		$this->prospect->addChild('requestdate', $this->requestdate_date);
		$this->prospect->addChild('vendor')->addChild('name', $this->vendor_name);
		$this->prospect->addChild('provider')->addChild('name', $this->provider_name);		
		return $this->adf->saveXML();
	}

	/**
	 * Add a vehicle to the prospect
	 */
	public function vehicle($data, $interest = null, $status = null, $comments = null)
	{
		$this->vehicle = $this->prospect->addChild('vehicle');
		
		foreach ($data as $key => $value)
		{
			$this->vehicle->addChild($key, $value);
		}

		if ($interest) $this->vehicle->addAttribute('interest', $interest);
		if ($status) $this->vehicle->addAttribute('status', $status);
	}

	/**
	 * Add a customer to the prospect
	 */
	public function customer($name, $comments = null)
	{
		$this->customer = $this->prospect->addChild('customer');
		$this->contact = $this->customer->addChild('contact');

		if (is_array($name))
		{
			foreach ($name as $part => $value)
			{
				$name = $this->contact->addChild('name', $value);
				$name->addAttribute('part', $part);
			}
		}
		else
		{
			$name = $this->contact->addChild('name', $name);
			$name->addAttribute('part', 'full');
		}

		if ($comments) $this->customer->addChild('comments', $comments);

		return $this;
	}

	/**
	 * Add an email address to the customer contact
	 */
	public function email($email)
	{
		$this->contact->addChild('email', $email);

		return $this;
	}

	/**
	 * Add a phone number to the customer contact
	 */
	public function phone($number, $type = null, $time = null, $besttime = false)
	{
		$number = $this->contact->addChild('phone', $number);

		if ($type) $number->addAttribute('type', $type);
		if ($time) $number->addAttribute('time', $time);
		if ($besttime) $number->addAttribute('besttime', true);

		return $this;
	}

	/**
	 * Add an address to the customer contact
	 */
	public function address($data, $type = null)
	{
		$address = $this->contact->addChild('address');

		foreach ($data as $key => $value)
		{
			if (is_array($value) && $key == 'street')
			{
				foreach ($value as $line => $text)
				{
					$address->addChild('street', $text)
							->addAttribute('line', $line + 1);
				}
			}
			else
			{
				$address->addChild($key, $value);
			}
		}

		if ($type) $address->addAttribute('type', $type);

		return $this;
	}

	/**
	 * Add a vendor to the prospect
	 */
	public function vendor($name)
	{
		$this->vendor_name = $name;
	}

	/**
	 * Add a provider to the prospect
	 */
	public function provider($name)
	{
		$this->provider_name = $name;
	}

	/**
	 * Add a requestdate to the prospect
	 */
	public function requestdate($date)
	{
		$this->requestdate_date = date('c', $date);
	}

}

/* End of file adf.php */
/* Location: ./application/libraries/adf.php */
