Arquivo de Retorno
==================

Classe em PHP que implementa uma interface IRetorno, para a leitura e o processamentos dos dados contidos no arquivo de retorno padrão CNAB400.

---

Bancos Implementados
--------------------

Até o momento a única implementação feita e homologada foi com o *Banco Itaú SA*.

A lista de bancos que ainda serão implementados é a seguinte:
* Banco do Brasil
* Bradesco
* HSBC
* Santander

Quem já possuir algo implementado e quiser contribuir, este positório aceita Forks, portanto... Fiquem à vontade! =) 

Como Utilizar
-------------

Crie um formulário HTML básico:

```HTML
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Arquivo de Retorno:</title>
</head>
<body>
	<form action="?" method="post" enctype="multipart/form-data">
		<label for="arquivo">Selecione o arquivo:</label>
		<input type="file" name="arquivo"><br>
		<button type="submit">Enviar</button>
	</form>
</body>
</html>
```

Depois de criar o arquivo HTML, crie um arquivo PHP, que receberá o POST do formulário. Neste arquivo você poderá instanciar o objeto de três formas direfentes e de sua livre escolha.

1. Instanciando o banco:
```php
<?php 
	use Retorno\Banco\Itau
	use Retorno\Utils;

	$retorno = new Itau();
	$retorno->setFile($_FILE['arquivo']);
?>
```
2. Instanciando pelo nome do banco via Factory:
```php
<?php 
	use Retorno\RetornoFactory;
	use Retorno\Utils;

	$retorno = new RetornoFactory::banco('Itau');
	$retorno->setFile($_FILE['arquivo']);
?>
```
3. Instanciando pelo número do banco via Factory:
```php
<?php 
	use Retorno\RetornoFactory;
	use Retorno\Utils;

	$retorno = new RetornoFactory::code(341);
	$retorno->setFile($_FILE['arquivo']);
?>
```

  ```php 
<?php

  if($_SERVER['REQUEST_METHOD']=='POST'){
	
  	$retorno = new Retorno();
  	$retorno->setFile($_FILES['arquivo']);
  	
  	foreach ($retorno->getDetalhes() as $detalhe) {
  		echo $detalhe->VALORPRINCIPAL . '<br>';
  	};
  	
  }
  ```
  
Propriedades do Header
----------------------

Após chamar o método $retorno->getHeader(), será retornado uma stdClass (Anonymous Class) com as propriedades abaixo listadas.
As propriedades foram criadas de acordo com os nomes existentes no arquivo de cobrança do Banco Itaú.

```
PROPRIEDADE 			NOME DO CAMPO 				SIGNIFICADO 										POSICAO		PICTURE		CONTEUDO
-------------------------------------------------------------------------------------------------------------------------------------------------
TIPOREGISTRO 			TIPO DE REGISTRO 			IDENTIFICACAO DO REGISTRO HEADER					001 001		9(01)		0
CODIGORETORNO			CODIGO DE RETORNO			IDENTIFICACAO DO ARQUIVO DE RETORNO					002 002		9(01)		2	
LITERALDERETORNO 		LITERAL DE RETORNO 			IDENTIFICACAO PER EXTENSO DO TIPO DE MOVIMENTO		003 009		X(07)		RETORNO
CODIGODOSERVICO 		CODIGO DO SERVICO 			IDENTIFICACAO DO TIPO DE SERVICO 					010 011		9(02)		01
LITERALDESERVICO 		LITERAL DE SERVICO 			IDENTIFICACAO POR EXTENSO DO TIPO DE SERVICO		012 026		X(15)		
AGENCIA 				AGENCIA 					AGENCIA MANTEDORA DA CONTA							027 030		9(04)		
ZEROS01				 	ZEROS 						COMPLEMENTO DE REGISTRO								031 032		9(02)		"00"		
CONTA 					AGENCIA 					AGENCIA MANTEDORA DA CONTA							033 037		9(05)		
DAC 					DAC 						DIGITO DE AUTOCONFERENCIA AG/CONTA DA EMPRESA		038 038		9(01)		
BRANCOS01				BRANCOS 					COMPLEMENTO DO REGISTRO								039 046		X(08)		
NOMEDAEMPRESA			NOME DA EMPRESA				NOME POR EXTENSO DA EMPRESA MAE						047 076		X(30)		
CODIGODOBANCO			CODIGO DO BANCO				NUMERO DO BANCO NA CAMARA DE COMPENSACAO			077 079		9(03)		NUMERO DO BANCO		
NOMEDOBANCO			 	NOME DO BRANCOS				NOME POR EXTENSO DO BANCO COBRADOR					080 094		9(15)		NOME DO BANCO	
DATADEGERACAO			DATA DE GERACAO				DATA DE GERACAO DO ARQUIVO							095 100		9(06)		DDMMAA		
DENSIDADE				DENSIDADE					UNIDADE DA DENSIDADE								101 105		9(05)				
UNIDADEDEDENSID		 	UNIDADE DE DENSID.			DENSIDADE DA GRAVACAO DO ARQUIVO					106 108		X(03)		BPI				
NRSEQARQUIVORET		 	NR. SEQ. ARQUIVO RET.		NUMERO SEQUENCIAL DO ARQUIVO DE RETORNO				109 113		9(05)						
DATADECREDITO			DATA DE CREDITO				DATA DE CREDITO DOS LANCAMENTOS						114 119		9(09)		DDMMAA						
BRANCOS02				BRANCOS 					COMPLEMENTO DO REGISTRO								120 394		X(275)		
NRSEQUENCIAL			NUMERO SEQUENCIAL			NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO			120 394		9(06)		000001	
```	

Propriedades de Detalhes
------------------------

Ao chamar o método $retorno->getDetalhes(), será retornado um array onde em cada indice conterá as propriedades abaixo listadas e seus respectivos valores. 

```
PROPRIEDADE 				NOME DO CAMPO 				SIGNIFICADO 										POSICAO		PICTURE		CONTEUDO
--------------------------------------------------------------------------------------------------------------------------------------------------------------------
TIPODEREGISTRO 				TIPO DE REGISTRO 			IDENTIFICACAO DO REGISTRO HEADER					001 001		9(01)		0
CODIGODEINSCRICAO 			CODIGO DE INSCRICAO			IDENTIFICACAO DO TIPO DE INSCRICAO/EMPRESA			002 003		9(02)		01=CPF 02=CNPJ	
NUMERODEINSCRICAO 			NUMERO DE INSCRICAO			NUMERO DE INSCRICAO DA EMPRESA (CPF/CNPJ)			004 017		9(14)		
AGENCIA 					AGENCIA						AGENCIA MANTEDORA DA CONTA 							018 021		9(04)		
ZEROS01 					ZEROS01						COMPLEMENTO DO REGISTRO 							022 023		9(02)		"00"
CONTA 						AGENCIA 					AGENCIA MANTEDORA DA CONTA							024 028		9(05)		
DAC 						DAC 						DIGITO DE AUTOCONFERENCIA AG/CONTA DA EMPRESA		029 029		9(01)		
BRANCOS01					BRANCOS 					COMPLEMENTO DO REGISTRO								030 037		X(08)		
USODAEMPRESA				USO DA EMPRESA				IDENTIFICACAO DO TITULO NA EMPRESA					038 062		X(25)		NOTA 2	
NOSSONUMERO01				NOSSO NUMERO				IDENTIFICACAO DO TITULO NO BANCO					063 070		9(08)		
BRANCOS02					BRANCOS 					COMPLEMENTO DO REGISTRO								071 082		X(12)		
NUMEROCARTEIRA				CARTEIRA					NUMERO CARTEIRA										083 085		9(03)		NOTA 5		
NOSSONUMERO02				NOSSO NUMERO				IDENTIFICACAO DO TITULO NO BANCO					086 093		9(08)		NOTA 3		
DACNOSSONUMERO02			DAC NOSSO NUMERO			DAC DO NOSSO NUMERO									094 094		9(01)		NOTA 3		
BRANCOS03					BRANCOS 					COMPLEMENTO DO REGISTRO								095 107		X(13)		
CODIGOCARTEIRA				CARTEIRA					CODIGO CARTEIRA										108 108		X(01)		NOTA 5		
CODIGODEOCORRENCIA			CODIGO DE OCORRENIA			IDENTIFICACAO DA OCORRENCIA							109 110		9(02)		NOTA 17		
DATADEOCORRENCIA			DATA DE OCORRENIA			DATA DE OCORRENCIA NO BANCO							111 116		X(06)		DDMMAAA		
NRDODOCUMENTO				NUMERO DO DOCUMENTO			NUMERO DO DOCUMENTO DE COBRANCA (DUPL., NP, ETC)	117 126		X(10)		NOTA 18		
NOSSONUMERO03				NUMERO DO DOCUMENTO			CONFIRMACAO DO NUMERO DO TITULO NO BANCO			117 126		9(08)				
BRANCOS04					BRANCOS 					COMPLEMENTO DO REGISTRO								135 146		X(12)		
VENCIMENTO					VENCIMENTO					DATA DE VENCIMENTO DO TITULO						147 152		X(06)		DDMMAAA		
VALORDOTIULO				VALOR DO DOCUMENTO			VALOR NOMINAL DO DOCUMENTO 							153 165		9(11)V9(2)	
CODIGODOBANCO				CODIGO DO BANCO				NUMERO DO BANCO NA CAMARA DE COMPENSACAO			166 168		9(09)	
AGENCIACOBRADORA			AGENCIA COBRADORA			AG. COBRADORA, AG. DE LIQUIDACAO OU BAIXA			169 172		9(04)		NOTA 9	
DACAGCOBRADORA				DAC AG. COBRADORA			DAC DA AGENCIA COBRADORA							173 173		9(01)			
ESPECIE						ESPECIE						ESPECIE DO TITULO									174 175		9(02)		NOTA 10			
TARIFADECOBRANCA			VALOR DO DOCUMENTO			VALOR DA DESPESA DE COBRANCA						176 188		9(11)V9(2)	
BRANCOS05					BRANCOS 					COMPLEMENTO DO REGISTRO								189 214		X(26)		
VALORDOIOF					VALOR DO IOF				VALOR DO IOF A SER RECOLHIDO (NOTAS SEGURO)			215 227		9(11)V9(2)			
VALORABATIMENTO				VALOR ABATIMENTO			VALOR DO ABATIMENTO CONCEDIDO						228 240		9(11)V9(2)	NOTA 19	
VALORDESCONTO				VALOR DESCONTO				VALOR DO DESCONTO CONCEDIDO							241 253		9(11)V9(2)	NOTA 19	
VALORPRINCIPAL				VALOR PRINCIPAL				VALOR LANCADO EM CONTA CORRENTE						254 266		9(11)V9(2)	
JUROSDEMORAMULTA			JUROS DE MORA/MULTA			VALOR DE MORA E MULTA 								267 279		9(11)V9(2)	
OUTROSCREDITOS				OUTROS CREDITOS				VALOR DE OUTROS CREDITOS							280 292		9(11)V9(2)	
BOLETODDA					BOLETO DDA 					INDICADOR DE BOLETO DDA 							293 293		X(01)		NOTA 34	
BRANCOS06					BRANCOS 					COMPLEMENTO DO REGISTRO								294 295		X(02)		
DATACREDITO					DATA CREDITO				DATA DE CREDITO DESTA LIQUIDACAO					296 301		X(06)		DDMMAA		
INSTRCANCELADA				INSTR. CANCELADA			CODIGO DA INSTRUCAO CANCELADA						302 305		9(04)		NOTA 20		
BRANCOS07					BRANCOS 					COMPLEMENTO DO REGISTRO								306 311		X(06)		
ZEROS02						ZEROS 						COMPLEMENTO DO REGISTRO								312 324		9(13)		
NOMEDOPAGADOR				NOME DO PAGADOR				NOME DO PAGADOR										325 354		X(30)		
BRANCOS07					BRANCOS 					COMPLEMENTO DO REGISTRO								355 377		X(23)		
ERROS						ERROS / MSG. INFORMATIVA	REG. REJEITADO/ALEGACAO PAGADOR/MSG INFORMATIVA		378 385		X(08)		NOTA 20		
BRANCOS08					BRANCOS 					COMPLEMENTO DO REGISTRO 							386 392		X(07)				
CODIGODELIQUIDACAO			COD. DE LIQUIDACAO			MEIO PELO QUAL O TITULO FOI LIQUIDADO				393 394		X(02)				
NUMEROSEQUENCIAL			NUMERO SEQUENCIAL			NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO			395 400		X(06)				
```	
