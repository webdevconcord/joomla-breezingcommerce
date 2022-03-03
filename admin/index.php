<?php

// No direct access.
defined('_JEXEC') or die('Restricted access');

$libpath = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_breezingcommerce' . DS . 'classes' . DS . 'plugin' . DS;
require_once $libpath . 'CrBcAPaymentAdminPlugin.php';
require_once $libpath . 'CrBcPaymentAdminPlugin.php';

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
class CrBc_Plugins_Payment_Concordpay_Admin extends CrBcAPaymentAdminPlugin implements CrBcPaymentAdminPlugin
{
	/**
	 * Constructor method.
	 *
	 * @since 3.8.0
	 */
	public function __construct()
	{
		require_once JPATH_SITE . '/administrator/components/com_breezingcommerce/classes/CrBcPane.php';

		// Always call the parent constructor and always call it _first_
		parent::__construct();

		// Define the default table for built-in list/details view
		$this->table = '#__breezingcommerce_plugin_payment_concordpay';
	}

	/**
	 * Allow raw for the info through beforeStore overwrite.
	 *
	 * @param   array $data
	 * @return array
	 *
	 * @since 3.8.0
	 */
	public function beforeStore($data)
	{
		return $data;
	}

	/**
	 * Allow raw for the info_translation through afterStore.
	 *
	 * @param   array $data Plugin settings and controller info.
	 * @return void
	 *
	 * @since 3.8.0
	 */
	public function afterStore($data)
	{
		$data['info_translation'] = JRequest::getVar('info_translation', '', 'POST', 'STRING', JREQUEST_ALLOWRAW);

		if (isset($data['info_translation']) && $data['info_translation'] != '')
		{
			$fields = array();
			$fields['body'] = trim($data['info_translation']);
			CrBcHelpers::storeTranslation($fields, $data[$this->identity_column], 'plugin_payment_concordpay');
		}
	}

	/**
	 * Called on render HTML
	 *
	 * @return void
	 * @since 3.8.0
	 */
	public function display()
	{
		$this->setDetailsView(array('apply', 'cancel'));
	}

	/**
	 * Override from CrBcAdminPlugin.
	 *
	 * @param   array $toolbarItems Toolbar items
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function setDetailsView($toolbarItems = array()): void
	{
		$this->setToolbar($toolbarItems);
		$this->template = 'details';

		$db = JFactory::getDBO();

		if (!$db)
		{
			throw new JDatabaseExceptionConnecting('Unable to connect to the Database.');
		}

		$db->setQuery("SELECT * FROM " . $this->table . " ORDER BY `" . $this->identity_column . "` DESC LIMIT 1");
		$row = $db->loadObject();

		if (!($row instanceof stdClass))
		{
			$row = new stdClass;
			$id = $this->identity_column;
			$row->$id = 0;

			$row->merchant_id = '';
			$row->secret_key  = '';
			$row->language    = '';
		}

		$this->assignRef('entity', $row);
	}

	/**
	 * Will be called when the plugin loads, right before it fires any other method.
	 *
	 * @param   CrBcCart $subject Parameter is optional and holds the current cart object
	 * @return void
	 * @since 3.8.0
	 */
	public function init($subject = null)
	{
		// Nothing yet
	}

	/**
	 * @return string
	 * @since 3.8.0
	 */
	public function getPaymentInfo(): string
	{
		return "ConcordPay Info";
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
	public function getPluginDescription(): string
	{
		return JText::_('COM_BREEZINGCOMMERCE_CONCORDPAY_DESCRIPTION');
	}
}
