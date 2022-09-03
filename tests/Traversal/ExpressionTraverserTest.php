<?php

/*
 * This file is part of the webmozart/expression package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Expression\Tests\Traversal;

use PHPUnit\Framework\TestCase;
use Webmozart\Expression\Constraint\GreaterThan;
use Webmozart\Expression\Constraint\Same;
use Webmozart\Expression\Logic\AndX;
use Webmozart\Expression\Logic\Not;
use Webmozart\Expression\Logic\OrX;
use Webmozart\Expression\Selector\Key;
use Webmozart\Expression\Traversal\ExpressionTraverser;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ExpressionTraverserTest extends TestCase
{
    /**
     * @var ExpressionTraverser
     */
    private $traverser;

    protected function setUp(): void
    {
        $this->traverser = new ExpressionTraverser();
    }

    public function testAddVisitor()
    {
        $visitor1 = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');
        $visitor2 = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $this->traverser->addVisitor($visitor1);
        $this->traverser->addVisitor($visitor2);
        $this->traverser->addVisitor($visitor1);

        $this->assertSame([$visitor1, $visitor2, $visitor1], $this->traverser->getVisitors());
    }

    public function testRemoveVisitor()
    {
        $visitor1 = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');
        $visitor2 = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $this->traverser->addVisitor($visitor1);
        $this->traverser->addVisitor($visitor2);
        $this->traverser->addVisitor($visitor1);
        $this->traverser->removeVisitor($visitor1);

        $this->assertSame([$visitor2], $this->traverser->getVisitors());
    }

    public function testTraverse()
    {
        $expr = new GreaterThan(10);

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');
        $visitor
            ->method('enterExpression')
            ->with($this->identicalTo($expr))
            ->willReturn($expr);
        $visitor
            ->method('leaveExpression')
            ->with($this->identicalTo($expr))
            ->willReturn($expr);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr, $this->traverser->traverse($expr));
    }

    public function testModifyExprInEnterExpression()
    {
        $expr1 = new GreaterThan(10);
        $expr2 = new GreaterThan(5);

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');
        $visitor
            ->method('enterExpression')
            ->with($this->identicalTo($expr1))
            ->willReturn($expr2);
        $visitor
            ->method('leaveExpression')
            ->with($this->identicalTo($expr2))
            ->willReturn($expr2);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr2, $this->traverser->traverse($expr1));
    }

    public function testModifyExprInLeaveExpression()
    {
        $expr1 = new GreaterThan(10);
        $expr2 = new GreaterThan(5);

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');
        $visitor
            ->method('enterExpression')
            ->with($this->identicalTo($expr1))
            ->willReturn($expr1);
        $visitor
            ->method('leaveExpression')
            ->with($this->identicalTo($expr1))
            ->willReturn($expr2);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr2, $this->traverser->traverse($expr1));
    }

    public function testRemoveExpr()
    {
        $expr = new GreaterThan(10);

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');
        $visitor
            ->method('enterExpression')
            ->with($this->identicalTo($expr))
            ->willReturn($expr);
        $visitor
            ->method('leaveExpression')
            ->with($this->identicalTo($expr))
            ->willReturn(null);

        $this->traverser->addVisitor($visitor);

        $this->assertNull($this->traverser->traverse($expr));
    }

    public function testTraverseMultipleVisitors()
    {
        $expr1 = new GreaterThan(10);
        $expr2 = new GreaterThan(5);

        $visitor1 = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');
        $visitor1
            ->method('enterExpression')
            ->with($this->identicalTo($expr1))
            ->willReturn($expr1);
        $visitor1
            ->method('leaveExpression')
            ->with($this->identicalTo($expr1))
            ->willReturn($expr2);

        $visitor2 = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');
        $visitor2
            ->method('enterExpression')
            ->with($this->identicalTo($expr2))
            ->willReturn($expr2);
        $visitor2
            ->method('leaveExpression')
            ->with($this->identicalTo($expr2))
            ->willReturn($expr2);

        $this->traverser->addVisitor($visitor1);
        $this->traverser->addVisitor($visitor2);

        $this->assertSame($expr2, $this->traverser->traverse($expr1));
    }

    public function testTraverseSkipsSubsequentVisitorsIfExpressionRemoved()
    {
        $expr = new GreaterThan(10);

        $visitor1 = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor1->expects($this->exactly(1))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr)]
            )
            ->willReturnOnConsecutiveCalls($expr);

        $visitor1->expects($this->exactly(1))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($expr)]
            )
            ->willReturnOnConsecutiveCalls(null);

        $visitor2 = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');
        $visitor2->expects($this->never())
            ->method('enterExpression');
        $visitor2->expects($this->never())
            ->method('leaveExpression');

        $this->traverser->addVisitor($visitor1);
        $this->traverser->addVisitor($visitor2);

        $this->assertNull($this->traverser->traverse($expr));
    }

    public function testTraverseNot()
    {
        $expr = new Not($gt = new GreaterThan(10));

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(2))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr)],
                [$this->identicalTo($gt)]
            )
            ->willReturnOnConsecutiveCalls($expr, $gt);

        $visitor->expects($this->exactly(2))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt)],
                [$this->identicalTo($expr)]
            )
            ->willReturnOnConsecutiveCalls($gt, $expr);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr, $this->traverser->traverse($expr));
    }

    public function testModifyNotChildInEnterExpression()
    {
        $expr1 = new Not($gt1 = new GreaterThan(10));
        $expr2 = new Not($gt2 = new GreaterThan(5));

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(2))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr1)],
                [$this->identicalTo($gt1)]
            )
            ->willReturnOnConsecutiveCalls($expr1, $gt2);

        $visitor->expects($this->exactly(2))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt2)],
                [$this->equalTo($expr2)]
            )
            ->willReturnOnConsecutiveCalls($gt2, $expr2);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr2, $this->traverser->traverse($expr1));
    }

    public function testModifyNotChildInLeaveExpression()
    {
        $expr1 = new Not($gt1 = new GreaterThan(10));
        $expr2 = new Not($gt2 = new GreaterThan(5));

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(2))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr1)],
                [$this->identicalTo($gt1)]
            )
            ->willReturnOnConsecutiveCalls($expr1, $gt1);

        $visitor->expects($this->exactly(2))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt1)],
                [$this->equalTo($expr2)]
            )
            ->willReturnOnConsecutiveCalls($gt2, $expr2);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr2, $this->traverser->traverse($expr1));
    }

    public function testRemoveNotChild()
    {
        $expr1 = new Not($gt1 = new GreaterThan(10));

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(2))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr1)],
                [$this->identicalTo($gt1)]
            )
            ->willReturnOnConsecutiveCalls($expr1, $gt1);

        $visitor->expects($this->exactly(1))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt1)]
            )
            ->willReturnOnConsecutiveCalls(null);

        $this->traverser->addVisitor($visitor);

        $this->assertNull($this->traverser->traverse($expr1));
    }

    public function testTraverseKey()
    {
        $expr = new Key('key', $gt = new GreaterThan(10));

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(2))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr)],
                [$this->identicalTo($gt)]
            )
            ->willReturnOnConsecutiveCalls($expr, $gt);

        $visitor->expects($this->exactly(2))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt)],
                [$this->identicalTo($expr)]
            )
            ->willReturnOnConsecutiveCalls($gt, $expr);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr, $this->traverser->traverse($expr));
    }

    public function testModifyKeyChildInEnterExpression()
    {
        $expr1 = new Key('key', $gt1 = new GreaterThan(10));
        $expr2 = new Key('key', $gt2 = new GreaterThan(5));

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(2))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr1)],
                [$this->identicalTo($gt1)]
            )
            ->willReturnOnConsecutiveCalls($expr1, $gt2);

        $visitor->expects($this->exactly(2))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt2)],
                [$this->equalTo($expr2)]
            )
            ->willReturnOnConsecutiveCalls($gt2, $expr2);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr2, $this->traverser->traverse($expr1));
    }

    public function testModifyKeyChildInLeaveExpression()
    {
        $expr1 = new Key('key', $gt1 = new GreaterThan(10));
        $expr2 = new Key('key', $gt2 = new GreaterThan(5));

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(2))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr1)],
                [$this->identicalTo($gt1)]
            )
            ->willReturnOnConsecutiveCalls($expr1, $gt1);

        $visitor->expects($this->exactly(2))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt1)],
                [$this->equalTo($expr2)]
            )
            ->willReturnOnConsecutiveCalls($gt2, $expr2);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr2, $this->traverser->traverse($expr1));
    }

    public function testRemoveKeyChild()
    {
        $expr1 = new Key('key', $gt1 = new GreaterThan(10));

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(2))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr1)],
                [$this->identicalTo($gt1)]
            )
            ->willReturnOnConsecutiveCalls($expr1, $gt1);

        $visitor->expects($this->exactly(1))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt1)]
            )
            ->willReturnOnConsecutiveCalls(null);

        $this->traverser->addVisitor($visitor);

        $this->assertNull($this->traverser->traverse($expr1));
    }

    public function testTraverseConjunction()
    {
        $expr = new AndX([
            $gt = new GreaterThan(10),
            $same = new Same('5'),
        ]);

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(3))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr)],
                [$this->identicalTo($gt)],
                [$this->identicalTo($same)]
            )
            ->willReturnOnConsecutiveCalls($expr, $gt, $same);

        $visitor->expects($this->exactly(3))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt)],
                [$this->identicalTo($same)],
                [$this->equalTo($expr)]
            )
            ->willReturnOnConsecutiveCalls($gt, $same, $expr);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr, $this->traverser->traverse($expr));
    }

    public function testModifyConjunctInEnterExpression()
    {
        $expr1 = new AndX([
            $gt1 = new GreaterThan(10),
            $same = new Same('5'),
        ]);
        $expr2 = new AndX([
            $gt2 = new GreaterThan(5),
            $same,
        ]);

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(3))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr1)],
                [$this->identicalTo($gt1)],
                [$this->identicalTo($same)]
            )
            ->willReturnOnConsecutiveCalls($expr1, $gt2, $same);

        $visitor->expects($this->exactly(3))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt2)],
                [$this->identicalTo($same)],
                [$this->equalTo($expr2)]
            )
            ->willReturnOnConsecutiveCalls($gt2, $same, $expr2);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr2, $this->traverser->traverse($expr1));
    }

    public function testModifyConjunctInLeaveExpression()
    {
        $expr1 = new AndX([
            $gt1 = new GreaterThan(10),
            $same = new Same('5'),
        ]);
        $expr2 = new AndX([
            $gt2 = new GreaterThan(5),
            $same,
        ]);

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(3))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr1)],
                [$this->identicalTo($gt1)],
                [$this->identicalTo($same)]
            )
            ->willReturnOnConsecutiveCalls($expr1, $gt1, $same);

        $visitor->expects($this->exactly(3))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt1)],
                [$this->identicalTo($same)],
                [$this->equalTo($expr2)]
            )
            ->willReturnOnConsecutiveCalls($gt2, $same, $expr2);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr2, $this->traverser->traverse($expr1));
    }

    public function testRemoveConjunct()
    {
        $expr1 = new AndX([
            $gt1 = new GreaterThan(10),
            $same = new Same('5'),
        ]);
        $expr2 = new AndX([$same]);

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(3))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr1)],
                [$this->identicalTo($gt1)],
                [$this->identicalTo($same)]
            )
            ->willReturnOnConsecutiveCalls($expr1, $gt1, $same);

        $visitor->expects($this->exactly(3))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt1)],
                [$this->identicalTo($same)],
                [$this->equalTo($expr2)]
            )
            ->willReturnOnConsecutiveCalls(null, $same, $expr2);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr2, $this->traverser->traverse($expr1));
    }

    public function testRemoveAllConjuncts()
    {
        $expr1 = new AndX([
            $gt1 = new GreaterThan(10),
            $same = new Same('5'),
        ]);

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(3))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr1)],
                [$this->identicalTo($gt1)],
                [$this->identicalTo($same)]
            )
            ->willReturnOnConsecutiveCalls($expr1, $gt1, $same);

        $visitor->expects($this->exactly(2))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt1)],
                [$this->identicalTo($same)]
            )
            ->willReturnOnConsecutiveCalls(null, null);

        $this->traverser->addVisitor($visitor);

        $this->assertNull($this->traverser->traverse($expr1));
    }

    public function testTraverseDisjunction()
    {
        $expr = new OrX([
            $gt = new GreaterThan(10),
            $same = new Same('5'),
        ]);

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(3))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr)],
                [$this->identicalTo($gt)],
                [$this->identicalTo($same)]
            )
            ->willReturnOnConsecutiveCalls($expr, $gt, $same);

        $visitor->expects($this->exactly(3))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt)],
                [$this->identicalTo($same)],
                [$this->equalTo($expr)]
            )
            ->willReturnOnConsecutiveCalls($gt, $same, $expr);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr, $this->traverser->traverse($expr));
    }

    public function testModifyDisjunctInEnterExpression()
    {
        $expr1 = new OrX([
            $gt1 = new GreaterThan(10),
            $same = new Same('5'),
        ]);
        $expr2 = new OrX([
            $gt2 = new GreaterThan(5),
            $same,
        ]);

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(3))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr1)],
                [$this->identicalTo($gt1)],
                [$this->identicalTo($same)]
            )
            ->willReturnOnConsecutiveCalls($expr1, $gt2, $same);

        $visitor->expects($this->exactly(3))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt2)],
                [$this->identicalTo($same)],
                [$this->equalTo($expr2)]
            )
            ->willReturnOnConsecutiveCalls($gt2, $same, $expr2);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr2, $this->traverser->traverse($expr1));
    }

    public function testModifyDisjunctInLeaveExpression()
    {
        $expr1 = new OrX([
            $gt1 = new GreaterThan(10),
            $same = new Same('5'),
        ]);
        $expr2 = new OrX([
            $gt2 = new GreaterThan(5),
            $same,
        ]);

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(3))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr1)],
                [$this->identicalTo($gt1)],
                [$this->identicalTo($same)]
            )
            ->willReturnOnConsecutiveCalls($expr1, $gt1, $same);

        $visitor->expects($this->exactly(3))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt1)],
                [$this->identicalTo($same)],
                [$this->equalTo($expr2)]
            )
            ->willReturnOnConsecutiveCalls($gt2, $same, $expr2);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr2, $this->traverser->traverse($expr1));
    }

    public function testRemoveDisjunct()
    {
        $expr1 = new OrX([
            $gt1 = new GreaterThan(10),
            $same = new Same('5'),
        ]);
        $expr2 = new OrX([$same]);

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(3))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr1)],
                [$this->identicalTo($gt1)],
                [$this->identicalTo($same)]
            )
            ->willReturnOnConsecutiveCalls($expr1, $gt1, $same);

        $visitor->expects($this->exactly(3))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt1)],
                [$this->identicalTo($same)],
                [$this->equalTo($expr2)]
            )
            ->willReturnOnConsecutiveCalls(null, $same, $expr2);

        $this->traverser->addVisitor($visitor);

        $this->assertSame($expr2, $this->traverser->traverse($expr1));
    }

    public function testRemoveAllDisjuncts()
    {
        $expr1 = new OrX([
            $gt1 = new GreaterThan(10),
            $same = new Same('5'),
        ]);

        $visitor = $this->createMock('Webmozart\Expression\Traversal\ExpressionVisitor');

        $visitor->expects($this->exactly(3))
            ->method('enterExpression')
            ->withConsecutive(
                [$this->identicalTo($expr1)],
                [$this->identicalTo($gt1)],
                [$this->identicalTo($same)]
            )
            ->willReturnOnConsecutiveCalls($expr1, $gt1, $same);

        $visitor->expects($this->exactly(2))
            ->method('leaveExpression')
            ->withConsecutive(
                [$this->identicalTo($gt1)],
                [$this->identicalTo($same)]
            )
            ->willReturnOnConsecutiveCalls(null, null);

        $this->traverser->addVisitor($visitor);

        $this->assertNull($this->traverser->traverse($expr1));
    }
}
