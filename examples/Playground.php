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

$query = isset($_REQUEST['q'])
        ? $_REQUEST['q']
        : 'autha = Manzoni OR facets-target = m OR tid NOT IN (test:catalog:1, test:catalog:2, test:catalog:3) AND year != 2000'
;
$parser = new Parser(new Grammar);
$lexer = new Lexer;
//var_dump($expression);

//var_dump($luceneQuery);
/*printf("<b>Input query:</b><br>");
printf("<pre>$query</pre>");
printf("<b>Output query:</b><br>");
printf("<pre>{$luceneQuery->getMainQuery()}</pre>");
printf("<b>Filters:</b><br>");
foreach ($luceneQuery->getFilterQueries() as $filter)
{
    printf("<pre>$filter</pre>");
}
printf("<br><br><a href='http://opac.provincia.brescia.it/opac/search/lst?solr=%s'>DNG!</a>", urlencode($luceneQuery->getMainQuery()));*/

function compile($string)
{
    global $compiler, $lexer, $parser;
    try {
        return (string) $compiler->compile($parser->parse($lexer->lex($string)));
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
}

if (isset($_REQUEST['action'])) {
    switch ($_REQUEST['action']) {
        case 'compile':
            echo compile($_REQUEST['query']);
            exit();
        case 'dng':
            header('Location: ' . $_REQUEST['dng'] . '/opac/search/lst?solr=' . urlencode(compile($_REQUEST['query'])));
            exit();
    }
}

$examples = array(
    'home-lib' => 'value',
    'subject' => 'value',
    'subject-type' => 'value',
    'subj-and-type' => '(t = G, s = "Religioni")',
    'series' => 'value',
    'dewey' => 'value',
    'fulltext-atc' => 'value',
    'classtxt' => 'value',
    'class' => array('800.1', 'matematica'),
    'facets-class-desc' => 'value',
    'facets-editore' => 'value',
    'publisher' => 'value',
    'aut' => 'value',
    'materiale' => array('(bibtypefirst = 1, bibtype = a01)', '(bibtypefirst = 1)'),
    'facets-materiale' => array('(bibtypefirst = 1, bibtype = a01)', '(bibtypefirst = 1)'),
    'facets-biblevel-full' => 'value',
    'facets-biblevel-full' => 'value',
    'facets-subject' => 'value',
    'facets-lang' => 'value',
    'facets-place' => 'value',
    'facets-country' => 'value',
    'facets-owner' => 'value',
    'facets-printer' => 'value',
    'facets-author' => 'value',
    'facets-author-main' => 'value',
    'facets-target' => 'value',
    'id-subj' => 'value',
    'id-work' => 'value',
    'id-marca' => 'value',
    'biblevel' => 'value',
    'bibtype' => 'value',
    'target' => 'value',
    'pub-name' => 'value',
    'pub-place' => 'value',
    'collocation' => 'value',
    'language' => 'value',
    'autha' => 'value',
    'owner' => 'value',
    'printer' => 'value',
    'place' => 'value',
    'year' => '(from = 2000, to = 2010)',
    'segnatura' => 'value',
    'tid' => 'value',
    'q' => 'value',
    'libarea' => array('1', 'nonexisting-area'),
    'collection' => 'value',
    'loanable' => array('0', '1', '2'),
    'ean' => 'value',
    'num-ean' => 'value',
    'num-isbn' => 'value',
    'num-issn' => 'value',
    'num-fingerprint' => 'value',
    'num-ismn' => 'value',
    'num-article' => 'value',
    'num-isrn' => 'value',
    'num-isrc' => 'value',
    'num-other' => 'value',
    'num-natbib' => 'value',
    'num-depleg' => 'value',
    'num-gov' => 'value',
    'num-coden' => 'value',
    'num-upc' => 'value',
    'standard-number' => array('(subfield = ISBN, value = 123456)', '123456'),
    'fldin_str_bid' => 'value',
    'solr' => 'value',
);

ksort($examples);
?>
<html>
<head>
    <title>ClavisLanguage Playground</title>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="//code.jquery.com/jquery-2.0.3.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">

    <div class="row">
        <div class="col-lg-12">
            <h1>DSQ Language Playground</h1>
            <form role="form" method="get">
                <div class="form-group">
                    <label for="query">ClavisQuery</label>
                    <input type="text" class="form-control input-lg" name="query" placeholder="title = promessi AND autha = manzoni">
                </div>
                <div class="form-group">
                    <label for="dng">DNG Url</label>
                    <input type="text" class="form-control" name="dng" placeholder="http://your-dng-url.com" value="http://opac.provincia.brescia.it">
                </div>
                <input type = "hidden" name="action" value="dng">
                <button type="submit" class="btn btn-default" value="dng">View in DNG</button>
            </form>

            <b>Compiled Lucene query: </b>
                    <pre id="compiled-query">Write a ClavisQuery and see the live result here</pre>
        </div>
    </div>
    <div class="row"><div class="col-lg-12">
        <p>
            <h4>Some examples:</h4>
            <i>Simple equalities with boolean operators: </i><code>autha = manzoni AND (title = (promessi sposi) OR title = "cinque maggio")</code><br>
            <i>The same with IN operator: </i><code>autha = manzoni AND title IN ((promessi sposi), "cinque maggio")</code><br>
            <i>Not equal operator: </i><code>autha != manzoni</code><br>
            <i>Nested Values: </i><code>subj-and-type = (t = G, s = "Religioni")</code><br>
            <i>Comparison operators: </i><code>year >= 2010</code><br>
            <i>Negations: </i><code>autha = manzoni AND NOT title = promessi</code>

        </p>
        <h4>Fields configuration:</h4>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Field</th>
                <th>Examples</th>
                <th>Solr</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($examples as $fieldname => $fieldvalues): ?>
                <tr>
                <td><b><?php echo $fieldname; ?></td>
                    <td>
                        <?php foreach ((array) $fieldvalues as $value): ?>
                        <code><?php echo $fieldname, ' = ', $value; ?></code><br>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php foreach ((array) $fieldvalues as $value): ?>
                        <code class="text-info"><?php echo substr(compile($fieldname .  ' = ' . $value), 1, -1); ?></code><br>
                        <?php endforeach; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody></table>

    </div></div>
</div>
<script>
    (function ($) {
        $(document).ready(function() {
           $('[name="query"]').on('input', function() {
                $('#compiled-query').show().load(
                    '',
                    {"action": "compile", "query": $(this).val()}
                );
           });
        });
    })(jQuery);
</script>
</body>
</html>
