<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 12.04.2016
 * Time: 14:11
 */
include_once 'Parser.php';
$string = '{Будь ласка,|Просто|Якщо зможете,} прийміть {просте|дуже{ складне| вдале}} рішення';
$parser = new Parser($string);
$parser->parse();
echo $parser->getResultString();
