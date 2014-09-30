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
