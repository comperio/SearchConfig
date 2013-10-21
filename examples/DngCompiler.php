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
use DSQ\Expression\Builder\ExpressionBuilder;
use DSQ\Lucene\Compiler\LuceneCompiler;
use DSQ\Lucene\Compiler\LuceneQueryCompiler;

include '../vendor/autoload.php';
ini_set('xdebug.var_display_max_depth', '10');
$start = microtime(true);
$compiler = new DNGCompiler();
$queryCompiler = new LuceneQueryCompiler();
$urlCompiler = new AdvancedUrlCompiler();
$builder = new ExpressionBuilder('and');

$expression = $builder
    //->field('series', 'asd"sd:')
    ->binary('>')
        ->value('foo')
        ->value(2123)
    ->end()
    ->and()
        ->field('facets-target', 'm')
        ->field('year', array('from' => 2000, 'to' => 3000))
        ->field('class', 'ciao')
        ->field('class', '830')
        ->field('publisher', 'mondadori')
        ->field('solr', 'sorti_date:["2000" TO "2010"]')
    //->field('subj-and-type', array('s' => 'ragazzi', 't' => 'firenze'))
        ->field('materiale', array('bibtype' => 'ah'))
    ->end()
    ->not()
        ->field('materiale', array('bibtypefirst' => 'boh'))
        ->field('id-subj', array('value' => 'ciao', 'name' => 'boh'))
        ->field('id-subj', 'scalar')
        ->field('facets-target', 'm')
        ->field('libarea', 1)
        ->field('loanable', 0)
        ->field('standard-number', array('subfield' => 'EAN', 'value' => 123))
        ->field('ean', 123)
    ->end()
    ->get();

var_dump($expression);
$expr = $compiler->compile($expression);
var_dump($expr);
echo $expr->getMainQuery();
var_dump(microtime(true) - $start);

$start = microtime(true);
var_dump( $urlCompiler->compile($expression));
var_dump(microtime(true) - $start);

$start = microtime(true);
var_dump($queryCompiler->compile($expr)->convertExpressionsToStrings());
var_dump(microtime(true) - $start);

var_dump(memory_get_peak_usage());