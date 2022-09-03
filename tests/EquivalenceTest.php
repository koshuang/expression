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
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic\AlwaysFalse;
use Webmozart\Expression\Logic\AlwaysTrue;
use Webmozart\Expression\Logic\Not;
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
class EquivalenceTest extends PHPUnit_Framework_TestCase
{
    public function getEquivalentCriteria()
    {
        return [
            [new Same('10'), new Same('10')],
            [new Same('10'), new In(['10'], true)],

            [new NotSame('10'), new NotSame('10')],

            [new Equals('10'), new Equals('10')],
            [new Equals('10'), new Equals(10)],
            [new Equals('10'), new In(['10'], false)],
            [new Equals('10'), new In([10], false)],

            [new NotEquals('10'), new NotEquals('10')],
            [new NotEquals('10'), new NotEquals(10)],

            [new GreaterThan('10'), new GreaterThan('10')],
            [new GreaterThan('10'), new GreaterThan(10)],

            [new GreaterThanEqual('10'), new GreaterThanEqual('10')],
            [new GreaterThanEqual('10'), new GreaterThanEqual(10)],

            [new LessThan('10'), new LessThan('10')],
            [new LessThan('10'), new LessThan(10)],

            [new LessThanEqual('10'), new LessThanEqual('10')],
            [new LessThanEqual('10'), new LessThanEqual(10)],

            [new IsEmpty(), new IsEmpty()],

            [new IsInstanceOf('SplFileInfo'), new IsInstanceOf('SplFileInfo')],

            [new KeyExists('10'), new KeyExists('10')],
            [new KeyExists('10'), new KeyExists(10)],

            [new KeyNotExists('10'), new KeyNotExists('10')],
            [new KeyNotExists('10'), new KeyNotExists(10)],

            [new Matches('foo.*'), new Matches('foo.*')],

            [new In(['10'], false), new In(['10'], false)],
            [new In(['10'], false), new In([10], false)],
            [new In(['10'], true), new In(['10'], true)],

            [new StartsWith('10'), new StartsWith('10')],
            [new StartsWith('10'), new StartsWith(10)],

            [new EndsWith('10'), new EndsWith('10')],
            [new EndsWith('10'), new EndsWith(10)],

            [new Contains('10'), new Contains('10')],
            [new Contains('10'), new Contains(10)],

            [new Not(new Same('10')), new Not(new Same('10'))],

            [new Key('key', new Same('10')), new Key('key', new Same('10'))],
            [new Key('42', new Same('10')), new Key(42, new Same('10'))],

            [new Property('prop', new Same('10')), new Property('prop', new Same('10'))],

            [new Method('getFoo', [], new Same('10')), new Method('getFoo', [], new Same('10'))],
            [new Method('getFoo', [42], new Same('10')), new Method('getFoo', [42], new Same('10'))],

            [new AtLeast(1, new Same('10')), new AtLeast(1, new Same('10'))],

            [new AtMost(1, new Same('10')), new AtMost(1, new Same('10'))],

            [new Exactly(1, new Same('10')), new Exactly(1, new Same('10'))],

            [new All(new Same('10')), new All(new Same('10'))],

            [new Count(new Same(10)), new Count(new Same(10))],

            [new AlwaysTrue(), new AlwaysTrue()],

            [new AlwaysFalse(), new AlwaysFalse()],
        ];
    }

    /**
     * @dataProvider getEquivalentCriteria
     */
    public function testEquivalence(Expression $left, Expression $right)
    {
        $this->assertTrue($left->equivalentTo($right));
        $this->assertTrue($right->equivalentTo($left));
    }

    public function getNonEquivalentCriteria()
    {
        return [
            [new Same('10'), new Same('11')],
            [new Same('10'), new Same(10)],
            [new Same('10'), new Equals('10')],

            [new Same('10'), new In([10], true)],
            [new Same('10'), new In(['10'], false)],
            [new Same('10'), new In([], true)],
            [new Same('10'), new In(['10', '11'], true)],

            [new NotSame('10'), new NotSame('11')],
            [new NotSame('10'), new NotSame(10)],
            [new NotSame('10'), new NotEquals('10')],

            [new Equals('10'), new Equals('11')],
            [new Equals('10'), new In(['10'], true)],
            [new Equals('10'), new In([], false)],
            [new Equals('10'), new In(['10', '11'], false)],

            [new GreaterThan('10'), new GreaterThan('11')],
            [new GreaterThan('10'), new LessThan('10')],

            [new GreaterThanEqual('10'), new GreaterThanEqual('11')],
            [new GreaterThanEqual('10'), new LessThan('10')],

            [new LessThan('10'), new LessThan('11')],
            [new LessThan('10'), new GreaterThan('10')],

            [new LessThanEqual('10'), new LessThanEqual('11')],
            [new LessThanEqual('10'), new GreaterThan('10')],

            [new IsInstanceOf('SplFileInfo'), new IsInstanceOf('DateTime')],

            [new KeyExists('10'), new KeyExists('11')],
            [new KeyExists('foo'), new KeyExists(0)],
            [new KeyExists('10'), new Equals('10')],

            [new KeyNotExists('10'), new KeyNotExists('11')],
            [new KeyNotExists('foo'), new KeyNotExists(0)],
            [new KeyNotExists('10'), new Equals('10')],

            [new Matches('10'), new Matches(10)],
            [new Matches('10'), new Equals('10')],

            [new In(['10'], true), new In(['11'], true)],
            [new In(['10'], true), new In([10], true)],
            [new In(['10'], true), new In(['11'], false)],
            [new In(['10'], true), new IsEmpty()],

            [new StartsWith('10'), new StartsWith('11')],
            [new StartsWith('foo'), new StartsWith(0)],
            [new StartsWith('10'), new Equals('10')],

            [new EndsWith('10'), new EndsWith('11')],
            [new EndsWith('foo'), new EndsWith(0)],
            [new EndsWith('10'), new Equals('10')],

            [new Contains('10'), new Contains('11')],
            [new Contains('foo'), new Contains(0)],
            [new Contains('10'), new Equals('10')],

            [new Not(new Same('10')), new Not(new Same(10))],
            [new Not(new Same('10')), new Same(10)],

            [new Key('foo', new Same('10')), new Key('bar', new Same('10'))],
            [new Key('foo', new Same('10')), new Key(0, new Same('10'))],
            [new Key('foo', new Same('10')), new Key('foo', new Same(10))],
            [new Key('foo', new Same('10')), new Same('10')],

            [new Property('foo', new Same('10')), new Property('bar', new Same('10'))],
            [new Property('foo', new Same('10')), new Property('foo', new Same(10))],
            [new Property('foo', new Same('10')), new Same('10')],

            [new Method('getFoo', [42], new Same('10')), new Method('getFoo', ['42'], new Same('10'))],
            [new Method('getFoo', [42], new Same('10')), new Method('getFoo', [42, true], new Same('10'))],
            [new Method('getFoo', [], new Same('10')), new Method('getBar', [], new Same('10'))],
            [new Method('getFoo', [], new Same('10')), new Method('getFoo', [], new Same(10))],
            [new Method('getFoo', [], new Same('10')), new Same('10')],

            [new AtLeast(1, new Same('10')), new AtLeast(2, new Same('10'))],
            [new AtLeast(1, new Same('10')), new AtLeast(1, new Same(10))],
            [new AtLeast(1, new Same('10')), new Same('10')],

            [new AtMost(1, new Same('10')), new AtMost(2, new Same('10'))],
            [new AtMost(1, new Same('10')), new AtMost(1, new Same(10))],
            [new AtMost(1, new Same('10')), new Same('10')],

            [new Exactly(1, new Same('10')), new Exactly(2, new Same('10'))],
            [new Exactly(1, new Same('10')), new Exactly(1, new Same(10))],
            [new Exactly(1, new Same('10')), new Same('10')],

            [new All(new Same('10')), new All(new Same(10))],
            [new All(new Same('10')), new Same('10')],

            [new Count(new Same('10')), new Count(new Same(10))],
            [new Count(new Same('10')), new Same('10')],

            [new AlwaysTrue(), new Same(10)],

            [new AlwaysFalse(), new Same(10)],
        ];
    }

    /**
     * @dataProvider getNonEquivalentCriteria
     */
    public function testNonEquivalence(Expression $left, Expression $right)
    {
        $this->assertFalse($left->equivalentTo($right));
        $this->assertFalse($right->equivalentTo($left));
    }
}
