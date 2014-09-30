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

Enviando arquivo de retorno e recuperando informações de detalhes:

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
