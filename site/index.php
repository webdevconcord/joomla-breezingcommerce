<?php

// No direct access.
defined('_JEXEC') or die('Restricted access');

$libpath = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_breezingcommerce' . DS . 'classes' . DS . 'plugin' . DS;
require_once $libpath . 'CrBcAPaymentSitePlugin.php';
require_once $libpath . 'CrBcPaymentSitePlugin.php';
require_once JPATH_SITE . '/media/breezingcommerce/plugins/payment/concordpay/api/ConcordPayApi.php';

/**
 * PHP version 7.2.34
 *
 * @category   Class
 * @package    BreezingCommerce
 * @subpackage BreezingCommerce
 * @author     MustPay <info@mustpay.tech>
 * @copyright  2022 ConcordPay
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://concordpay.concord.ua
 * @since      3.8.0
 */
class CrBc_Plugins_Payment_Concordpay_Site extends CrBcAPaymentSitePlugin implements CrBcPaymentSitePlugin
{
	/**
	 * @var string
	 * @since 3.8.0
	 */
	private $tx = '';

	/**
	 * Constructor method.
	 * @since 3.8.0
	 */
	public function __construct()
	{
		// Always call the parent constructor and always call it _first_
		parent::__construct();

		// Define the default table for built-in list/details view
		$this->table = '#__breezingcommerce_plugin_payment_concordpay';
		$this->requeryCount = 0;
	}

	/**
	 * Will return the tx id and is called right after a successfully and verified payment
	 * through $this->verifyPayment()
	 *
	 * @return mixed
	 * @since 3.8.0
	 */
	public function getPaymentTransactionId()
	{
		return $this->tx;
	}

	/**
	 * Callback handler.
	 *
	 * @param   CrBcCart $_cart Cart object
	 * @param   stdClass $order Order object
	 * @return boolean
	 * @throws Exception
	 *
	 * @since 3.8.0
	 */
	public function verifyPayment(CrBcCart $_cart, stdClass $order)
	{
		$response = json_decode(file_get_contents('php://input'), true);

		$db = JFactory::getDBO();

		if (!$db)
		{
			throw new JDatabaseExceptionConnecting('Unable to connect to the Database.');
		}

		$db->setQuery("SELECT * FROM " . $this->table . " ORDER BY `" . $this->identity_column . "` DESC LIMIT 1");
		$concordpay = $db->loadObject();

		if (!($concordpay instanceof stdClass))
		{
			throw new \RuntimeException(
				'No ConcordPay payment setup found, please create one first in Admin => BreezingCommerce => Plugins => ConcordPay'
			);
		}

		$_cart_items = $_cart->getItems(true);

		if (count($_cart_items) == 0)
		{
			throw new \RuntimeException('Empty cart');
		}

		$db->setQuery("SELECT * FROM #__breezingcommerce_plugins WHERE published = 1 AND type = 'shipping' ORDER BY `ordering`");
		$shipping_plugins = $db->loadAssocList();

		$data = CrBcCart::getData($order->id, $_cart_items, -1, -1);

		$_order_info = CrBcCart::getOrder(
			$order->id,
			$_cart,
			$_cart->getArray(),
			$_cart_items,
			$order->customer_id,
			$data,
			$shipping_plugins,
			array()
		);

		// Check order ID.
		$reference = $response['orderReference'];
		$order_details = explode(ConcordPayApi::ORDER_SEPARATOR, $reference);
		$order_id = (int) $order_details[0];

		if ($order_id !== (int) $order->id)
		{
			throw new \RuntimeException('Error: wrong order ID.');
		}

		// Check amount.
		if ($_order_info->grand_total !== (float) $response['amount'])
		{
			throw new \RuntimeException('Error: wrong amount.');
		}

		// Check currency.
		if ($_order_info->history_currency_code !== $response['currency'])
		{
			throw new \RuntimeException('Error: wrong currency.');
		}

		$db->setQuery("SELECT * FROM " . $this->table . " ORDER BY `" . $this->identity_column . "` DESC LIMIT 1");
		$concordpay      = $db->loadObject();
		$concordpay->api = new ConcordPayApi($concordpay->secret_key);

		// Check operation type.
		if (!in_array($response['type'], $concordpay->api->getAllowedOperationTypes()))
		{
			throw new \RuntimeException('Error: wrong operation type.');
		}

		// Check signature.
		if ($response['merchantSignature'] !== $concordpay->api->getResponseSignature($response))
		{
			throw new \RuntimeException('Error: wrong payment signature.');
		}

		// Примечание: в отличие от других плагинов ConcordPay, в данном плагине отсутствует обработчик
		// возврата платежа. Это связано с особенностями изменения состояний заказа в Breezing Commerce (после
		// создания заказ перестаёт быть модифицируемым и может быть только отменён).
		// Поэтому возврат платежа осуществляется из админ-панели Joomla вручную.
		if ($response['transactionStatus'] === ConcordPayApi::TRANSACTION_STATUS_APPROVED
			&& $response['type'] === ConcordPayApi::RESPONSE_TYPE_PAYMENT
		)
		{
			$this->tx = $response['transactionId'];

			return true;
		}

		return false;
	}

	/**
	 * Redirect to payment page.
	 *
	 * @return false|string
	 * @throws Exception
	 * @since 3.8.0
	 */
	public function getInitOutput()
	{
		$db = JFactory::getDBO();

		require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'CrBcCart.php';

		$_session_cart = JFactory::getSession()->get('crbc_cart', array());

		if (!isset($_session_cart['checkout']) || !isset($_session_cart['checkout']['payment_plugin_id']))
		{
			throw new \RuntimeException('User checkout not performed yet');
		}

		$payment_plugin_id = (int) $_session_cart['checkout']['payment_plugin_id'];

		$_cart = new CrBcCart($_session_cart);
		$_cart_items = $_cart->getItems(true);

		if (count($_cart_items) == 0)
		{
			throw new \RuntimeException('Trying to pay an empty cart');
		}

		$db->setQuery("SELECT * FROM #__breezingcommerce_plugins WHERE published = 1 AND type = 'shipping' ORDER BY `ordering`");
		$shipping_plugins = $db->loadAssocList();

		$data = CrBcCart::getData($_session_cart['order_id'], $_cart_items, -1, -1);

		$_order_info = CrBcCart::getOrder(
			$_session_cart['order_id'],
			$_cart,
			$_session_cart,
			$_cart_items,
			$_session_cart['customer_id'],
			$data,
			$shipping_plugins,
			array()
		);

		if ($_order_info->grand_total <= 0)
		{
			throw new \RuntimeException('Trying to use concordpay while the total is zero.');
		}

		$db->setQuery("SELECT * FROM " . $this->table . " ORDER BY `" . $this->identity_column . "` DESC LIMIT 1");
		$concordpay = $db->loadObject();

		if (!($concordpay instanceof stdClass))
		{
			throw new \RuntimeException(
				'No concordpay payment setup found, please create one first in Admin => BreezingCommerce => Plugins => ConcordPay'
			);
		}

		$concordpay->api = new ConcordPayApi($concordpay->secret_key);
		$concordpay->url = $concordpay->api->getApiUrl();
		$concordpay->business_name = CrBcHelpers::getBcConfig()->get('business_name', 'Default Shop Name');

		$concordpay->items  = $_cart_items;
		$concordpay->tax    = $_order_info->taxes;
		$concordpay->locale = JFactory::getApplication()->getLanguage()->getTag();
		$concordpay->locale = explode('-', $concordpay->locale);
		$concordpay->locale = $concordpay->locale[1];

		if (!empty($concordpay->force_locale))
		{
			$concordpay->locale = $concordpay->force_locale;
		}

		$concordpay->currency = $_cart->currency_code;

		if (!empty($concordpay->force_currency))
		{
			$concordpay->currency = $concordpay->force_currency;
		}

		$customer_id = $_session_cart['checkout']['userid'];

		$concordpay->amount    = $_order_info->grand_total;
		$concordpay->firstname = $_order_info->customer->firstname;
		$concordpay->lastname  = $_order_info->customer->lastname;
		$concordpay->phone     = $_order_info->customer->phone;
		$concordpay->email     = $_order_info->customer->email;

		$concordpay->no_shipping = $_cart->isVirtualOrder($_cart_items) ? 1 : 0;

		$concordpay->shipping = $_order_info->shipping_costs;
		$concordpay->order_id = $_session_cart['order_id'];

		$concordpay->payment_plugin_id = $payment_plugin_id;
		$concordpay->txref = $concordpay->order_id . '_' . time();

		$baseUrl = trim(JUri::root(), '/');
		$concordpay->approve_url  = $baseUrl . JRoute::_('index.php?option=com_breezingcommerce&controller=checkout&layout=thankyou&task=perform_checkout', false);
		$concordpay->decline_url  = $baseUrl . JRoute::_('index.php?option=com_breezingcommerce&controller=checkout&layout=payment_fail&task=perform_checkout', false);
		$concordpay->cancel_url   = $baseUrl . JRoute::_('index.php?option=com_breezingcommerce&controller=checkout&layout=payment_fail&task=perform_checkout', false);
		$concordpay->callback_url = JUri::getInstance()->toString() . '&order_id=' . $concordpay->order_id
			. '&verify_payment=1&payment_plugin_id=' . $concordpay->payment_plugin_id;

		ob_start();
		require_once JPATH_SITE . '/media/breezingcommerce/plugins/payment/concordpay/site/tmpl/payment.php';
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Optional method to prevent this payment from being used if it's not suitable.
	 * For example determine if user's location is actually suitable for the payment option.
	 * If it returns false, the option won't be displayed upon checkout and also not being processed.
	 *
	 * @return boolean
	 * @since 3.8.0
	 */
	public function isPaymentSuitable()
	{
		return true;
	}

	/**
	 * @return string
	 * @since 3.8.0
	 */
	public function getAfterPaymentInfo(): string
	{
		return JText::_('COM_BREEZINGCOMMERCE_CONCORDPAY_INFO_PAID');
	}

	/**
	 * @return string
	 * @since 3.8.0
	 */
	public function getPluginDisplayName(): string
	{
		return JText::_('COM_BREEZINGCOMMERCE_CONCORDPAY');
	}

	/**
	 * @return string
	 * @since 3.8.0
	 */
	public function getPluginIcon(): string
	{
		$img = JUri::root() . 'media/breezingcommerce/plugins/payment/concordpay/site/concordpay.png';

		return '<img src="' . $img . '" width="40px">';
	}

	/**
	 * @return string
	 * @since 3.8.0
	 */
	public function getPluginDescription(): string
	{
		return JText::_('COM_BREEZINGCOMMERCE_CONCORDPAY_DESCRIPTION');
	}

	/**
	 * @return string
	 * @since 3.8.0
	 */
	function getPaymentInfo()
	{
		$db = JFactory::getDBO();

		$db->setQuery("SELECT * FROM " . $this->table . " ORDER BY `" . $this->identity_column . "` DESC LIMIT 1");
		$row = $db->loadObject();

		if (!($row instanceof stdClass))
		{
			$row = new stdClass;
			$row->info = JText::_('No payment info available');
		}

		$id = $this->identity_column;

		$result = CrBcHelpers::loadTranslation($row->$id, 'plugin_payment_concordpay');

		if ($result)
		{
			$row->info = $result->body;
		}

		// TODO: Доработать этот метод, чтобы переводы текста работали так, как задумано разработчиками.
		return 'ConcordPay';
	}
}
