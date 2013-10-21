<?php
/**
 * This file is part of SearchConfig
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ;

use Comperio\DSQ\Compiler\AdvancedUrlCompiler;
use Comperio\DSQ\Compiler\DNGCompiler;
use Dissect\Parser\LALR1\Parser;
use DSQ\Expression\Builder\ExpressionBuilder;
use DSQ\Language\Grammar;
use DSQ\Language\Lexer;
use DSQ\Lucene\Compiler\LuceneCompiler;
use DSQ\Lucene\Compiler\LuceneQueryCompiler;

include '../vendor/autoload.php';
ini_set('xdebug.var_display_max_depth', '10');
$start = microtime(true);
$compiler = new DNGCompiler();
$queryCompiler = new LuceneQueryCompiler();

$query = isset($_GET['q'])
    ? $_GET['q']
    : 'autha = Manzoni OR facets-target = m OR tid NOT IN (test:catalog:1, test:catalog:2, test:catalog:3) AND year != 2000'
;
$parser = new Parser(new Grammar);
$lexer = new Lexer;
$expression = $parser->parse($lexer->lex($query));
//var_dump($expression);
$luceneQuery = $compiler->compile($expression);
//var_dump($luceneQuery);
printf("<b>Input query:</b><br>");
printf("<pre>$query</pre>");
printf("<b>Output query:</b><br>");
printf("<pre>{$luceneQuery->getMainQuery()}</pre>");
printf("<b>Filters:</b><br>");
foreach ($luceneQuery->getFilterQueries() as $fileter)
{
    printf("<pre>$fileter</pre>");
}
printf("<br><br><a href='http://opac.provincia.brescia.it/opac/search/lst?solr=%s'>DNG!</a>", urlencode($luceneQuery->getMainQuery()));


