<?php 

/*
 * Retorno PHP - Processamento de arquivos de Retorno
 *
 * LICENSE: The MIT License (MIT)
 *
 * Copyright (C) 2014 M2 Digital
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this
 * software and associated documentation files (the "Software"), to deal in the Software
 * without restriction, including without limitation the rights to use, copy, modify,
 * merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies
 * or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Retorno\Banco;

use Retorno\RetornoAbstract;
use Retorno\Exception;
use Retorno\Utils;

/**
 * Classe para leitura do arquivo de retorno do Banco Itau S.A.
 * 
 * @package    Retorno
 * @author     Mauricio Rodrigues <mauricio.vsr@gmail.com>
 * @copyright  Copyright (c) 2014 M2 Digital (http://www.m2digital.com.br)
 * @license    MIT License
 * @version    1.0
 * 
 */

class Itau extends RetornoAbstract 
{

	/**
	 * Processa o Header do arquivo enviado.
	 * Header são linhas onde o Tipo de Registro possuem o valor 0.
	 * @return RetornoAbstract Object 
	 */
	protected function makeHeader() {
		if(count($this->lines)==0) return null;

		$headerLine = $this->lines[0];

		$header = new \stdClass();													# NOME DO CAMPO 				SIGNIFICADO 										POSICAO		PICTURE		CONTEUDO
		$header->TIPOREGISTRO 			= substr($headerLine, 0, 1);				# TIPO DE REGISTRO 				IDENTIFICACAO DO REGISTRO HEADER					001 001		9(01)		0
		$header->CODIGORETORNO			= substr($headerLine, 1, 1);				# CODIGO DE RETORNO				IDENTIFICACAO DO ARQUIVO DE RETORNO					002 002		9(01)		2	
		$header->LITERALDERETORNO 		= substr($headerLine, 2, 7);				# LITERAL DE RETORNO 			IDENTIFICACAO PER EXTENSO DO TIPO DE MOVIMENTO		003 009		X(07)		RETORNO
		$header->CODIGODOSERVICO 		= substr($headerLine, 9, 2);				# CODIGO DO SERVICO 			IDENTIFICACAO DO TIPO DE SERVICO 					010 011		9(02)		01
		$header->LITERALDESERVICO 		= substr($headerLine, 11, 15);				# LITERAL DE SERVICO 			IDENTIFICACAO POR EXTENSO DO TIPO DE SERVICO		012 026		X(15)		
		$header->AGENCIA 				= substr($headerLine, 26, 4);				# AGENCIA 						AGENCIA MANTEDORA DA CONTA							027 030		9(04)		
		$header->ZEROS01				= substr($headerLine, 31, 2);				# ZEROS 						COMPLEMENTO DE REGISTRO								031 032		9(02)		"00"		
		$header->CONTA 					= substr($headerLine, 32, 5);				# AGENCIA 						AGENCIA MANTEDORA DA CONTA							033 037		9(05)		
		$header->DAC 					= substr($headerLine, 37, 1);				# DAC 							DIGITO DE AUTOCONFERENCIA AG/CONTA DA EMPRESA		038 038		9(01)		
		$header->BRANCOS01				= substr($headerLine, 38, 8);				# BRANCOS 						COMPLEMENTO DO REGISTRO								039 046		X(08)		
		$header->NOMEDAEMPRESA			= substr($headerLine, 46, 30);				# NOME DA EMPRESA				NOME POR EXTENSO DA EMPRESA MAE						047 076		X(30)		
		$header->CODIGODOBANCO			= substr($headerLine, 76, 3);				# CODIGO DO BANCO				NUMERO DO BANCO NA CAMARA DE COMPENSACAO			077 079		9(03)		NUMERO DO BANCO		
		$header->NOMEDOBANCO			= substr($headerLine, 79, 15);				# NOME DO BRANCOS				NOME POR EXTENSO DO BANCO COBRADOR					080 094		9(15)		NOME DO BANCO	
		$header->DATADEGERACAO			= substr($headerLine, 94, 6);				# DATA DE GERACAO				DATA DE GERACAO DO ARQUIVO							095 100		9(06)		DDMMAA		
		$header->DENSIDADE				= substr($headerLine, 100, 5);				# DENSIDADE						UNIDADE DA DENSIDADE								101 105		9(05)				
		$header->UNIDADEDEDENSID		= substr($headerLine, 105, 3);				# UNIDADE DE DENSID.			DENSIDADE DA GRAVACAO DO ARQUIVO					106 108		X(03)		BPI				
		$header->NRSEQARQUIVORET		= substr($headerLine, 108, 5);				# NR. SEQ. ARQUIVO RET.			NUMERO SEQUENCIAL DO ARQUIVO DE RETORNO				109 113		9(05)						
		$header->DATADECREDITO			= substr($headerLine, 113, 6);				# DATA DE CREDITO				DATA DE CREDITO DOS LANCAMENTOS						114 119		9(09)		DDMMAA						
		$header->BRANCOS02				= substr($headerLine, 119, 275);			# BRANCOS 						COMPLEMENTO DO REGISTRO								120 394		X(275)		
		$header->NRSEQUENCIAL			= substr($headerLine, 394, 400);			# NUMERO SEQUENCIAL				NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO			120 394		9(06)		000001		
		
		$this->header = $header;

		return $this;
	}

	
	/**
	 * Processa os detalhes do arquivo.
	 * Detalhes são linhas onde o Tipo de Registro possuem o valor 1
	 * 
	 */
	protected function makeDetails(){
		
		if(count($this->lines)==0) return null;

		$i=0;
				
		foreach($this->lines as $line) {
			
			// verifica o tipo de registro, antes de recuperar os detalhes 
			if(substr($line, 0, 1)=='1') {
				$details[$i] = new \stdClass();											# NOME DO CAMPO 				SIGNIFICADO 										POSICAO		PICTURE		CONTEUDO
				$details[$i]->TIPODEREGISTRO 		= substr($line, 0, 1);				# TIPO DE REGISTRO 				IDENTIFICACAO DO REGISTRO HEADER					001 001		9(01)		0
				$details[$i]->CODIGODEINSCRICAO 	= substr($line, 1, 2);				# CODIGO DE INSCRICAO			IDENTIFICACAO DO TIPO DE INSCRICAO/EMPRESA			002 003		9(02)		01=CPF 02=CNPJ	
				$details[$i]->NUMERODEINSCRICAO 	= substr($line, 3, 14);				# NUMERO DE INSCRICAO			NUMERO DE INSCRICAO DA EMPRESA (CPF/CNPJ)			004 017		9(14)		
				$details[$i]->AGENCIA 				= substr($line, 17, 4);				# AGENCIA						AGENCIA MANTEDORA DA CONTA 							018 021		9(04)		
				$details[$i]->ZEROS01 				= substr($line, 21, 2);				# ZEROS01						COMPLEMENTO DO REGISTRO 							022 023		9(02)		"00"
				$details[$i]->CONTA 				= substr($line, 23, 5);				# AGENCIA 						AGENCIA MANTEDORA DA CONTA							024 028		9(05)		
				$details[$i]->DAC 					= substr($line, 28, 1);				# DAC 							DIGITO DE AUTOCONFERENCIA AG/CONTA DA EMPRESA		029 029		9(01)		
				$details[$i]->BRANCOS01				= substr($line, 29, 8);				# BRANCOS 						COMPLEMENTO DO REGISTRO								030 037		X(08)		
				$details[$i]->USODAEMPRESA			= substr($line, 37, 25);			# USO DA EMPRESA				IDENTIFICACAO DO TITULO NA EMPRESA					038 062		X(25)		NOTA 2	
				$details[$i]->NOSSONUMERO01			= substr($line, 62, 8);				# NOSSO NUMERO					IDENTIFICACAO DO TITULO NO BANCO					063 070		9(08)		
				$details[$i]->BRANCOS02				= substr($line, 70, 12);			# BRANCOS 						COMPLEMENTO DO REGISTRO								071 082		X(12)		
				$details[$i]->NUMEROCARTEIRA		= substr($line, 82, 3);				# CARTEIRA						NUMERO CARTEIRA										083 085		9(03)		NOTA 5		
				$details[$i]->NOSSONUMERO02			= substr($line, 85, 8);				# NOSSO NUMERO					IDENTIFICACAO DO TITULO NO BANCO					086 093		9(08)		NOTA 3		
				$details[$i]->DACNOSSONUMERO02		= substr($line, 93, 1);				# DAC NOSSO NUMERO				DAC DO NOSSO NUMERO									094 094		9(01)		NOTA 3		
				$details[$i]->BRANCOS03				= substr($line, 94, 13);			# BRANCOS 						COMPLEMENTO DO REGISTRO								095 107		X(13)		
				$details[$i]->CODIGOCARTEIRA		= substr($line, 107, 1);			# CARTEIRA						CODIGO CARTEIRA										108 108		X(01)		NOTA 5		
				$details[$i]->CODIGODEOCORRENCIA	= substr($line, 108, 2);			# CODIGO DE OCORRENIA			IDENTIFICACAO DA OCORRENCIA							109 110		9(02)		NOTA 17		
				$details[$i]->DATADEOCORRENCIA		= substr($line, 110, 6);			# DATA DE OCORRENIA				DATA DE OCORRENCIA NO BANCO							111 116		X(06)		DDMMAAA		
				$details[$i]->NRDODOCUMENTO			= substr($line, 116, 10);			# NUMERO DO DOCUMENTO			NUMERO DO DOCUMENTO DE COBRANCA (DUPL., NP, ETC)	117 126		X(10)		NOTA 18		
				$details[$i]->NOSSONUMERO03			= substr($line, 126, 8);			# NUMERO DO DOCUMENTO			CONFIRMACAO DO NUMERO DO TITULO NO BANCO			117 126		9(08)				
				$details[$i]->BRANCOS04				= substr($line, 134, 12);			# BRANCOS 						COMPLEMENTO DO REGISTRO								135 146		X(12)		
				$details[$i]->VENCIMENTO			= substr($line, 146, 6);			# VENCIMENTO					DATA DE VENCIMENTO DO TITULO						147 152		X(06)		DDMMAAA		
				$details[$i]->VALORDOTIULO			= substr($line, 152, 13);			# VALOR DO DOCUMENTO			VALOR NOMINAL DO DOCUMENTO 							153 165		9(11)V9(2)	
				$details[$i]->CODIGODOBANCO			= substr($line, 165, 3);			# CODIGO DO BANCO				NUMERO DO BANCO NA CAMARA DE COMPENSACAO			166 168		9(09)	
				$details[$i]->AGENCIACOBRADORA		= substr($line, 168, 4);			# AGENCIA COBRADORA				AG. COBRADORA, AG. DE LIQUIDACAO OU BAIXA			169 172		9(04)		NOTA 9	
				$details[$i]->DACAGCOBRADORA		= substr($line, 172, 1);			# DAC AG. COBRADORA				DAC DA AGENCIA COBRADORA							173 173		9(01)			
				$details[$i]->ESPECIE				= substr($line, 173, 2);			# ESPECIE						ESPECIE DO TITULO									174 175		9(02)		NOTA 10			
				$details[$i]->TARIFADECOBRANCA		= substr($line, 175, 13);			# VALOR DO DOCUMENTO			VALOR DA DESPESA DE COBRANCA						176 188		9(11)V9(2)	
				$details[$i]->BRANCOS05				= substr($line, 188, 26);			# BRANCOS 						COMPLEMENTO DO REGISTRO								189 214		X(26)		
				$details[$i]->VALORDOIOF			= substr($line, 214, 13);			# VALOR DO IOF					VALOR DO IOF A SER RECOLHIDO (NOTAS SEGURO)			215 227		9(11)V9(2)			
				$details[$i]->VALORABATIMENTO		= substr($line, 227, 13);			# VALOR ABATIMENTO				VALOR DO ABATIMENTO CONCEDIDO						228 240		9(11)V9(2)	NOTA 19	
				$details[$i]->VALORDESCONTO			= substr($line, 240, 13);			# VALOR DESCONTO				VALOR DO DESCONTO CONCEDIDO							241 253		9(11)V9(2)	NOTA 19	
				$details[$i]->VALORPRINCIPAL		= substr($line, 253, 13);			# VALOR PRINCIPAL				VALOR LANCADO EM CONTA CORRENTE						254 266		9(11)V9(2)	
				$details[$i]->JUROSDEMORAMULTA		= substr($line, 266, 13);			# JUROS DE MORA/MULTA			VALOR DE MORA E MULTA 								267 279		9(11)V9(2)	
				$details[$i]->OUTROSCREDITOS		= substr($line, 279, 13);			# OUTROS CREDITOS				VALOR DE OUTROS CREDITOS							280 292		9(11)V9(2)	
				$details[$i]->BOLETODDA				= substr($line, 292, 1);			# BOLETO DDA 					INDICADOR DE BOLETO DDA 							293 293		X(01)		NOTA 34	
				$details[$i]->BRANCOS06				= substr($line, 293, 2);			# BRANCOS 						COMPLEMENTO DO REGISTRO								294 295		X(02)		
				$details[$i]->DATACREDITO			= substr($line, 295, 6);			# DATA CREDITO					DATA DE CREDITO DESTA LIQUIDACAO					296 301		X(06)		DDMMAA		
				$details[$i]->INSTRCANCELADA		= substr($line, 301, 4);			# INSTR. CANCELADA				CODIGO DA INSTRUCAO CANCELADA						302 305		9(04)		NOTA 20		
				$details[$i]->BRANCOS07				= substr($line, 305, 6);			# BRANCOS 						COMPLEMENTO DO REGISTRO								306 311		X(06)		
				$details[$i]->ZEROS02				= substr($line, 311, 13);			# ZEROS 						COMPLEMENTO DO REGISTRO								312 324		9(13)		
				$details[$i]->NOMEDOPAGADOR			= substr($line, 324, 30);			# NOME DO PAGADOR				NOME DO PAGADOR										325 354		X(30)		
				$details[$i]->BRANCOS07				= substr($line, 354, 23);			# BRANCOS 						COMPLEMENTO DO REGISTRO								355 377		X(23)		
				$details[$i]->ERROS					= substr($line, 377, 8);			# ERROS / MSG. INFORMATIVA		REG. REJEITADO/ALEGACAO PAGADOR/MSG INFORMATIVA		378 385		X(08)		NOTA 20		
				$details[$i]->BRANCOS08				= substr($line, 385, 7);			# BRANCOS 						COMPLEMENTO DO REGISTRO 							386 392		X(07)				
				$details[$i]->CODIGODELIQUIDACAO	= substr($line, 392, 2);			# COD. DE LIQUIDACAO			MEIO PELO QUAL O TITULO FOI LIQUIDADO				393 394		X(02)				
				$details[$i]->NUMEROSEQUENCIAL		= substr($line, 394, 6);			# NUMERO SEQUENCIAL				NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO			395 400		X(06)				
				$i++;
			}

		}

		$this->detalhes = $details;

		return $this;
		
	}


	/**
	 * Método onde deve ser implementada a leitura do Registro Trailer do arquivo.
	 * O Registro Trailer é a última linha, onde o Tipo de Registro possuem valor 9.
	 * @return RetornoAbstract Object 
	 */
	protected function makeTrailer() {
		
		if(count($this->lines)==0) return null;

		// $trailer
	}
	
}

?>