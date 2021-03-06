<?php

namespace InsalesApi;

use InsalesApi\Api\PaymentGateway\PaymentGateway;
use InsalesApi\Api\Webhook\Webhook;
use InsalesApi\Exception\SDKException;

class InsalesAPI
{
	const MESSAGE_FORMAT_XML = 'xml';
	const MESSAGE_FORMAT_JSON = 'json';
	/**
	 * @var TransportInterface
	 */
	protected $transport;
	/**
	 * @var string
	 */
	protected $messageFormat;
	
	public function __construct(
		TransportInterface $transport,
		$messageFormat = self::MESSAGE_FORMAT_JSON
	) {
		if (!in_array(
			$messageFormat,
			array(
				self::MESSAGE_FORMAT_JSON,
				self::MESSAGE_FORMAT_XML
			)
		)) {
			throw new SDKException('Unsupported message format ' . $messageFormat . '! Use xml or json.');
		} else {
			$this->transport = $transport;
		}
		
		if ($messageFormat == self::MESSAGE_FORMAT_XML) {
			if (!class_exists('SimpleXMLElement')) {
				throw new SDKException('XML Module not found - it is required to parse XML messages');
			}
			$this->transport->setHeaders(
				array(
					'Content-Type: application/xml; charset=utf-8',
					'Accept: application/xml',
					"Expect:"
				)
			);
		} else {
			$this->transport->setHeaders(
				array(
					'Content-Type: application/json; charset=utf-8',
					'Accept: application/json',
					"Expect:"
				)
			);
		}
		$this->messageFormat = $messageFormat;
	}
	
	/**
	 * @return Webhook
	 */
	public function webhook()
	{
		return new Webhook(
			$this->transport,
			$this->messageFormat
		);
	}
	
	/**
	 * @return PaymentGateway
	 */
	public function paymentGateway()
	{
		return new PaymentGateway(
			$this->transport,
			$this->messageFormat
		);
	}
}