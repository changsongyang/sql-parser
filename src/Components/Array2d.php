<?php

/**
 * `VALUES` keyword parser.
 *
 * @package    SqlParser
 * @subpackage Components
 */
namespace SqlParser\Components;

use SqlParser\Component;
use SqlParser\Parser;
use SqlParser\Token;
use SqlParser\TokensList;

/**
 * `VALUES` keyword parser.
 *
 * @category   Keywords
 * @package    SqlParser
 * @subpackage Components
 * @author     Dan Ungureanu <udan1107@gmail.com>
 * @license    http://opensource.org/licenses/GPL-2.0 GNU Public License
 */
class Array2d extends Component
{

    /**
     * @param Parser     $parser  The parser that serves as context.
     * @param TokensList $list    The list of tokens that are being parsed.
     * @param array      $options Parameters for parsing.
     *
     * @return ArrayObj[]
     */
    public static function parse(Parser $parser, TokensList $list, array $options = array())
    {
        $ret = array();

        /**
         * Whether an array was parsed or not. To be a valid parsing, at least
         * one array must be parsed after each comma.
         * @var bool $parsed
         */
        $parsed = false;

        /**
         * The number of values in each set.
         * @var int
         */
        $count = -1;

        /**
         * The state of the parser.
         *
         * Below are the states of the parser.
         *
         *      0 ----------------------[ array ]---------------------> 1
         *
         *      1 ------------------------[ , ]------------------------> 0
         *      1 -----------------------[ else ]----------------------> -1
         *
         * @var int
         */
        $state = 0;

        for (; $list->idx < $list->count; ++$list->idx) {
            /**
             * Token parsed at this moment.
             * @var Token $token
             */
            $token = $list->tokens[$list->idx];

            // End of statement.
            if ($token->type === Token::TYPE_DELIMITER) {
                break;
            }

            // Skipping whitespaces and comments.
            if (($token->type === Token::TYPE_WHITESPACE) || ($token->type === Token::TYPE_COMMENT)) {
                continue;
            }

            // No keyword is expected.
            if (($token->type === Token::TYPE_KEYWORD) && ($token->flags & Token::FLAG_KEYWORD_RESERVED)) {
                break;
            }

            if ($state === 0) {
                if ($token->value === '(') {
                    $arr = ArrayObj::parse($parser, $list, $options);
                    $arrCount = count($arr->values);
                    if ($count === -1) {
                        $count = $arrCount;
                    } elseif ($arrCount != $count) {
                        $parser->error("{$count} values were expected, but found {$arrCount}.", $token);
                    }
                    $ret[] = $arr;
                    $parsed = true;
                    $state = 1;
                } else {
                    break;
                }
            } elseif ($state === 1) {
                if ($token->value === ',') {
                    $parsed = false;
                    $state = 0;
                } else {
                    break;
                }
            }
        }

        if (!$parsed) {
            $parser->error(
                'An opening bracket followed by a set of values was expected.',
                $list->tokens[$list->idx]
            );
        }

        --$list->idx;
        return $ret;
    }
}