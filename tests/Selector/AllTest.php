<?php

/*
 * This file is part of the webmozart/expression package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Expression\Tests\Selector;

use ArrayIterator;
use PHPUnit_Framework_TestCase;
use Webmozart\Expression\Constraint\EndsWith;
use Webmozart\Expression\Constraint\GreaterThan;
use Webmozart\Expression\Logic\AndX;
use Webmozart\Expression\Selector\All;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class AllTest extends PHPUnit_Framework_TestCase
{
    public function testEvaluate()
    {
        $all = new All(new GreaterThan(10));

        $this->assertTrue($all->evaluate([11, 12, 13]));
        $this->assertTrue($all->evaluate([11, 12]));
        $this->assertFalse($all->evaluate([10, 11, 12]));
        $this->assertFalse($all->evaluate([9, 10, 11, 12]));
        $this->assertTrue($all->evaluate(new ArrayIterator([11, 12, 13])));
        $this->assertTrue($all->evaluate(new ArrayIterator([11, 12])));
        $this->assertFalse($all->evaluate(new ArrayIterator([10, 11, 12])));
        $this->assertFalse($all->evaluate(new ArrayIterator([9, 10, 11, 12])));

        $this->assertTrue($all->evaluate([]));
        $this->assertFalse($all->evaluate('foobar'));
    }

    public function testToString()
    {
        $expr1 = new All(new GreaterThan(10));
        $expr2 = new All(new EndsWith('.css'));
        $expr3 = new All(new AndX([
            new GreaterThan(10),
            new EndsWith('.css'),
        ]));

        $this->assertSame('all(>10)', $expr1->toString());
        $this->assertSame('all(endsWith(".css"))', $expr2->toString());
        $this->assertSame('all(>10 && endsWith(".css"))', $expr3->toString());
    }
}
