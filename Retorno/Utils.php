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

/**
 * Classe de Utilidades
 * 
 * @package Retorno
 * @author Mauricio Rodrigues <mauricio.vsr@gmail.com>
 * @copyright  Copyright (c) 2014 M2 Digital (http://www.m2digital.com.br)
 * @license MIT License
 * @version 0.1
 * 
 */

class Utils{

	/**
	 * PRE - Tag de abertura
	 * @var string
	 */
	const PRE_INI = "<pre>";

	/**
	 * PRE - tag de fechamento
	 * @var string
	 */
	const PRE_END = "</pre>";


	/**
	 * Execute a var_dump and die methods.
	 * The script will be stoped after call this method.
	 * @param array $data
	 * @return void
	 */
	public static function dd($data){
		echo self::PRE_INI;
		var_dump($data);
		echo self::PRE_END;
		die();
	}


	/**
	 * Print array data recursively.
	 * @param array $data
	 * @return void
	 */
	public static function pre($data){
		echo self::PRE_INI;
		print_r($data);
		echo self::PRE_END;
	}


	/**
	 * Replaces assumed decimal comma
	 * 
	 * @param string $valor Valor with decimal comma assumed
	 * @param boolean $raw If true, return brazilian real formated decimal
	 * @param int $decimalLength Set de length 
	 * @return string
	 */
	public static function numberFormat($valor, $raw = true, $decimalLength = 2){

		$left 	= substr($valor, 0, strlen($valor)-$decimalLength);
		$right 	= substr($valor, strlen($valor)-$decimalLength, strlen($valor));
		$tmp 	= $left .'.'. $right;  

		return $raw ? number_format($tmp,2,".","") : number_format($tmp, 2, ',', '.');
	}


	/**
	 * Método para tratar as datas existentes no arquivo de retorno.
	 * 
	 * @param string $data data no formato (dmy - 311214)
	 * @param string $formatToReturn Formato da data que será retornada. More info: http://php.net/manual/en/datetime.formats.php
	 * @param string $timezone Representation of time zone. More info: http://php.net/manual/en/class.datetimezone.php
	 * @return mixed string|DateTime Object
	 */
	public static function dateFormat($data, $formatToReturn = null, $timezone = 'America/Fortaleza'){
		
		$dia = substr($data, 0, 2);
		$mes = substr($data, 2, 2);
		$ano = '20' . substr($data, 4, 2);
		
		$format = "{$ano}-{$mes}-{$dia}";
		
		$dateTime = new DateTime($format, new DateTimeZone($timezone));
		
		return is_null($formatToReturn) ? $dateTime : $dateTime->format($formatToReturn);

	}

}