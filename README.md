# String_Parser

include_once 'Parser.php';  
$string = '{Будь ласка,|Просто|Якщо зможете,} прийміть {просте|дуже{ складне| вдале}} рішення';  
$parser = new Parser($string);  
$parser->parse();  
echo $parser->getResultString();

Якщо зможете, прийміть дуже складне рішення
