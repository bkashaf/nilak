<?php

return [
	'expiration_minutes' => (int) env('PAYMENT_EXPIRATION_MINUTES', 30),
	'default_gateway' => env('PAYMENT_DEFAULT_GATEWAY', 'fake'),
	'gateways' => [
		'fake' => [
			'enabled' => env('PAYMENT_FAKE_ENABLED', true),
		],
		'zarinpal' => [
			'enabled' => env('ZARINPAL_ENABLED', false),
			'merchant_id' => env('ZARINPAL_MERCHANT_ID'),
			'endpoint' => env('ZARINPAL_ENDPOINT', 'https://payment.zarinpal.com/pg/v4/payment'),
			'amount_multiplier' => (int) env('ZARINPAL_AMOUNT_MULTIPLIER', 1),
		],
	],
];
