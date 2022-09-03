<?php

/*
 * This file is part of the webmozart/expression package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Expression\Logic;

use Webmozart\Expression\Expr;
use Webmozart\Expression\Expression;

/**
 * A logical literal.
 *
 * In pure logics, a literal is any part of a formula that does not contain
 * "and" or "or" operators. In this package, the definition of a literal is
 * widened to any logical expression that is *not* a conjunction/disjunction.
 *
 * Examples:
 *
 *  * not endsWith(".css")
 *  * greaterThan(0)
 *  * not (greaterThan(0) and lessThan(120))
 *
 * The following examples are *not* literals:
 *
 *  * greaterThan(0) and lessThan(120)
 *  * in(["A", "B", "C]) or null()
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * {@inheritdoc}
 */
abstract class Literal implements Expression
{
    /**
     * @return Expression
     */
    public function andX(Expression $expr)
    {
        if ($expr instanceof AlwaysTrue) {
            return $this;
        } elseif ($expr instanceof AlwaysFalse) {
            return $expr;
        }

        if ($this->equivalentTo($expr)) {
            return $this;
        }

        return new AndX([$this, $expr]);
    }

    /**
     * @return Expression
     */
    public function andNot(Expression $expr)
    {
        return $this->andX(Expr::not($expr));
    }

    /**
     * @return Expression
     */
    public function andTrue()
    {
        return $this;
    }

    /**
     * @return Expression
     */
    public function andFalse()
    {
        return Expr::false();
    }

    /**
     * @return Expression
     */
    public function andKey(string $keyName, Expression $expr)
    {
        return $this->andX(Expr::key($keyName, $expr));
    }

    /**
     * @return Expression
     */
    public function andMethod(string $methodName, mixed $args)
    {
        return $this->andX(call_user_func_array(['Webmozart\Expression\Expr', 'method'], func_get_args()));
    }

    /**
     * @return Expression
     */
    public function andProperty(string $propertyName, Expression $expr)
    {
        return $this->andX(Expr::property($propertyName, $expr));
    }

    /**
     * @return Expression
     */
    public function andAtLeast(int $count, Expression $expr)
    {
        return $this->andX(Expr::atLeast($count, $expr));
    }

    /**
     * @return Expression
     */
    public function andAtMost(int $count, Expression $expr)
    {
        return $this->andX(Expr::atMost($count, $expr));
    }

    /**
     * @return Expression
     */
    public function andExactly(int $count, Expression $expr)
    {
        return $this->andX(Expr::exactly($count, $expr));
    }

    /**
     * @return Expression
     */
    public function andAll(Expression $expr)
    {
        return $this->andX(Expr::all($expr));
    }

    /**
     * @return Expression
     */
    public function andCount(Expression $expr)
    {
        return $this->andX(Expr::count($expr));
    }

    /**
     * @return Expression
     */
    public function andNull()
    {
        return $this->andX(Expr::null());
    }

    /**
     * @return Expression
     */
    public function andNotNull()
    {
        return $this->andX(Expr::notNull());
    }

    /**
     * @return Expression
     */
    public function andEmpty()
    {
        return $this->andX(Expr::isEmpty());
    }

    /**
     * @return Expression
     */
    public function andNotEmpty()
    {
        return $this->andX(Expr::notEmpty());
    }

    /**
     * @return Expression
     */
    public function andInstanceOf(string $className)
    {
        return $this->andX(Expr::isInstanceOf($className));
    }

    /**
     * @return Expression
     */
    public function andEquals(mixed $value)
    {
        return $this->andX(Expr::equals($value));
    }

    /**
     * @return Expression
     */
    public function andNotEquals(mixed $value)
    {
        return $this->andX(Expr::notEquals($value));
    }

    /**
     * @return Expression
     */
    public function andSame(mixed $value)
    {
        return $this->andX(Expr::same($value));
    }

    /**
     * @return Expression
     */
    public function andNotSame(mixed $value)
    {
        return $this->andX(Expr::notSame($value));
    }

    /**
     * @return Expression
     */
    public function andGreaterThan(mixed $value)
    {
        return $this->andX(Expr::greaterThan($value));
    }

    /**
     * @return Expression
     */
    public function andGreaterThanEqual(mixed $value)
    {
        return $this->andX(Expr::greaterThanEqual($value));
    }

    /**
     * @return Expression
     */
    public function andLessThan(mixed $value)
    {
        return $this->andX(Expr::lessThan($value));
    }

    /**
     * @return Expression
     */
    public function andLessThanEqual(mixed $value)
    {
        return $this->andX(Expr::lessThanEqual($value));
    }

    /**
     * @return Expression
     */
    public function andIn(array $values)
    {
        return $this->andX(Expr::in($values));
    }

    /**
     * @return Expression
     */
    public function andMatches(string $regExp)
    {
        return $this->andX(Expr::matches($regExp));
    }

    /**
     * @return Expression
     */
    public function andStartsWith(string $prefix)
    {
        return $this->andX(Expr::startsWith($prefix));
    }

    /**
     * @return Expression
     */
    public function andEndsWith(string $suffix)
    {
        return $this->andX(Expr::endsWith($suffix));
    }

    /**
     * @return Expression
     */
    public function andContains(string $string)
    {
        return $this->andX(Expr::contains($string));
    }

    /**
     * @return Expression
     */
    public function andKeyExists(string $keyName)
    {
        return $this->andX(Expr::keyExists($keyName));
    }

    /**
     * @return Expression
     */
    public function andKeyNotExists(string $keyName)
    {
        return $this->andX(Expr::keyNotExists($keyName));
    }

    /**
     * @return Expression
     */
    public function orX(Expression $expr)
    {
        if ($expr instanceof AlwaysFalse) {
            return $this;
        } elseif ($expr instanceof AlwaysTrue) {
            return $expr;
        }

        if ($this->equivalentTo($expr)) {
            return $this;
        }

        return new OrX([$this, $expr]);
    }

    /**
     * @return Expression
     */
    public function orNot(Expression $expr)
    {
        return $this->orX(Expr::not($expr));
    }

    /**
     * @return Expression
     */
    public function orTrue()
    {
        return Expr::true();
    }

    /**
     * @return Expression
     */
    public function orFalse()
    {
        return $this;
    }

    /**
     * @return Expression
     */
    public function orKey(string $keyName, Expression $expr)
    {
        return $this->orX(Expr::key($keyName, $expr));
    }

    /**
     * @return Expression
     */
    public function orMethod(string $methodName, mixed $args)
    {
        return $this->orX(call_user_func_array(['Webmozart\Expression\Expr', 'method'], func_get_args()));
    }

    /**
     * @return Expression
     */
    public function orProperty(string $propertyName, Expression $expr)
    {
        return $this->orX(Expr::property($propertyName, $expr));
    }

    /**
     * @return Expression
     */
    public function orAtLeast(int $count, Expression $expr)
    {
        return $this->orX(Expr::atLeast($count, $expr));
    }

    /**
     * @return Expression
     */
    public function orAtMost(int $count, Expression $expr)
    {
        return $this->orX(Expr::atMost($count, $expr));
    }

    /**
     * @return Expression
     */
    public function orExactly(int $count, Expression $expr)
    {
        return $this->orX(Expr::exactly($count, $expr));
    }

    /**
     * @return Expression
     */
    public function orAll(Expression $expr)
    {
        return $this->orX(Expr::all($expr));
    }

    /**
     * @return Expression
     */
    public function orCount(Expression $expr)
    {
        return $this->orX(Expr::count($expr));
    }

    /**
     * @return Expression
     */
    public function orNull()
    {
        return $this->orX(Expr::null());
    }

    /**
     * @return Expression
     */
    public function orNotNull()
    {
        return $this->orX(Expr::notNull());
    }

    /**
     * @return Expression
     */
    public function orEmpty()
    {
        return $this->orX(Expr::isEmpty());
    }

    /**
     * @return Expression
     */
    public function orNotEmpty()
    {
        return $this->orX(Expr::notEmpty());
    }

    /**
     * @return Expression
     */
    public function orInstanceOf(string $className)
    {
        return $this->orX(Expr::isInstanceOf($className));
    }

    /**
     * @return Expression
     */
    public function orEquals(mixed $value)
    {
        return $this->orX(Expr::equals($value));
    }

    /**
     * @return Expression
     */
    public function orNotEquals(mixed $value)
    {
        return $this->orX(Expr::notEquals($value));
    }

    /**
     * @return Expression
     */
    public function orSame(mixed $value)
    {
        return $this->orX(Expr::same($value));
    }

    /**
     * @return Expression
     */
    public function orNotSame(mixed $value)
    {
        return $this->orX(Expr::notSame($value));
    }

    /**
     * @return Expression
     */
    public function orGreaterThan(mixed $value)
    {
        return $this->orX(Expr::greaterThan($value));
    }

    /**
     * @return Expression
     */
    public function orGreaterThanEqual(mixed $value)
    {
        return $this->orX(Expr::greaterThanEqual($value));
    }

    /**
     * @return Expression
     */
    public function orLessThan(mixed $value)
    {
        return $this->orX(Expr::lessThan($value));
    }

    /**
     * @return Expression
     */
    public function orLessThanEqual(mixed $value)
    {
        return $this->orX(Expr::lessThanEqual($value));
    }

    /**
     * @return Expression
     */
    public function orIn(array $values)
    {
        return $this->orX(Expr::in($values));
    }

    /**
     * @return Expression
     */
    public function orMatches(string $regExp)
    {
        return $this->orX(Expr::matches($regExp));
    }

    /**
     * @return Expression
     */
    public function orStartsWith(string $prefix)
    {
        return $this->orX(Expr::startsWith($prefix));
    }

    /**
     * @return Expression
     */
    public function orEndsWith(string $suffix)
    {
        return $this->orX(Expr::endsWith($suffix));
    }

    /**
     * @return Expression
     */
    public function orContains(string $string)
    {
        return $this->orX(Expr::contains($string));
    }

    /**
     * @return Expression
     */
    public function orKeyExists(string $keyName)
    {
        return $this->orX(Expr::keyExists($keyName));
    }

    /**
     * @return Expression
     */
    public function orKeyNotExists(string $keyName)
    {
        return $this->orX(Expr::keyNotExists($keyName));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
