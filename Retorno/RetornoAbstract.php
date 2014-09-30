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

namespace Retorno;
use Retorno\Exception;

/**
 * Classe base para leirura e processamento de dados do arquivo de retorno.
 * 
 * @package    Retorno
 * @author     Mauricio Rodrigues <mauricio.vsr@gmail.com>
 * @copyright  Copyright (c) 2014 M2 Digital (http://www.m2digital.com.br)
 * @license    MIT License
 * @version    1.0
 * 
 */

abstract class RetornoAbstract {

	/**
	 * Modo de abertura do arquivo de retorno
	 * For more info: http://php.net/manual/en/function.fopen.php
	 * @var string
	 */
	const FOPEN_MODE = 'r';

	/**
	 * Nome do arquivo submetido
	 * @var string
	 */
	protected $fileName;

	/**
	 * Tipo do arquivo submetido
	 * @var string
	 */
	protected $fileType;

	/**
	 * Endereço temporário do arquivo submetido
	 * @var string
	 */
	protected $fileTempName;

	/**
	 * Tamanho do arquivo submetido
	 * @var int
	 */
	protected $fileSize;

	/**
	 * Array para armazenar todas as linhas lidas no arquivo postado.
	 * @var array
	 */
	protected $lines = array();

	/**
	 * Header do arquivo de retorno.
	 * @var string
	 */
	protected $header;

	/**
	 * Array de detalhes do arquivo de retorno
	 * @var array 
	 */
	protected $detalhes = array();

	/**
	 * Registro Trailer do arquivo de retorno
	 * @var string
	 */
	protected $trailer;

	/**
	 * Constructor method
	 * @param array $file (optional) Posted file via form
	 * @return void
	 */
	public function __construct($file=null){
		if($file) {
			$this->setFile($file);
		}
	}


	/**
	 * Define arquivo de retorno a ser lido.
	 * @return RetornoAbstract Object
	 */
	public function setFile($file) {
		$this->fileName 		= $file['name'];
		$this->fileType 		= $file['type'];
		$this->fileTempName 	= $file['tmp_name'];
		$this->fileSize 		= $file['size'];

		// process data
		$this->process();
		
		return $this;
	}


	/**
	 * Proccess read data.
	 * @return Retorno
	 */
	protected function process(){
		$this->readFile()
			->makeHeader()
			->makeDetails()
			->makeTrailer();

		return $this;
	}


	/**
	 * Read all existing lines from posted file
	 * @return Retorno Object
	 */
	protected function readFile(){
		
		$fileHandle = fopen($this->fileTempName, self::FOPEN_MODE);

		$i = 0;

		while (($buffer = @fgets($fileHandle, 4096)) !== false) {
			$this->lines[$i++] = $buffer;
		}
		
		if (!@feof($fileHandle)) {
			throw new Exception("Error: unexpected fgets() fail. Please contact the system administrator.\n");
			exit;
		}

		fclose($fileHandle);

		return $this;
	}


	/**
	 * Recupera Header do arquivo lido
	 * @return string
	 */
	public function getHeader() {
		return $this->header;
	}


	/**
	 * Método onde deve ser implementada a leitura do Header.
	 * Header são linhas onde o Tipo de Registro possuem o valor 1.
	 * @return RetornoAbstract Object 
	 */
	protected abstract function makeHeader();


	/**
	 * Método onde deve ser implementada a leitura dos detalhes do arquivo.
	 * Os detalhes são linhas onde o Tipo de Registro possuem valor 0.
	 * @return RetornoAbstract Object 
	 */
	protected abstract function makeDetails();


	/**
	 * Retorna um array com todos detalhes.
	 * Cada indice do array, corresponde à uma linha do arquivo de retorno.
	 * @return array
	 */
	public function getDetalhes() {
		return $this->detalhes;
	}


	/**
	 * Método onde deve ser implementada a leitura do Registro Trailer do arquivo.
	 * O Registro Trailer é a última linha, onde o Tipo de Registro possuem valor 9.
	 * @return RetornoAbstract Object 
	 */
	protected abstract function makeTrailer();



	
}

?>