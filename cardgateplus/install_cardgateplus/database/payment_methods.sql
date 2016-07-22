INSERT INTO `%s`
	(
	`paymentid`,
	`payment_method`,
	`payment_details`,
	`payment_template`,
	`payment_script`,
	`protocol`,
	`orderby`,
	`active`,
	`is_cod`,
	`af_check`,
	`processor_file`,
	`surcharge`,
	`surcharge_type`
	)
VALUES
	(
	null,
	'CardGatePlus %s',
	'Please select the payment method you wish to use',
	'customer/main/payment_cgp_%3$s.tpl',
	'payment_cc.php',
	'http',
	999,
	'N',
	'N',
	'N',
	'cc_cgp_%3$s.php',
	0.00,
	'$'
	)