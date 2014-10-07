<?php 
	include("autoloader.php");

	use Retorno\RetornoFactory;
	use Retorno\Utils;

	if($_SERVER['REQUEST_METHOD']=='POST'){
		
		$retorno = RetornoFactory::banco('Itau');
		$retorno->setFile($_FILES['arquivo']);

		$header = $retorno->getHeader();

		$detalhes = $retorno->getDetalhes();

		foreach ($detalhes as $detalhe) {
			echo "(" . $retorno->getRecursoLiquidacao($detalhe->CODIGODELIQUIDACAO) . ") " . $detalhe->DATACREDITO . " - " . $detalhe->NOSSONUMERO01 .' - '. Utils::numberFormat($detalhe->VALORPRINCIPAL, false) . '<br>';
		};

		echo "<hr>";
		
	}

?>