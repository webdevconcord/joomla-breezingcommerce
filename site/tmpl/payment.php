<?php

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * PHP version 7.2.34
 *
 * @category   Payment script
 * @package    BreezingCommerce
 * @subpackage BreezingCommerce
 * @author     MustPay <info@mustpay.tech>
 * @copyright  2022 ConcordPay
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://concordpay.concord.ua
 * @since      3.8.0
 */

// Index.php/component/breezingcommerce/checkout-checkout?layout=thankyou&task=perform_checkout
/** @var stdClass $concordpay */
$baseUrl = $concordpay->url;

$layout = strtolower(JFactory::getApplication()->input->getCmd('layout', 'default'));

if ($layout === 'thankyou')
{
	echo '<h2 style="color:green">' . JText::_('COM_BREEZINGCOMMERCE_CONCORDPAY_APPROVED') . '</h2>';
}
elseif ($layout === 'payment_fail')
{
	echo '<h2 style="color:red">' . JText::_('COM_BREEZINGCOMMERCE_CONCORDPAY_DECLINED') . '</h2>';
}
else
{
	$postfields = array();
	$postfields['operation']    = 'Purchase';
	$postfields['merchant_id']  = $concordpay->merchant_id;
	$postfields['amount']       = $concordpay->amount;
	$postfields['order_id']     = $concordpay->order_id;
	$postfields['currency_iso'] = $concordpay->currency;
	$postfields['description']  = JText::_('COM_BREEZINGCOMMERCE_CONCORDPAY_ORDER_DESC') . ' '
		. htmlspecialchars($_SERVER['HTTP_HOST'])
		. ", {$concordpay->firstname} {$concordpay->lastname}, {$concordpay->phone}.";
	$postfields['approve_url']  = $concordpay->approve_url;
	$postfields['decline_url']  = $concordpay->decline_url;
	$postfields['cancel_url']   = $concordpay->cancel_url;
	$postfields['callback_url'] = $concordpay->callback_url;
	$postfields['language']     = $concordpay->language;

	// Statistics.
	$postfields['client_last_name']  = $concordpay->lastname;
	$postfields['client_first_name'] = $concordpay->firstname;
	$postfields['phone'] = $concordpay->phone;
	$postfields['email'] = $concordpay->email;

	$postfields['signature'] = $concordpay->api->getRequestSignature($postfields);

	// Generate payment form.
	$html = '<form id="form_concordpay" method="post" action="' . $baseUrl . '">';

	foreach ($postfields as $key => $field)
	{
		$html .= '<input type="hidden" name="' . $key . '" value="' . $field . '"/>' . PHP_EOL;
	}

	$html .= '</form>';
	$html .= "<script>window.addEventListener('DOMContentLoaded', function () { document.querySelector('#form_concordpay').submit(); })</script>";

	echo $html;
}
