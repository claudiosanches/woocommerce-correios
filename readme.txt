=== WooCommerce Correios ===
Contributors: claudiosanches, rodrigoprior
Donate link: http://claudiosmweb.com/doacoes/
Tags: ecommerce, e-commerce, commerce, wordpress ecommerce, shipping, delivery, woocommerce, correios
Requires at least: 3.0
Tested up to: 3.6
Stable tag: 1.5.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds Correios shipping to the WooCommerce plugin

== Description ==

### Add Correios shipping to WooCommerce ###

This plugin adds Correios shipping to WooCommerce.

Please notice that WooCommerce must be installed and active.

### Descrição em Português: ###

Adicione os Correios como método de entrega em sua loja WooCommerce.

[Correios](http://www.correios.com.br/) é um método de entrega brasileiro.

O plugin WooCommerce Correios foi desenvolvido sem nenhum incentivo dos Correios. Nenhum dos desenvolvedores deste plugin possuem vínculos com esta empresa.

Este plugin foi feito baseado na documentação do [Webservices Correios](http://www.correios.com.br/webservices/) e com apoio da [Infranology](http://infranology.com.br/) na construção das classes de Cubagem que possuem este plugin.

= Métodos de entrega aceitos: =

* PAC (com ou sem contrato).
* SEDEX (com ou sem contrato).
* SEDEX 10.
* SEDEX Hoje.
* e-SEDEX (apenas com contrato).

= Instalação: =

Confira o nosso guia de instalação e configuração do Correios na aba [Installation](http://wordpress.org/extend/plugins/woocommerce-correios/installation/).

= Dúvidas? =

Você pode esclarecer suas dúvidas usando:

* A nossa sessão de [FAQ](http://wordpress.org/extend/plugins/woocommerce-correios/faq/).
* Criando um tópico no [fórum de ajuda do WordPress](http://wordpress.org/support/plugin/woocommerce-correios) (apenas em inglês).
* Ou entre em contato com os desenvolvedores do plugin em nossa [página](http://claudiosmweb.com/plugins/correios-para-woocommerce/).

== Installation ==

* Upload plugin files to your plugins folder, or install using WordPress built-in Add New Plugin installer;
* Activate the plugin;
* Navigate to WooCommerce -> Settings -> Shipping, choose Correios and fill settings.

### Instalação e configuração em Português: ###

= Instalação do plugin: =

* Envie os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress.
* Ative o plugin.

= Requerimentos: =

Possuir instalado a extensão SimpleXML (que já é instalado por padrão com o PHP 5).

= Configurações no Correios: =

Com o plugin instalado navegue até "WooCommerce" > "Configurações" > "Entrega" > "Correios".

Nesta tela configure o seu **CEP de origem**, e ative os serviços que você deseja utilizar para a entrega.

É possível escolher o tipo de **Serviço Corporativo** que irá ativar o *e-SEDEX*. Você precisa apenas ser cadastrado nos Correios e informar o seu *Código Administrativo* e *Senha Administrativa*.

Também existe a possibilidade de definir um tamanho mínimo para o pacote das suas encomendas. Por padrão o plugin vem configurado com as medidas mínimas requeridas pelos Correios.

== Frequently Asked Questions ==

= What is the plugin license? =

* This plugin is released under a GPL license.

### FAQ em Português: ###

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

= O que eu preciso para utilizar este plugin? =

* Ter instalado o plugin WooCommerce.
* Possuir instalado em sua hospedagem a extensão de SimpleXML.
* Configurar o seu CEP de origem nas configurações do plugin.
* Adicionar peso e medidas nos produtos que pretende entregar.

= Quais são os métodos de entrega que o plugin aceita? =

São aceitos os métodos:

* PAC (com contrato, código 41068 ou sem contrato, código 41106).
* SEDEX (com contrato, código 40096 ou sem contrato, código 40010).
* SEDEX 10 (código 40215).
* SEDEX Hoje (código 40290).
* e-SEDEX (apenas com contrato, código 81019).

Para mais informações sobre os métodos de entrega dos Correios visite: [Encomendas - Correios](http://www.correios.com.br/voce/enviar/encomendas.cfm).

= Como é feita a cotação do frete? =

A cotação do frete é feita utilizando o [Webservices dos Correios](http://www.correios.com.br/webservices/) utilizando SimpleXML (que é nativo do PHP 5).

Na cotação do frete é usado o seu CEP de origem, CEP de destino do cliente e a cubagem total dos produtos mais o peso. Desta forma o valor cotado sera o mais próximo possível do real.

= É possível calcular frete para quais países? =

No momento o Webservices faz cotação apenas para dentro do Brasil.

= Como resolver o erro *"Nenhum método de envio encontrado. Por favor, recalcule seu frete informando seu estado/país e o CEP para verificar se há algum método de envio disponível para sua região."*? =

Esta é uma mensagem de erro padrão do WooCommerce, ela pode ser gerada por vários problemas.

Segue uma lista dos prováveis erros:

* Os produtos foram cadastros sem peso e dimensões.
* O peso e as dimensões foram cadastrados de forma errada, verifique as configurações de medidas em `WooCommerce > Configurações > Catalogo`.

É possível identificar o erro ligando a opção **Log de depuração** nas configurações dos **Correios**. Desta forma é gerado um log dentro da pasta `wp-content/plugins/woocommerce/logs/`. Ao ativar esta opção, tente realizar uma cotação de frete e depois verique o arquivo gerado.

Caso apareça no log a mensagem `WP_Error: connect() timed out!` pode acontecer do site dos Correios ter caido ou o seu servidor estar com pouca memoria.

= Quais são os limites de dimensões e peso do plugin? =

Veja quais são os limites em: [Correios - limites de dimensões e peso](http://www.correios.com.br/produtosaz/produto.cfm?id=8560360B-5056-9163-895DA62922306ECA).

= Não uso dimensões na minha loja, apenas peso. Como configurar o pacote padrão para todos os produtos? =

No seu `functions.php` adicione:

    function cs_default_correios_package( $measures ) {
        // Gets Correios settings.
        $default = get_option( 'woocommerce_correios_settings' );

        // Sets default package for dimensions.
        $measures['height'] = array( $default['minimum_height'] );
        $measures['length'] = array( $default['minimum_length'] );
        $measures['width']  = array( $default['minimum_width'] );

        return $measures;
    }

    add_filter( 'wccorreios_default_package', 'cs_default_correios_package' );

Desta forma serão usadas as dimensões padrões que estão nas configurações do plugin.

= Mais dúvidas relacionadas ao funcionamento do plugin? =

Entre em contato [clicando aqui](http://claudiosmweb.com/plugins/correios-para-woocommerce/).

== Screenshots ==

1. Settings page.
2. Checkout page.

== Changelog ==

= 1.5.0 - 15/07/2013 =

* Removida a classe de conexão com SOAP.
* Melhoria do código.
* Adicionado método para conexão utilizando `wp_remote_get` e leitura com `SimpleXmlElement`.
* Adicionado opção de taxa de manuseio.
* Melhoria das descrições na página de configuração.
* Melhoria no tratamento de erros.

= 1.4 - 14/04/2013 =

* Adicionada opção para enviar o código de rastreamento dos Correios.
* Adicionado o filtro `woocommerce_correios_shipping_methods` para manipular os métodos de entrega do plugin.

= 1.3.6 - 17/03/2013 =

* Correção do formato de moeda que é recebido dos Correios.

= 1.3.5 - 02/03/2013 =

* Correção do formato de número enviado pelo metodo de SimpleXML.

= 1.3.4 - 17/12/2012 =

* Adicionado o filtro **wccorreios_default_package** para definir um padrão de medidas para a cubagem.

= 1.3.3 - 12/12/2012 =

* Adicionada opção para inserir dias extras na **Estimativa de Entrega**.

= 1.3.2 - 06/12/2012 =

* Melhoria no método connection_method().
* Adicionado método (fix_format()) para corrigir o formato das medidas.
* Correção do método order_shipping().

= 1.3.1 - 30/11/2012 =

* Corrigido o método connection_method().

= 1.3 - 30/11/2012 =

* Adicionada opção para logs de erro.
* Adiciona opção para selecionar o tipo de conexão (SOAP ou SimpleXML) caso esteja disponível mais de uma opção no servidor.

= 1.2.1 - 12/09/2012  =

* Adicionada prevenção de erros quando o carrinho possui apenas produtos que não requerem entrega.

= 1.2 - 11/09/2012  =

* Adicionada classe alternativa para fazer a consulta com os correios utilizando SimpleXML.
* Adicionada mensagem de notificação sobre a falta das extensões de SOAP e SimpleXML no servidor.

= 1.1 - 05/09/2012  =

* Adicionada conversão de pesos para kg (padrão dos Correios).
* Adicionada conversão de medidas para cm (padrão dos Correios).
* Cubagem: removido os produtos que não possuem pesos ou medidas.
* Correção do index do array de medidas (causava erro no carrinho quando tinha um segundo produto com quantidade maior do que 1).

= 1.0.1 =

* Adicionado sistema de verificação e notificação sobre a falta de SOAP no servidor.
* Melhorada a inclusão das classes dos Correios no plugin.

= 1.0 =

* Versão inicial do plugin.

== Upgrade Notice ==

= 1.4 =

* Adicionada opção para enviar o código de rastreamento dos Correios.

== License ==

WooCommerce Correios is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

WooCommerce Correios is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with WooCommerce Correios. If not, see <http://www.gnu.org/licenses/>.
