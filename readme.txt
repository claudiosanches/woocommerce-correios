=== WooCommerce Correios ===
Contributors: claudiosanches, rodrigoprior
Tags: ecommerce, e-commerce, commerce, wordpress ecommerce, shipping, delivery, woocommerce, correios
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds Correios shipping to the WooCommerce plugin

== Description ==

### Add Correios shipping to WooCommerce

This plugin adds Correios shipping to WooCommerce.

Please notice that WooCommerce must be installed and active.

### Correios services:

* PAC
* SEDEX
* SEDEX 10
* SEDEX Hoje
* e-SEDEX (need administration code to works)

== Installation ==

* Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
* Activate the plugin
* Navigate to WooCommerce -> Settings -> Shipping, choose Correios and fill settings

== License ==

This file is part of WooCommerce Correios.
WooCommerce Correios is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
WooCommerce Correios is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Author Bio Box. If not, see <http://www.gnu.org/licenses/>.

== Frequently Asked Questions ==

= What is the plugin license? =
* This plugin is released under a GPL license.

== Changelog ==

== 1.2 - 12/09/2012  =
* Adicionada classe alternativa para fazer a consulta com os correios utilizando SimpleXML.
* Adicionada mensagem de notificação sobre a falta das extensões de SOAP e SimpleXML no servidor.

== 1.1 - 05/09/2012  =
* Adicionada conversão de pesos para kg (padrão dos Correios).
* Adicionada conversão de medidas para cm (padrão dos Correios).
* Cubagem: removido os produtos que não possuem pesos ou medidas.
* Correção do index do array de medidas (causava erro no carrinho quando tinha um segundo produto com quantidade maior do que 1).

= 1.0.1 =
* Adicionado sistema de verificação e notificação sobre a falta de SOAP no servidor.
* Melhorada a inclusão das classes dos Correios no plugin.

= 1.0 =
* Versão incial do plugin.

== Upgrade Notice ==

= 1.2 =
* Added optional class with SimpleXML.

= 1.1 =
* Fixed several errors, upgrade recommended.

= 1.0.1 =
* Fixed soap error.

= 1.0 =
* Enjoy it.

== Screenshots ==

1. screenshot-1.png
2. screenshot-2.png
