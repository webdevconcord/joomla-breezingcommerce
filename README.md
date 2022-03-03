# Модуль ConcordPay для Joomla BreezingCommerce

Creator: [ConcordPay](https://concordpay.concord.ua)<br>
Tags: ConcordPay, Joomla, BreezingCommerce, payment, payment gateway, credit card, Visa, Masterсard, Apple Pay, Google Pay<br>
Requires at least: Joomla 3.8, BreezingCommerce 1.0<br>
License: GNU GPL v3.0<br>
License URI: [License](https://opensource.org/licenses/GPL-3.0)

Этот модуль позволит вам принимать платежи через платёжную систему **ConcordPay**.

Для работы модуля у вас должны быть установлены **CMS Joomla 3.x** и модуль электронной коммерции **BreezingCommerce 1.x**.

**Обратите внимание!**<br>
Модуль не является стандартным плагином **Joomla**, а представляет собой платёжный плагин к компоненту **BreezingCommerce**, требующий иного способа установки.

## Установка

1. Распаковать файлы модуля.

2. В админ-панели зайти в «Components -> BreezingCommerce -> Plugins» и нажать кнопку *Install*.

3. Нажать кнопку *Choose file*, выбрать файл `bc_plugin_payment_concordpay.zip` из каталога `package`,
затем нажать кнопку *Upload and install*.
 
4. После установки активировать плагин, установив галку *Published*, и перейти в настройки плагина.

5. Установить необходимые настройки плагина.<br>
   
   Указать данные, полученные от платёжной системы:
    - *Идентификатор продавца (Merchant ID)*;
    - *Секретный ключ (Secret Key)*.

    и язык страницы оплаты ConcordPay.

5. Сохранить настройки модуля.

Модуль готов к работе.

*Модуль Joomla BreezingCommerce протестирован для работы с Joomla 3.10.6, BreezingCommerce 1.0.9 и PHP 7.2.*