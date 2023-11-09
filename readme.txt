=== Claudio Sanches - Correios for WooCommerce ===
Contributors: claudiosanches, rodrigoprior, matheuscl
Donate link: https://apoia.se/claudiosanches?utm_source=plugin-correios
Tags: shipping, delivery, woocommerce, correios
Requires at least: 4.0
Tested up to: 6.3
Stable tag: 4.2.3
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integration between the Correios and WooCommerce

== Description ==

Utilize os métodos de entrega e serviços dos Correios com a sua loja WooCommerce.

[Correios](http://www.correios.com.br/) é um método de entrega brasileiro.

Este plugin foi desenvolvido sem nenhum incentivo dos Correios. Nenhum dos desenvolvedores deste plugin possuem vínculos com esta empresa. E note que este plugin foi feito baseado na documentação do [Webservices Correios](https://www.correios.com.br/atendimento/developers) e com apoio da [Infranology](http://infranology.com.br/) na construção das classes de cubagem.

= Integração =

Este plugin suporta a nova API dos Correios, fazendo integração completa com o seu contrato, sendo possível integrar com qualquer método de entrega disponível para o seu contrato, como PAC, SEDEX e mais.
Também tem integração para consulta do histórico de rastreamento da encomenda (sendo exibida na página do pedido em "Minha conta" para o cliente), além de incluir integração para preenchimento automático de endereços a partir de um CEP.

= Instalação: =

Confira o nosso guia de instalação e configuração do Correios na aba [Installation](http://wordpress.org/extend/plugins/woocommerce-correios/installation/).

= Compatibilidade =

- WooCommerce 3.0 ou posterior para funcionar.
- Integrado com a [API nova dos Correios](https://www.correios.com.br/atendimento/developers).
- Compatível com o [Link Correios](https://www.linkcorreios.com.br/).

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

- Ter contrato utilizando um CNPJ com os Correios.
- O contrato com os Correios precisa ser pelo menos no nível Bronze 1 para funcionar com a API.

= Configuração do plugin =

[youtube https://www.youtube.com/watch?v=YOoiqv-VJWw]

1. Depois de instalado e ativado, vá até "WooCommerce" > "Configurações" > "Integrações" > "Correios".
2. Preencha os campos "Nome de usuário", "Código de acesso" e "Cartão de postagem" na sessão "Correios Web Services".
3. Salve as configurações.
4. Ainda nesta página você pode clicar em "Atualizar lista de serviços" para baixar a lista de serviços dos Correios disponíveis no seu contrato.
5. Finalmente vá até "WooCommerce" > "Configurações" > "Entrega" e crie/edite uma area de entrega para entregas no Brasil.
6. Adicione o método de entrega "Correios (Nova API)".
7. Clique para editar o método e selecione o "Serviço" que será usado. Por exemplo "PAC CONTRATO AG" ou "SEDEX CONTRATO AG" (os serviços são baixados na etapa 4 deste guia).
8. Finalmente termine de configurar as outras opções conforme a sua necessidade e salve o método de entrega.
9. Pronto, tudo configurado.

Para garantir que tudo esta funcionando tenha certeza de configurar o peso e medidas dos produtos em cada um deles, veja mais detalhes na sessão seguinte.

= Configurações dos produtos =

É necessário configurar o **peso** e **dimensões** de todos os seus produtos, caso você queria que a cotação de frete seja exata.
Note que é possível configurar com produtos do tipo **simples** ou **variável** e não *virtuais* (produtos virtuais são ignorados na hora de cotar o frete).  

Alternativamente, você pode configurar apenas o peso e deixar as dimensões em branco, pois serão utilizadas as configurações do **Pacote Padrão** para as dimensões (neste caso pode ocorrer uma variação pequena no valor do frete, pois os Correios consideram mais o peso do que as dimensões para a cotação).

== Frequently Asked Questions ==

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

= O que eu preciso para utilizar este plugin? =

* WooCommerce 3.0 ou posterior.
* Contrato com os Correios.
* Adicionar peso e dimensões nos produtos que pretende entregar.

= Quais são os métodos de entrega que o plugin aceita? =

Todos os métodos disponíveis no seu contrato com os Correios.

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

Note que já fazem mais de 10 anos que este plugin existe utilizando o mesmo método para obter a cubagem do pedido e tem funcionando muito bem, caso você tenha algum problema, provavelmente é por causa de configurar valores errados nos produtos.

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

Dica: Caso apareça no log a mensagem `WP_Error: connect() timed out!` pode acontecer do site dos Correios ter caído ou o seu servidor estar com pouca memória.

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

Em caso de dúvidas, basta abrir um tópico no [fórum de suporte do plugin](https://wordpress.org/support/plugin/woocommerce-correios/#new-post), vou responder conforme eu tenho tempo livre e caso sua dúvida for relacionada com o funcionamento deste plugin.

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

= 4.2.3 - 2023/11/08 =

- Corrigido um erro relacionado a lista de serviços desatualizada que impedia editar o método de entrega.
- Atualizada a mensagem do e-mail para deixar claro que estimativa de entrega começa apenas depois do envio do produto.

= 4.2.2 - 2023/10/30 =

- Corrigida conversão de peso para gramas.

= 4.2.1 - 2023/10/29 =

- Adicionado suporte para WooCommerce 8.2+.

= 4.2.0 - 2023/10/29 =

- Implementação de método de entrega internacional.
- Melhorada compatibilidade com PHP 8.2.

= 4.1.8 - 2023/09/29 =

- Corrigido o comportamento da integração de preenchimento de endereço quando os campos da nova API estão preenchidos pela metade.

= 4.1.7 - 2023/09/28 =

- Corrigido o assunto do e-mail do código de rastreamento que não era exibido as vezes.
- Melhorado os logs para incluir apenas a resposta e o cabeçalho da resposta de requisições.
- Melhorada a descrição dos campos da integração, adicionado link para ajudar os usuários encontrar o número do Cartão de Postagem.

= 4.1.6 - 2023/09/26 =

- Corrigido a exibição da estimativa de entrega para métodos offline.

= 4.1.5 - 2023/09/25 =

- Adicionado suporte para serviços do tipo "LOG +".
- Corrigido link de log da integração e dos métodos de entrega.
- Corrigido erros quando token não pode ser gerado com sucesso.
- Adicionado suporte para WooCommerce 8.1

= 4.1.4 - 2023/09/13 =

- Atualizado o link para o rastreamento de objeto.

= 4.1.3 - 2023/09/13 =

- Corrida taxa de manuseio que não estava sendo aplicada no método "Correios (Nova API)".

= 4.1.2 - 2023/09/12 =

- Atualizado valor mínimo declarado para R$ 24,5.

= 4.1.1 - 2023/09/11 =

- Adicionado suporte para WooCommerce High-Performance Order Storage.
- Corrigida a validade do token dos Correios.
- Adicionada mensagem para avisar quando a API dos Correios não esta configurada corretamente.
- Removido suporte para versões anteriores ao WooCommerce 3.0.
- Corrigida a tabela de rastreamento de objeto para ser responsiva.

= 4.1.0 - 2023/09/11 =

- Atualizado valor mínimo declarado para 24 reais.
- Melhorada exibição das datas da tabela de rastreamento de objeto.
- Corrigido auto preenchimento de endereço quando a cidade tem apenas um CEP, prevenindo de remover o nome da rua e o bairro.
- Atualizado peso máximo e valores do Impresso Normal.
- Atualizado os preços de Carta Registrada.
- Correções gerais de estabilidade e prevenindo erros no WP.

= 4.0.0 - 2023/09/10 =

- Implementação da nova API dos Correios para calculo do valor de entrega, estimativa de entrega, rastreamento de objeto e de busca de endereço por CEP.
- Um novo método de entrega chamado "Correios (Nova API)" foi adicionado.

[See changelog for all versions](https://raw.githubusercontent.com/claudiosanches/woocommerce-correios/master/CHANGELOG.txt).

== Upgrade Notice ==

= 4.2.3 =

- Corrigido um erro relacionado a lista de serviços desatualizada que impedia editar o método de entrega.
- Atualizada a mensagem do e-mail para deixar claro que estimativa de entrega começa apenas depois do envio do produto.
- Corrigida conversão de peso para gramas.
- Adicionado suporte para WooCommerce 8.2+.
- Implementação de método de entrega internacional.
- Melhorada compatibilidade com PHP 8.2.
