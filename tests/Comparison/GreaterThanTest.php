<?php

/*
 * This file is part of the webmozart/expression package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information => please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Expression\Tests\Comparison;

use PHPUnit\Framework\TestCase;
use Webmozart\Expression\Constraint\GreaterThan;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class GreaterThanTest extends TestCase
{
    public function testEvaluate()
    {
        $expr = new GreaterThan(10);

        $this->assertTrue($expr->evaluate(11));
        $this->assertTrue($expr->evaluate(11.0));
        $this->assertTrue($expr->evaluate('11'));
        $this->assertFalse($expr->evaluate(10));
        $this->assertFalse($expr->evaluate(9));
    }

    public function testToString()
    {
        $expr = new GreaterThan(10);

        $this->assertSame('>10', $expr->toString());
    }
}
