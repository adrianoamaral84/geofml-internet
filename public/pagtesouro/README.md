# Cliente da API PagTesouro (Versão Atual 1.0.0)
  
Este cliente visa conexão com o Serviço do PagTesouro disponibilizado pela SERPRO. **Verifique sempre neste repositório as versões mais atuais. Caso tenha sugestões (rafael.87@gmail.com)**
   
  
   
  .

  # Importante: Certifique-se com o Órgão de Hospedagem (CT/CTA no caso de OMs do EB). Que a infraestrutura que provê seu site possui o PHP na versão 7.4 ou maior com a extensão cURL instalada. Caso contrário não será possível realizar a comunicação com os Servidores do SERPRO (API PagTesouro).
  

     
  .
  
# Passos para implementação
  
### Siga os passos abaixo para que o sistema esteja funcional:
  
> ## 1 - Gere os Códigos Necessários (Setor Financeiro)
   
 - A - Solicite o *Setor Financeiro* de sua organização (usuário SIAFI
   válido) que gere um **Token** para cada uma das UGs sobre sua
   gerência, no
   [SISGRU](https://www.sisgru.tesouro.gov.br/sisgru/public/pages/login.jsf).
   > Aba PagTesouro / Autorização de uso do PagTesouro / Inserir o código da UG , o período para utilização e setar Situação "ativo".
   
  
 - B - Solicite o *Setor Financeiro* (usuário SIAFI válido) que gere os
   **Códigos de Serviço** correspondentes para cada UG., no
   [SISGRU](https://www.sisgru.tesouro.gov.br/sisgru/public/pages/login.jsf).
   > Aba PagTesouro / Catálago de serviços / Inserir o código da UG, código de recolhimento da GRU e o tipo de serviço. Após isso, selecione Incluir.
   

 - ~~C - Teste o token e os códigos gerados no simulados através do [ambiente de testes da API](https://valpagtesouro.tesouro.gov.br/simulador/#/pages/pagamento/tabs)~~

 - **Em alguns casos o teste no Simulador da SERPRO falha (UG não possui autorização de acesso ativa) mas neste cliente funciona. Portanto mesmo que o teste falhe continue com a instalação/configuração deste cliente**
  
  .
  
  <!-- ![Simulador PagTesouro](http://www.10rm.eb.mil.br/images/pagtes_imgs/p1c.png)   -->
  
   
  .
  
> ## 2 - Faça Download do projeto (Download ZIP)
    
  .
Acesse o repositório GitHub do projeto (se você já está lendo através do GitHub já está no local correto). 
  .
  
  ![Download do Projeto](http://www.10rm.eb.mil.br/images/pagtes_imgs/p2.png)  
  
     
  .

> ## 3 - Renomeie a Pasta
Renomeie a pasta para somente **pagtesouro** ou o nome que preferir (por padrão ela é extraída como **pagtesouro-main** )
 
     
  .
  
> ## 4 - Obtenha o Arquivo de Configuração
  
Acesse a página de Geração do Arquivo de configuração [CLIQUE AQUI](https://www.10rm.eb.mil.br/pagtesouro/config.php). e preencha os dados da sua organização, UGs e Códigos de Serviço.  Observe a área de Pendências e assim que o botão de download estiver verde você já pode realizar o download do arquivo config.json
  
  .
  
  ![Geração do Arquivo de Configuração](http://www.10rm.eb.mil.br/images/pagtes_imgs/p3.png)  
  
  .
  Quanto estiver tudo OK
  ![Geração do Arquivo de Configuração](http://www.10rm.eb.mil.br/images/pagtes_imgs/pOk.png)  
  .
  
  
> ## 5 - Coloque o arquivo config.json na pasta config do projeto
  
Após o download, vá até a pasta onde o arquivo baixado se encontra, copie-o e coloque-o na pasta config do projeto
   
  .
  
  ![Arquivo config.json](http://www.10rm.eb.mil.br/images/pagtes_imgs/p4_1.png)  
  ![Arquivo config.json](http://www.10rm.eb.mil.br/images/pagtes_imgs/p4_2.png)  
   
  .
  
> ## 6 - Mude a logo
  
Substitua o arquivo **logo.png** para o logo da sua organização *(Medidas recomendadas: 60px de largura / 70px de altura)*
   
  .
  
  ![Arquivo config.json](http://www.10rm.eb.mil.br/images/pagtes_imgs/p5_1.png)  
   
  .
  
  
> ## 7 - Envie a pasta do projeto "pagtesouro" para seu site
  
Utilize um cliente FTP para enviar a pasta "pagtesouro" para a estrutura de pastas do seu site. Recomendo a utilização do programa FileZilla. Lembrando que a estrutura de pastas será utilizada para acessar o sistema através do link. (Exemplo "site.mil.br/pasta1/pasta2.../pagtesouro")
   
   .
  
  ![FileZilla](http://www.10rm.eb.mil.br/images/pagtes_imgs/p6.png)  
     
  .
  
  
> ## 8 - Teste
  
Acesse o site de sua organização,  (Exemplo: *www.suaorganizacao.gov.br/pagtesouro*) e verifique se os parâmetros setados estão aparecendo e se as solicitações estão sendo geradas. É importante acessar o site e ao final colocar o diretório /pagtesouro).
  
   .
  
  ![Teste de Aplicação](http://www.10rm.eb.mil.br/images/pagtes_imgs/p7.png)  
  
   
  . 
  
  
> ## 9 - Gere Links Personalizados (Por UG ou Serviço)
  
Caso deseje direcionar o usuário do site para um pagamento específico (UG, serviço e/ou valor pré-selecionados)

**Exemplos de Link Personalizado**



**Somente com UG selecionada** (www.suaorganizacao.eb.mil.br/pagtesouro?ug=160000)



**UG e Serviço Selecionados** ( ... /pagtesouro?ug=160000&servico=1111)



**UG, Serviço e Valor Selecionados** ( ... /pagtesouro?ug=160000&servico=1111&valor=33,33)
   
  .
  
  
  
> # Dúvidas/Sugestões
  
Caso tenha dúvidas entre em contato com o desenvolvedor através do e-mail **rafael.87@gmail.com**
  