<?php

/*
 * This file is part of the webmozart/expression package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Expression\Tests;

use ArrayObject;
use PHPUnit_Framework_TestCase;
use Webmozart\Expression\Constraint\Contains;
use Webmozart\Expression\Constraint\EndsWith;
use Webmozart\Expression\Constraint\Equals;
use Webmozart\Expression\Constraint\GreaterThan;
use Webmozart\Expression\Constraint\GreaterThanEqual;
use Webmozart\Expression\Constraint\In;
use Webmozart\Expression\Constraint\IsEmpty;
use Webmozart\Expression\Constraint\IsInstanceOf;
use Webmozart\Expression\Constraint\KeyExists;
use Webmozart\Expression\Constraint\KeyNotExists;
use Webmozart\Expression\Constraint\LessThan;
use Webmozart\Expression\Constraint\LessThanEqual;
use Webmozart\Expression\Constraint\Matches;
use Webmozart\Expression\Constraint\NotEquals;
use Webmozart\Expression\Constraint\NotSame;
use Webmozart\Expression\Constraint\Same;
use Webmozart\Expression\Constraint\StartsWith;
use Webmozart\Expression\Expr;
use Webmozart\Expression\Logic\AlwaysFalse;
use Webmozart\Expression\Logic\AlwaysTrue;
use Webmozart\Expression\Logic\AndX;
use Webmozart\Expression\Logic\Not;
use Webmozart\Expression\Logic\OrX;
use Webmozart\Expression\Selector\All;
use Webmozart\Expression\Selector\AtLeast;
use Webmozart\Expression\Selector\AtMost;
use Webmozart\Expression\Selector\Count;
use Webmozart\Expression\Selector\Exactly;
use Webmozart\Expression\Selector\Key;
use Webmozart\Expression\Selector\Method;
use Webmozart\Expression\Selector\Property;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ExprTest extends PHPUnit_Framework_TestCase
{
    public static function getComparisons()
    {
        return [
            [
                'keyExists',
                ['key'],
                new KeyExists('key'),
            ],
            [
                'keyNotExists',
                ['key'],
                new KeyNotExists('key'),
            ],
            [
                'null',
                [],
                new Same(null),
            ],
            [
                'notNull',
                [],
                new NotSame(null),
            ],
            [
                'isEmpty',
                [],
                new IsEmpty(),
            ],
            [
                'isInstanceOf',
                ['DateTime'],
                new IsInstanceOf('DateTime'),
            ],
            [
                'notEmpty',
                [],
                new Not(new IsEmpty()),
            ],
            [
                'equals',
                [10],
                new Equals(10),
            ],
            [
                'notEquals',
                [10],
                new NotEquals(10),
            ],
            [
                'same',
                [10],
                new Same(10),
            ],
            [
                'notSame',
                [10],
                new NotSame(10),
            ],
            [
                'greaterThan',
                [10],
                new GreaterThan(10),
            ],
            [
                'greaterThanEqual',
                [10],
                new GreaterThanEqual(10),
            ],
            [
                'lessThan',
                [10],
                new LessThan(10),
            ],
            [
                'lessThanEqual',
                [10],
                new LessThanEqual(10),
            ],
            [
                'matches',
                ['~^\d{4}$~'],
                new Matches('~^\d{4}$~'),
            ],
            [
                'startsWith',
                ['Thomas'],
                new StartsWith('Thomas'),
            ],
            [
                'endsWith',
                ['.css'],
                new EndsWith('.css'),
            ],
            [
                'contains',
                ['css'],
                new Contains('css'),
            ],
            [
                'in',
                [['1', '2', '3']],
                new In(['1', '2', '3']),
            ],
        ];
    }

    public static function getMethodTests()
    {
        $expr = new Same('10');

        return array_merge([
            [
                'not',
                [$expr],
                new Not($expr),
            ],
            [
                'key',
                ['key', $expr],
                new Key('key', $expr),
            ],
            [
                'method',
                ['getFoo', $expr],
                new Method('getFoo', [], $expr),
            ],
            [
                'method',
                ['getFoo', 42, 'bar', $expr],
                new Method('getFoo', [42, 'bar'], $expr),
            ],
            [
                'property',
                ['prop', $expr],
                new Property('prop', $expr),
            ],
            [
                'atLeast',
                [2, $expr],
                new AtLeast(2, $expr),
            ],
            [
                'atMost',
                [2, $expr],
                new AtMost(2, $expr),
            ],
            [
                'exactly',
                [2, $expr],
                new Exactly(2, $expr),
            ],
            [
                'all',
                [$expr],
                new All($expr),
            ],
            [
                'count',
                [$expr],
                new Count($expr),
            ],
            [
                'true',
                [],
                new AlwaysTrue(),
            ],
            [
                'false',
                [],
                new AlwaysFalse(),
            ],
        ], self::getComparisons());
    }

    /**
     * @dataProvider getMethodTests
     */
    public function testCreate($method, $args, $expected)
    {
        $this->assertEquals($expected, call_user_func_array(['Webmozart\Expression\Expr', $method], $args));
    }

    public function testExpr()
    {
        $expr = new Same(true);

        $this->assertSame($expr, Expr::expr($expr));
    }

    public function testAndX()
    {
        $andX = new AndX([new GreaterThan(5), new LessThan(10)]);

        $this->assertEquals($andX, Expr::andX([Expr::greaterThan(5), Expr::lessThan(10)]));
    }

    public function testOrX()
    {
        $andX = new OrX([new LessThan(5), new GreaterThan(10)]);

        $this->assertEquals($andX, Expr::orX([Expr::lessThan(5), Expr::greaterThan(10)]));
    }

    public function testFilterArray()
    {
        $input = range(1, 10);
        $output = array_filter($input, function ($i) {
            return $i > 4;
        });

        $this->assertSame($output, Expr::filter($input, Expr::greaterThan(4)));
    }

    public function testFilterCollection()
    {
        $input = new ArrayObject(range(1, 10));
        $output = new ArrayObject(array_filter(range(1, 10), function ($i) {
            return $i > 4;
        }));

        $this->assertEquals($output, Expr::filter($input, Expr::greaterThan(4)));
    }
}
