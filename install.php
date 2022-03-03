<?php

defined('_JEXEC') or die('Restricted access');

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
class CrBcInstallation extends CrBcInstaller
{
	/**
	 * Plugin type.
	 *
	 * @var string
	 * @since 3.8.0
	 */
	public $type = 'payment';

	/**
	 * Plugin name.
	 *
	 * @var string
	 * @since 3.8.0
	 */
	public $name = 'concordpay';

	/**
	 * Install action.
	 *
	 * @return void
	 * @since 3.8.0
	 */
	public function install()
	{
		$tables = (array) JFactory::getDBO()->getTableList();

		foreach ($tables as $table)
		{
			if ($table == JFactory::getDBO()->getPrefix() . 'breezingcommerce_plugin_payment_concordpay')
			{
				return;
			}
		}

		$db = JFactory::getDBO();

		if (!$db)
		{
			throw new JDatabaseExceptionConnecting('Unable to connect to the Database.');
		}

		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__breezingcommerce_plugin_payment_concordpay` (
            `identity` int(11) NOT NULL,
            `merchant_id` varchar(255) NOT NULL,
            `secret_key` varchar(255) NOT NULL,
            `language` varchar(255) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8
          "
		);
		$db->query();

		$db->setQuery("ALTER TABLE `#__breezingcommerce_plugin_payment_concordpay` ADD PRIMARY KEY (`identity`)");
		$db->query();

		$db->setQuery("ALTER TABLE `#__breezingcommerce_plugin_payment_concordpay` MODIFY `identity` int(11) NOT NULL AUTO_INCREMENT");
		$db->query();
	}
}
