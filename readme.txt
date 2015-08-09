=== WooCommerce Correios ===
Contributors: claudiosanches, rodrigoprior
Donate link: http://claudiosmweb.com/doacoes/
Tags: shipping, delivery, woocommerce, correios
Requires at least: 4.0
Tested up to: 4.3
Stable tag: 2.3.0
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

Este plugin foi feito baseado na documentação do [Webservices Correios](http://www.correios.com.br/webservices/) e com apoio da [Infranology](http://infranology.com.br/) na construção das classes de cubagem.

= Métodos de entrega aceitos: =

* PAC (com ou sem contrato).
* SEDEX (com ou sem contrato).
* SEDEX 10.
* SEDEX Hoje.
* e-SEDEX (apenas com contrato).

= Instalação: =

Confira o nosso guia de instalação e configuração do Correios na aba [Installation](http://wordpress.org/extend/plugins/woocommerce-correios/installation/).

= Compatibilidade =

Compatível com as versões 2.1.x, 2.2.x, 2.3.x e 2.4.x do WooCommerce.

= Dúvidas? =

Você pode esclarecer suas dúvidas usando:

* A nossa sessão de [FAQ](http://wordpress.org/extend/plugins/woocommerce-correios/faq/).
* Utilizando o nosso [fórum no Github](https://github.com/claudiosmweb/woocommerce-correios).
* Criando um tópico no [fórum de ajuda do WordPress](http://wordpress.org/support/plugin/woocommerce-correios).

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

= Configurações do plugin: =

Com o plugin instalado navegue até "WooCommerce" > "Configurações" > "Entrega" > "Correios".

Nesta tela configure o seu **CEP de origem**, e ative os serviços que você deseja utilizar para a entrega.

É possível escolher o tipo de **Serviço Corporativo** que irá ativar o *e-SEDEX*. Você precisa apenas ser cadastrado nos Correios e informar o seu *Código Administrativo* e *Senha Administrativa*.

Também é possível configurar um **Pacote Padrão** que será utilizando para definir as medidas mínimas do pacote de entraga.

= Configurações dos produtos =

Para que seja possível cotar o frete, os seus produtos precisam ser do tipo **simples** ou **variável** e não estarem marcados com *virtual* ou *baixável* (qualquer outro tipo de produto será ignorado na cotação).

É necessário configurar o **peso** e **dimensões** de todos os seus produtos, caso você queria que a cotação de frete seja exata.  
Alternativamente, você pode configurar apenas o peso e deixar as dimensões em branco, pois neste caso serão utilizadas as configurações do **Pacote Padrão** para as dimensões (neste caso pode ocorrer uma variação pequena no valor do frete, pois os Correios consideram mais o peso do que as dimensões para a cotação).

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
* Adicionar peso e dimensões nos produtos que pretende entregar.

**Atenção**: É obrigatório ter o **peso** configurado em cada produto para que seja possível cotar o frete de forma eficiente. As dimensões podem ficar em branco e neste caso, serão utilizadas as medidas da opção **Pacote Padrão** da configuração do plugin, mas é **recomendado** que cada produto tenha suas configurações próprias de **peso** e **dimensões**.

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

Desta forma é necessário adicionar pelo menos o peso em cada produto, pois na falta de dimensões serão utilizadas as configurações do pacote padrão.

= É possível calcular frete para quais países? =

No momento o Webservices faz cotação apenas para dentro do Brasil.

= Como resolver o erro "Nenhum método de envio encontrado. Por favor, recalcule seu frete informando seu estado/país e o CEP para verificar se há algum método de envio disponível para sua região." ou o erro "Desculpe, aparentemente não existem métodos de entrega disponíveis para sua localidade (Brasil). Se você precisa de ajuda ou deseja fazer uma negociação para realizar a entrega, entre em contato conosco."? =

Esta é uma mensagem de erro padrão do WooCommerce, ela pode ser gerada por vários problemas.

Segue uma lista dos prováveis erros:

* Os produtos foram cadastros sem peso e dimensões.
* O peso e as dimensões foram cadastrados de forma errada, verifique as configurações de medidas em `WooCommerce > Configurações > Catalogo`.

É possível identificar o erro ligando a opção **Log de depuração** nas configurações dos **Correios**. Desta forma é gerado um log dentro da pasta `wp-content/plugins/woocommerce/logs/`. Ao ativar esta opção, tente realizar uma cotação de frete e depois verique o arquivo gerado.

Caso apareça no log a mensagem `WP_Error: connect() timed out!` pode acontecer do site dos Correios ter caido ou o seu servidor estar com pouca memoria.

= Quais são os limites de dimensões e peso do plugin? =

Veja quais são os limites em: [Correios - limites de dimensões e peso](http://www.correios.com.br/produtosaz/produto.cfm?id=8560360B-5056-9163-895DA62922306ECA).

= Os métodos de entrega dos Correios não aparecem durante o checkout ou no carrinho? =

Verifique se você realmente ativou as opções de entrega do plugin e faça o mesmo procedimento da questão a cima.

Além de conferir se o carrinho possue produtos do tipo **simples** e **variável** e não estarem marcados com *virtual* ou *baixável*.

= O valor do frete calculado não bateu com a da loja dos Correios? =

Este plugin utiliza o Webservices dos Correios para calcular o frete e quando este tipo de problema acontece geralmente é porque:

1. Foram configuradas de forma errada as opções de peso e dimensões dos produtos na loja.
2. O Webservices dos Correios enviou um valor errado! Sim isso acontece e na página da documentação do Webservices tem o seguinte alerta:

>Os valores obtidos pelos simuladores aqui disponíveis são uma estimativa e deverão ser confirmados no ato da postagem.

= Mais dúvidas relacionadas ao funcionamento do plugin? =

Ative a opção de **Log de depuração** do plugin e entre em contato [clicando aqui](http://claudiosmweb.com/plugins/correios-para-woocommerce/).
**Atenção!** Não adianta pedir ajuda se não tiver o log em mãos.

== Screenshots ==

1. Configurações do plugin.
2. Seleção de frete na finalização do pedido.
3. Simulador de frete na página do produto.
4. Campo para adicionar o código de rastreamento (aparece na tela de gerenciamento do pedido).
5. Opções para personalizar os e-mails do código de rastreamento.
6. Alerta exibindo o código de rastreamento dentro da página de detalhes do pedido na página "Minha conta".
7. Tabela do histórico de rastreamento que é exibida no lugar do alerta acima quando ativada a opção "Tabela do histórico de rastreamento" nas configurações do plugin.

== Changelog ==

= 2.3.0 - 2015/08/08 =

* Adicionado suporte para WooCommerce 2.4.x.
* Removido suporte para WooCommerce 2.0.x.
* Corrigida a conversão dos valores de "Taxa de manuseio".
* Corrigido a conversão de valores do simulador de frete na página de produto.

= 2.2.3 - 2015/07/23 =

* Melhorada a função que converte o formato de moeda que vêm dos Correios para o WooCommerce.

= 2.2.2 - 2015/07/11 =

* Corrigido código de conversão do formato de moeda dos Correios para o WooCommerce. 

= 2.2.1 - 2015/02/18 =

* Removido os decimais do valor declarado, o valor agora é arredondado (Os Correios passou a ter problemas com valores declarados com decimais e a calcular o valor do frete de forma incorreta).
* Adicionado filtro `woocommerce_correios_tracking_user_data`.

= 2.2.0 - 2015/01/18 =

* Adicionada API para consulta do objeto no site dos Correios (Agradecimentos para [Edison Costa](https://github.com/claudiosmweb/woocommerce-correios/pull/24)).
* Adicionada opção para mostrar histórico do rastreamento do pedido no site dos Correios.
* Adicionado templates para a exibição do histórico de rastreamento.

= 2.1.3 - 2014/10/11 =

* Removida as opções de "Disponibilidade" e "Países Permitidos", pois funciona apenas no Brasil.
* Melhorado o relatório de erro quando os Correios retorna status 404.

= 2.1.2 - 2014/10/08 =

* Melhorado o salvamento dos códigos de rastremento utilizando o action `woocommerce_process_shop_order_meta`.
* Corrigido o suporte com o WooCommerce 2.0 nos templates de e-mail.

= 2.1.1 - 2014/08/14 =

* Corrigida a opção que manipula o conteúdo do e-mail sobre o código de rastreamento.

= 2.1.0 - 2014/08/13 =

* Adicionado suporte para a API de e-mails do WooCommerce para enviar as notificações sobre o código de rastreamento.
* Adicionado suporte para a API de templates do WooCommerce para personalizar os templates de e-mails e do simulador de frete.
* Adicionado o filtro `woocommerce_correios_webservice_url` para tornar possível personalizar o link do WebService.
* Adicionada versões comprimidas dos arquivos de CSS e JavaScript do plugin.
* Melhorado o script que exibe e oculta os campos das opções do plugin.
* Corrigida a exibição do link do código de rastreamento dentro de cada pedido em "Minha Conta".
* Corrigido o simulador de produtos que tinha problemas com páginas com cache.

= 2.0.8 - 2014/08/02 =

* Adicionado suporte para templates antigos no simulador.

= 2.0.7 - 2014/07/05 =

* Pequena correção nos dados antes de passar para fazer o calculo de cubagem.

= 2.0.6 - 2014/05/25 =

* Correção do método que pega os produtos variáveis no simulador de frete.

= 2.0.5 - 2014/05/24 =

* Correção da exibição do simulador para produtos virtuais.

= 2.0.4 - 2014/05/24 =

* Melhorada a compatibilidade com o WooCommerce 2.0.x.

= 2.0.3 - 2014/05/18 =

* Melhorada a perfomance na hora de calcular o frete enviando todos os códigos para serem calculados com apenas uma consulta.
* Melhorado o simulador, agora impede o cliente de clicar várias vezes ao mesmo tempo enquanto esta simulando o frete.

= 2.0.2 - 2014/05/06 =

* Melhoradas as classes do simulador.
* Melhorada a exibição do simulador, agora não é exibido quando o produto esta fora de estoque.

= 2.0.1 - 2014/05/06 =

* Correção da soma da opção "Dias adicionais".

= 2.0.0 - 2014/05/06 =

* Adicionado simulador de frete na página do produto.
* Adicionado tratamento de erro que exibe alguns erros úteis para o cliente na página do carrinho.
* Melhorias no código do plugin.

= 1.6.2 - 2013/12/20 =

* Correção no valor declarado que era passado com valor errado para os Correios.

= 1.6.1 - 2013/12/06 =

* Atualizado o método que extrai as médidas dos pedidos (agora ele é menos restrito).

= 1.6.0 - 2013/12/03 =

* Adicionado suporte para as versões 2.1.x do WooCommerce.

= 1.5.0 - 2013/07/15 =

* Removida a classe de conexão com SOAP.
* Melhoria do código.
* Adicionado método para conexão utilizando `wp_remote_get` e leitura com `SimpleXMLElement`.
* Adicionado opção de taxa de manuseio.
* Melhoria das descrições na página de configuração.
* Melhoria no tratamento de erros.

= 1.4.0 - 2013/04/14 =

* Adicionada opção para enviar o código de rastreamento dos Correios.
* Adicionado o filtro `woocommerce_correios_shipping_methods` para manipular os métodos de entrega do plugin.

= 1.3.6 - 2013/03/17 =

* Correção do formato de moeda que é recebido dos Correios.

= 1.3.5 - 2013/03/02 =

* Correção do formato de número enviado pelo metodo de SimpleXML.

= 1.3.4 - 2012/12/17 =

* Adicionado o filtro **wccorreios_default_package** para definir um padrão de medidas para a cubagem.

= 1.3.3 - 2012/12/12 =

* Adicionada opção para inserir dias extras na **Estimativa de Entrega**.

= 1.3.2 - 2012/12/06 =

* Melhoria no método connection_method().
* Adicionado método (fix_format()) para corrigir o formato das medidas.
* Correção do método order_shipping().

= 1.3.1 - 2012/11/30 =

* Corrigido o método connection_method().

= 1.3 - 2012/11/30 =

* Adicionada opção para logs de erro.
* Adiciona opção para selecionar o tipo de conexão (SOAP ou SimpleXML) caso esteja disponível mais de uma opção no servidor.

= 1.2.1 - 2012/09/12 =

* Adicionada prevenção de erros quando o carrinho possui apenas produtos que não requerem entrega.

= 1.2.0 - 2012/09/11 =

* Adicionada classe alternativa para fazer a consulta com os correios utilizando SimpleXML.
* Adicionada mensagem de notificação sobre a falta das extensões de SOAP e SimpleXML no servidor.

= 1.1.0 - 2012/09/05 =

* Adicionada conversão de pesos para kg (padrão dos Correios).
* Adicionada conversão de medidas para cm (padrão dos Correios).
* Cubagem: removido os produtos que não possuem pesos ou medidas.
* Correção do index do array de medidas (causava erro no carrinho quando tinha um segundo produto com quantidade maior do que 1).

= 1.0.1 =

* Adicionado sistema de verificação e notificação sobre a falta de SOAP no servidor.
* Melhorada a inclusão das classes dos Correios no plugin.

= 1.0.0 =

* Versão inicial do plugin.

== Upgrade Notice ==

= 2.3.0 =

* Adicionado suporte para WooCommerce 2.4.x.
* Removido suporte para WooCommerce 2.0.x.
* Corrigida a conversão dos valores de "Taxa de manuseio".
* Corrigido a conversão de valores do simulador de frete na página de produto.

== License ==

WooCommerce Correios is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

WooCommerce Correios is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with WooCommerce Correios. If not, see <http://www.gnu.org/licenses/>.
