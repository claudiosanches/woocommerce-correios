=== WooCommerce Correios ===
Contributors: claudiosanches, rodrigoprior, matheuscl
Donate link: https://claudiosanches.com/doacoes/
Tags: shipping, delivery, woocommerce, correios
Requires at least: 4.0
Tested up to: 4.9
Stable tag: 3.6.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integration between the Correios and WooCommerce

== Description ==

Utilize os métodos de entrega e serviços dos Correios com a sua loja WooCommerce.

[Correios](http://www.correios.com.br/) é um método de entrega brasileiro.

O plugin WooCommerce Correios foi desenvolvido sem nenhum incentivo dos Correios. Nenhum dos desenvolvedores deste plugin possuem vínculos com esta empresa. E note que este plugin foi feito baseado na documentação do [Webservices Correios](http://www.correios.com.br/webservices/) e com apoio da [Infranology](http://infranology.com.br/) na construção das classes de cubagem.

= Serviços integrados =

Estão integrados os seguintes serviços:

- Entrega nacional:
 - PAC
 - SEDEX
 - SEDEX 10 Envelope
 - SEDEX 10 Pacote
 - SEDEX 12
 - SEDEX Hoje
 - e-SEDEX (necessário conta administrativa nos Correios)
 - Carta Registrada
 - Impresso Normal
 - Impresso Urgente
- Entrega internacional:
 - Mercadoria Expressa
 - Mercadoria Econômica
 - Leve Internacional
- Consulta do histórico de rastreamento da encomenda (sendo exibida na página do pedido em "Minha conta" para o cliente)
- Consulta e autopreenchimento de endereços baseados em CEPs

= Instalação: =

Confira o nosso guia de instalação e configuração do Correios na aba [Installation](http://wordpress.org/extend/plugins/woocommerce-correios/installation/).

= Compatibilidade =

Requer WooCommerce 3.0 ou posterior para funcionar.

= Dúvidas? =

Você pode esclarecer suas dúvidas usando:

- A nossa sessão de [FAQ](http://wordpress.org/extend/plugins/woocommerce-correios/faq/).
- Utilizando o nosso [fórum no Github](https://github.com/claudiosanches/woocommerce-correios).
- Criando um tópico no [fórum de ajuda do WordPress](http://wordpress.org/support/plugin/woocommerce-correios).

== Installation ==

= Instalação do plugin: =

- Envie os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress.
- Ative o plugin.

= Requerimentos: =

- [SimpleXML](http://php.net/manual/pt_BR/book.simplexml.php) ativado no PHP (note que já é ativado por padrão no PHP 5).
- Modulo [SOAP](http://php.net/manual/pt_BR/book.soap.php) (utilizado para a tabela de histórico de rastreamento e autopreenchimento de endereços).

= Configurações do plugin: =

[youtube https://www.youtube.com/watch?v=IWjLAy9i--I]

= Configurações dos produtos =

É necessário configurar o **peso** e **dimensões** de todos os seus produtos, caso você queria que a cotação de frete seja exata.
Note que é possível configurar com produtos do tipo **simples** ou **variável** e não *virtuais* (produtos virtuais são ignorados na hora de cotar o frete).  

Alternativamente, você pode configurar apenas o peso e deixar as dimensões em branco, pois serão utilizadas as configurações do **Pacote Padrão** para as dimensões (neste caso pode ocorrer uma variação pequena no valor do frete, pois os Correios consideram mais o peso do que as dimensões para a cotação).

== Frequently Asked Questions ==

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

= O que eu preciso para utilizar este plugin? =

* WooCommerce 3.0 ou posterior.
* [SimpleXML](http://php.net/manual/pt_BR/book.simplexml.php) ativado no PHP (note que já é ativado por padrão no PHP 5).
* Modulo [SOAP](http://php.net/manual/pt_BR/book.soap.php) (utilizado para a tabela de histórico de rastreamento e autopreenchimento de endereços).
* Adicionar peso e dimensões nos produtos que pretende entregar.

= Quais são os métodos de entrega que o plugin aceita? =

São aceitos os seguintes métodos de entrega nacionais:

- PAC
- SEDEX
- SEDEX 10 Envelope
- SEDEX 10 Pacote
- SEDEX 12
- SEDEX Hoje
- e-SEDEX (necessário conta administrativa nos Correios)
- Carta Registrada
- Impresso Normal
- Impresso Urgente

E os seguintes métodos de entrega internacionais:

- Mercadoria Expressa
- Mercadoria Econômica
- Leve Internacional

= Onde configuro os métodos de entrega? =

Os métodos de entrega devem ser configurados em "WooCommerce" > "Configurações" > "Entrega" > "Áreas de entrega".

Para entrega nacional, é necessário criar uma área de entrega para o Brasil ou para determinados estados brasileiros e atribuir os métodos de entrega.

= Onde configuro o autopreenchimento de endereço ou a tabela de histórico de rastreamento =

É possível configurar os dois em "WooCommerce" > "Configurações" > "Integrações" > "Correios".

= Como alterar a mensagem que é enviada no e-mail do código de rastreamento? =

É possível encontrar configurações para o e-mail do código de rastreamento em "WooCommerce" > "Configurações" > "E-mails" > "Código de rastreio dos Correios".

= Como é feita a cotação do frete? =

A cotação do frete é feita utilizando o [Calculador Remoto de Preços e Prazos dos Correios](https://www.correios.com.br/para-sua-empresa/servicos-para-o-seu-contrato/precos-e-prazos).

Na cotação do frete é usado o seu CEP de origem, CEP de destino do cliente, junto com as dimensões dos produtos e peso. Desta forma o valor cotado sera o mais próximo possível do real.

Note que já fazem quase 4 anos que este plugin existe utilizando o mesmo método para obter a cubagem do pedido e tem funcionando muito bem, caso você tenha algum problema, provavelmente é por causa de configurar valores errados nos produtos.

= Tem calculadora de frete na página do produto? =

Não tem, simplesmente porque não faz parte do escopo deste plugin.

Escopo deste plugin é prover integração entre o WooCommerce e os Correios.

= Este plugin faz alterações na calculadora de frete na página do carrinho ou na de finalização? =

Não, nenhuma alteração é feita, este plugin funcionando esperando o WooCommerce verificar pelos valores de entrega, então é feita uma conexão com os Correios e os valores retornados são passados de volta para o WooCommerce apresentar.

Note que não damos suporte para qualquer tipo de personalização na calculadora, simplesmente porque não faz parte do escopo do plugin, caso você queria mudar algo como aparece, deve procurar ajuda com o WooCommerce e não com este plugin.

= Como resolver o erro "Não existe nenhum método de entrega disponível. Por favor, certifique-se de que o seu endereço esta correto ou entre em contato conosco caso você precise de ajuda."? =

Primeiro de tudo, isso não é um erro, isso é uma mensagem padrão do WooCommerce que é exibida quando não é encontrado nenhuma método de entrega.

Mesmo você configurando os métodos de entrega, eles não são exibidos quando os Correios retornam mensagens de erro, por exemplo quando a região onde o cliente esta não é coberta pelos Correios ou quando o peso do pacote passa do limite suportado.

Entretanto boa parte das vezes esse tipo de coisa acontece porque os métodos e/ou produtos não foram configurados corretamente.

Aqui uma lista de erros mais comuns:

- Faltando CEP de origem nos métodos configurados.
- CEP de origem inválido.
- Produtos cadastrados sem peso e dimensões
- Peso e dimensões cadastrados de forma incorreta (por exemplo configurando como 1000kg, pensando que seria 1000g, então verifique as configurações de medidas em `WooCommerce > Configurações > Produtos`).

E não se esqueça de verificar o erro ativando a opção de **Log de depuração** nas configurações de cada método de entrega. Imediatamente após ativar o log, basta tentar cotar o frete novamente, fazendo assim o log ser gerado. Você pode acessar todos os logs indo em "WooCommerce" > "Status do sistema" > "Logs".

Dica: Caso apareça no log a mensagem `WP_Error: connect() timed out!` pode acontecer do site dos Correios ter caído ou o seu servidor estar com pouca memoria.

= Os métodos de entrega dos Correios não aparecem no carrinho ou durante a finalização? =

As mesmas dicas da sessão acima valem como solução para isto também.

= O valor do frete calculado não bateu com a da loja dos Correios? =

Este plugin utiliza o Webservices dos Correios para calcular o frete e quando este tipo de problema acontece geralmente é porque:

1. Foram configuradas de forma errada as opções de peso e dimensões dos produtos na loja.
2. Configurado errado o CEP de origem nos métodos de entrega.
3. O Webservices dos Correios enviou um valor errado! Sim isso acontece e na página da documentação do Webservice tem o seguinte alerta:

> Os valores obtidos pelos simuladores aqui disponíveis são uma estimativa e deverão ser confirmados no ato da postagem.

= Ainda esta tendo problemas? =

Se estiver tendo problemas, antes de tudo ative a opção de **Log de depuração** do método que você esta tendo problema e tente novamente cotar o frete, fazendo isso, um arquivo de log é criado e são registradas as respostas do Webservice dos Correios, leia o arquivo de log, nele é descrito exatamente o que esta acontecendo, tanto o que foi concluindo com sucesso ou não.

Se ainda não foi capaz de solucionar o problema, copie o conteúdo do arquivo de log, cole no [pastebin.com](http://pastebin.com), salve e pegue o link gerado, depois disso abra um tópico informando o seu problema no [fórum de suporte do plugin](https://wordpress.org/support/plugin/woocommerce-correios/#new-post).

= Dúvidas sobre o funcionamento do plugin? =

Em caso de dúvidas, basta abrir um tópico no [fórum de suporte do plugin](https://wordpress.org/support/plugin/woocommerce-correios/#new-post), vou responder conforme eu tenho tempo livre e caso sua dúvida for relacionada com o funcionamento deste plguin.

== Screenshots ==

1. Exemplo de áreas de entrega com os Correios.
2. Exemplo da tela de configurações dos métodos de entrega.
3. Configurações de integração com os Correios.
4. Campo para adicionar o código de rastreamento (tela de administração de pedidos).
5. Configurações do e-mails do código de rastreamento.
6. Exemplo dos métodos de entrega sendo exibidos na página de finalização.
7. Exemplo do código de rastreamento sendo exibido dentro da página de detalhes de pedido na página "Minha conta".
8. Exemplo da tabela do histórico de rastreamento que é exibida no lugar do alerta acima quando ativada a opção "Tabela do histórico de rastreamento" nas configurações de integração.

== Changelog ==

= 3.6.0 - 2017/11/19 =

- Atualizado o valor mínimo declarado para 18 reais.
- Melhorias gerais no código base do plugin.

= 3.5.1 - 2017/10/26 =

- Usa por padrão o CEP de base do WooCommerce 3.2 ao adicionar um novo método de entrega.
- Correção do valor declarado para PAC, que ainda não aceitava o pedido quando o valor era igual a 17 reais.
- Correção do peso máximo para Impresso Normal.

= 3.5.0 - 2017/10/06 =

- Adicionada opção para adicionar peso extra por pacote.
- Adicionado suporte para entrega em áreas de risco.
- Corrigido problema de valor declaro do PAC.

= 3.4.1 - 2017/05/17 =

- Restaurado o suporte de valores em porcentagem para "Taxa de manuseio".

= 3.4.0 - 2017/05/13 =

- Adicionado novamente links para os códigos de rastreamento na detalhes do pedido no admin.
- Adicionado novos links com código de rastreamneto na lista de pedidos no admin.
- Corrigido códigos de rastreamento que ainda não foram atualizados na página de detalhes do pedido em "Minha conta".

= 3.3.0 - 2017/05/05 =

- Atualizados os códigos do PAC e Sedex.
- Atualizada a forma que são exibidos os códigos de rastreamento, uma vez que os Correios desativou o link que era utilizado.

= 3.2.2 - 2017/03/14 =

- Corrigido o salvamento dos códigos de rastreamento em versões anteriores ao WooCommerce 3.0.
- Corrigida compatibilidade com o envio de e-mails dos códigos de rastreamento no WooCommerce 3.0.

= 3.2.1 - 2017/03/08 =

- Corrigida compatiblidade com PHP 5.2 e 5.3 na nova ferramenta de cadastro de código de rastreamento.

= 3.2.0 - 2017/03/07 =

- Adicionado suporte para o WooCommerce 3.0.
- Atualizados os valores do Impresso Normal e Impresso Urgente (Obrigado Alex Koti).
- Adicionado suporte para vários códigos de rastreamento para cada pedido.
- Adicionado suporte a classes de entrega para todos os métodos de entrega.
- Corrigida compatibilidade com PHP 7.1.

= 3.1.7 - 2017/01/10 =

- Corrigido o link de rastreio nas notas do pedido.

= 3.1.6 - 2017/01/05 =

- Declara valor para os métodos de sedex apenas se o total do pacote for mais do que 17 reais.
- Adicionada opção manual para estimativa de entrega para os métodos de Impresso e Carta Registrada.
- Corrigida mensagens de alerta do plugin para serem exibidas apenas na página do carrinho.

= 3.1.5 - 2016/12/31 =

- Adicionado link para a página de rastreio do pedido nas notas do pedido.
- Corrigido valor declarado utilizando o valor do pacote atual e não do carrinho.
- Adicionado objeto do pacote no filtro `woocommerce_correios_{$method_id}_rate`.

= 3.1.4 - 2016/12/23 =

- Adicionada prevenção de erros no PHP quando todos os valores de peso e dimensões estão em branco.
- Introduzido o filtro `woocommerce_correios_get_estimating_delivery` para edição da estimativa de entrega.
- Adicionada informações do pacote nos filtros para tornar possível calcular frete para múltiplos pacotes.
- Adicionada prevenção de erro quando Correios retorna apenas um objeto no rastreio de entrega.

= 3.1.3 - 2016/09/27 =

- Corrigido erro quando utilizado código de rastreio incorreto.

= 3.1.2 - 2016/09/20 =

- Removida mensagem de erro quando serviço é indisponível no trecho informado, esta mensagem estava causando muita confusão para usuários quando Sedex 10 ou Sedex Hoje estão indisponíveis.

= 3.1.1 - 2016/09/15 =

- Corrigida a utilização das classes de entrega para os métodos de Carta Registrada, Impresso Normal e Impresso Urgente.

= 3.1.0 - 2016/09/13 =

- Adicionado os métodos Impresso Normal e Impresso Urgente (obrigado a [@jckronbauer](https://github.com/jckronbauer)).
- Adicionada opção para enviar novamente e-mail do código de rastreio.
- Adicionada mensagem de erro quando serviço é indisponível no trecho informado.

= 3.0.6 - 2016/08/20 =

- Introduzida a função `wc_correios_get_address_by_postcode()`.
- Removida opção de seguro para métodos internacionais, pois o valor adicionado era o valor do premio do seguro automático.
- Corrigido e-Sedex para aceitar apenas a opção corporativa.

= 3.0.5 - 2016/07/20 =

- Corrigido o formato das dimensões e peso para os métodos de entrega internacionais.

= 3.0.4 - 2016/07/20 =

- Corrigido o tratamento do XML das métodos internacionais, nos quais os Correios as vezes envia um XML no formato incorreto.

= 3.0.3 - 2016/06/28 =

- Corrigida as unidades de medidas dos métodos internacionais.

= 3.0.2 - 2016/06/28 =

- Adicionado novo método de Carta Registrada seguindo a tabela dos Correios e com suporte a classes de entrega.

= 3.0.1 - 2016/06/26 =

- Corrigido o calculo da taxa de manuseio.

= 3.0.0 - 2016/06/26 =

- Reformulação geral de todos o código do plugin.
- Adicionado suporte as áreas de entrega do WooCommerce 2.6.
- Adicionado os métodos de SEDEX 10 Envelope, SEDEX 12, Carta Registrada, Mercadoria Expressa, Mercadoria Econômica e Leve Internacional.
- Adicionado novo menu de "integrações".
- Adicionada integração com o serviço de consulta de CEP e assim adicionando uma opção para autopreencher endereços com base no CEP.
- Atualizada a integração com o sistema que gera o histórico de rastreamento do pedido.
- Removida a opção de simulador de frete na página do produto.
- Integrado o campo de "correios_tracking_code" dos pedidos com a API REST do WooCommerce.
- E mais outras várias alterações que podem ser verificadas pelo [GitHub](https://github.com/claudiosanches/woocommerce-correios/compare/2.3.0...3.0.0).

== Upgrade Notice ==

= 3.6.0 =

- Atualizado o valor mínimo declarado para 18 reais.
- Melhorias gerais no código base do plugin.
