<?php

/**
 * PHP version 7.2.34
 *
 * @category   Template
 * @package    BreezingCommerce
 * @subpackage BreezingCommerce
 * @author     MustPay <info@mustpay.tech>
 * @copyright  2022 ConcordPay
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://concordpay.concord.ua
 * @since      3.8.0
 */

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
  function submitbutton(pressbutton) {
    submitform(pressbutton);
  }

  /**
   * Submit the admin form
   */
  function submitform(pressbutton) {
    if (pressbutton) {
      document.adminForm.task.value = pressbutton;
    }
    if (typeof document.adminForm.onsubmit == "function") {
      document.adminForm.onsubmit();
    }
    document.adminForm.submit();
  }

  function crbc_submitbutton(pressbutton) {
    switch (pressbutton) {
      case 'plugin_cancel':
        pressbutton = 'cancel';
        submitform(pressbutton);
        break;
      case 'plugin_apply':
        var error = false;

        if (!error) {
          submitform(pressbutton);
        }

        break;
    }
  }

  // Joomla 1.6 compat.
  if (typeof Joomla != 'undefined') {
    Joomla.submitbutton = crbc_submitbutton;
  }
  // Joomla 1.5 compat.
  submitbutton = crbc_submitbutton;
</script>

<div class="form-horizontal">
  <div class="control-group">
    <img src="<?php echo JUri::root() . 'media/breezingcommerce/plugins/payment/concordpay/admin/concordpay.svg'; ?>"
       width="120px" style="margin-bottom:10px" alt="ConcordPay">
  </div>
  <div class="control-group">
    <div class="control-label">
      <label for="merchant_id" class="tip-top hasTooltip"
             title="<?php echo JHtml::tooltipText('COM_BREEZINGCOMMERCE_CONCORDPAY_MERCHANT_ID_DESCRIPTION'); ?>">
          <?php echo JText::_('COM_BREEZINGCOMMERCE_CONCORDPAY_MERCHANT_ID'); ?>
      </label>
    </div>
    <div class="controls">
      <input type="text" name="merchant_id" id="merchant_id"
             value="<?php echo $this->escape($this->entity->merchant_id); ?>"/>
    </div>
  </div>

  <div class="control-group">
    <div class="control-label">
      <label for="secret_key" class="tip-top hasTooltip"
             title="<?php echo JHtml::tooltipText('COM_BREEZINGCOMMERCE_CONCORDPAY_SECRET_KEY_DESCRIPTION'); ?>">
          <?php echo JText::_('COM_BREEZINGCOMMERCE_CONCORDPAY_SECRET_KEY'); ?>
      </label>
    </div>
    <div class="controls">
      <input type="text" name="secret_key" id="secret_key"
             value="<?php echo $this->escape($this->entity->secret_key); ?>"/>
    </div>
  </div>

  <div class="control-group">
    <div class="control-label">
      <label for="language" class="tip-top hasTooltip"
             title="<?php echo JHtml::tooltipText('COM_BREEZINGCOMMERCE_CONCORDPAY_LANGUAGE_DESCRIPTION'); ?>">
          <?php echo JText::_('COM_BREEZINGCOMMERCE_CONCORDPAY_LANGUAGE'); ?>
      </label>
    </div>
    <div class="controls">
      <select name="language" id="language">
        <option <?php echo $this->entity->language === "uk" ? ' selected' : ''; ?> value="uk">UA</option>
        <option <?php echo $this->entity->language === "ru" ? ' selected' : ''; ?> value="ru">RU</option>
        <option <?php echo $this->entity->language === "en" ? ' selected' : ''; ?> value="en">EN</option>
      </select>
    </div>
  </div>

</div>

<input type="hidden" name="identity" value="<?php echo $this->entity->identity; ?>"/>
