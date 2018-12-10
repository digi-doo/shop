<?php

/*
 * This file is part of the Digi Doo s.r.o. sshop project.
 *
 * (c) Digi Doo s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\Helpers\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * "MATCH_AGAINST" "(" {StateFieldPathExpression ","}* Literal ")"
 */
class MatchAgainst extends FunctionNode
{
    /**
     * @var array
     */
    public $columns = [];

    /**
     * @var string
     */
    public $needle;

    /**
     * @param  Parser $parser
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        do {
            $this->columns[] = $parser->StateFieldPathExpression();
            $parser->match(Lexer::T_COMMA);
        } while (!$parser->getLexer()->isNextToken(Lexer::T_INPUT_PARAMETER));

        // Got an input parameter
        $this->needle = $parser->InputParameter();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * @param  SqlWalker $sqlWalker
     *
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $haystack = null;
        $first = true;
        foreach ($this->columns as $column) {
            $first ? $first = false : $haystack .= ', ';
            $haystack .= $column->dispatch($sqlWalker);
        }

        return 'MATCH(' .
            $haystack .
            ') AGAINST (' .
            $this->needle->dispatch($sqlWalker) .
            ' IN BOOLEAN MODE )';
        // " IN NATURAL LANGUAGE MODE )";
    }
}
