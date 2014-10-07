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
	 * Indica o canal utilizado pelo PAGADOR para pagamento do BOLETO e, para clientes que possuem 
	 * o credito das liquidacoes separado em funcao do recurso utilizado no pagamento, indica 
	 * se o credito do valor correspondente estara “disponivel” ou “a compensar” na data do 
	 * lancamento em conta corrente.
	 * @var array
	 */
	protected $codigoLiquidacao = array();

	/**
	 * Indica o local onde o arquivo de retorno deverá ser salvo
	 * @var string
	 */
	public $local = null;


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
	 * Recupera todas as linhas existentes no arquivo postado
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
	 * Salva o arquivo de retorno 
	 * @param string $local Opicional. Local onde o arquivo deverá ser salvo.
	 * Se não informado, requer que o local seja setado via $this->local = 'path'. 
	 * 
	 * @return Retorno Object 
	 */
	public function save($local=null) {

		if(!is_null($local)) 
			$this->local = $local;

		if(is_null($this->local) || $this->local=='') {
			throw new Exception("O local onde o arquivo deverá ser salvo não foi informado. Contate o administrador do sistema.");
		}

		$_file = $this->local . DIRECTORY_SEPARATOR . $this->fileName;

		if(!is_dir($this->local))
			throw new Exception("Local informado invalido.");
		
		elseif(!is_writable($this->local))
			throw new Exception("Local informado nao possui permissao para escrita.");
		
		else
			move_uploaded_file($this->fileTempName, $_file);
		

		return $this;
	}

	/**
	 * Retorna o nome do arquivo
	 * @return string
	 */
	public function getFileName() {
		return $this->fileName;
	}


	/**
	 * Retorna o tipo do arquivo
	 * @return string
	 */
	public function getFileType() {
		return $this->fileType;
	}

	/**
	 * Retorna o tmp_name do arquivo
	 * @return string
	 */
	public function getFileTempName() {
		return $this->fileTempName;
	}

	/**
	 * Retorna o tamanho do arquivo
	 * @return int
	 */
	public function getFileSize() {
		return $this->fileSize;
	}


	/**
	 * Recupera Header do arquivo lido
	 * @return stdClass Object
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
	 * Todos os dados processados, em cada linha existente, foi transformado em um atributo
	 * de um objeto anônimo stdClass.
	 * @return array
	 */
	public function getDetalhes() {
		return $this->detalhes;
	}


	/**
	 * Retorna a descrição para o código de liquidação informado
	 * @param string $codigo
	 * @return string|midex Retorna a descrição ou NULL caso seja informado um código desconhecido/inexistente.  
	 */
	public function getDescricaoLiquidacao($codigo) {
		if (array_key_exists($codigo, $this->codigoLiquidacao)) {
			return $this->codigoLiquidacao[$codigo]['descricao'];
		};
		return null;
	}


	/**
	 * Retorna a descrição para o código de liquidação informado
	 * @param string $codigo
	 * @return string|midex Retorna a descrição ou NULL caso seja informado um código desconhecido/inexistente.  
	 */
	public function getRecursoLiquidacao($codigo) {
		if (array_key_exists($codigo, $this->codigoLiquidacao)) {
			return $this->codigoLiquidacao[$codigo]['recurso'];
		};
		return null;
	}


	/**
	 * Método onde deve ser implementada a leitura do Registro Trailer do arquivo.
	 * O Registro Trailer é a última linha, onde o Tipo de Registro possuem valor 9.
	 * @return RetornoAbstract Object 
	 */
	protected abstract function makeTrailer();


	/**
	 * Retorna o Trailer do arquivo em forma de objeto stdClass.
	 * O Trailer é a linha onde o TIPODEREGISTRO possui o valor 9.
	 * @return stdClass Object
	 */
	public function getTrailer() {
		return $this->trailer;
	}
	
}
