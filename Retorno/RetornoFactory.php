<?php

/*
 * LICENSE: The MIT License (MIT)
 *
 * Copyright (C) 2014 Mauricio Rodrigues
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
use Retorno\Utils;
use Retorno\Exception;

/**
 * Classe Factory para criação de instâncias de Retornos
 *
 * @package    Retorno
 * @author     Mauricio Rodrigues <mauricio.vsr@gmail.com>
 * @copyright  Copyright (c) 2014 M2 Digital (http://www.m2digital.com.br)
 * @license    MIT License
 * @version    1.0
 */
class RetornoFactory
{
    /**
     * Mapa das classes dos Bancos e seus códigos
     * @var array
     */
    protected static $classMap = array(
        341 => 'Itau'
    );

    /**
     * Retorna a instância de um Banco através do código
     *
     * @param int|string $codBanco Código do Banco
     * @param array $file $_FILE['arquivo']
     * @throws Exception Quando o banco não é suportado
     * @return RetornoAbstract
     */
    public static function code($codBanco, array $file = null)
    {
        Utils::dd(static::$classMap[$codBanco]);
        
        if (! isset(static::$classMap[$codBanco])) {
            throw new Exception(sprintf('O banco de código "%s" não é surportado.', $codBanco));
        }

        return static::banco(static::$classMap[$codBanco], $file);
    }

    /**
     * Retorna a instância de um Banco através do nome
     *
     * @param string $nome Nome do Banco
     * @param array $params Parâmetros iniciais para construção do objeto
     * @throws Exception Quando a classe não é encontrada
     * @return RetornoAbstract
     */
    public static function banco($nome, array $params = null)
    {
        $class = __NAMESPACE__ . '\\Banco\\' . $nome;

        if (! class_exists($class)) {
            throw new Exception(sprintf('A classe "%s" não existe.', $class));
        }

        return new $class($params);
    }
}
