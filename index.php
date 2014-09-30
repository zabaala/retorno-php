<?php 
	include("autoloader.php");

	use Retorno\RetornoFactory;
	use Retorno\Utils;

	if($_SERVER['REQUEST_METHOD']=='POST'){
		
		$retorno = RetornoFactory::banco('Itau');
		$retorno->setFile($_FILES['arquivo']);
		
		$header = $retorno->getHeader();
		// Utils::pre($header);

		$detalhes = $retorno->getDetalhes();
		// Utils::pre($detalhes);
		foreach ($detalhes as $detalhe) {
			echo Utils::numberFormat($detalhe->VALORPRINCIPAL, false) . '<br>';
		};
		
	}

?>

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