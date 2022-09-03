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
 * A disjunction of expressions.
 *
 * A disjunction is a set of {@link Expression} instances connected by logical
 * "or" operators.
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * {@inheritdoc}
 */
class OrX implements Expression
{
    /**
     * @var Expression[]
     */
    private $disjuncts = [];

    /**
     * Creates a disjunction of the given expressions.
     *
     * @param Expression[] $disjuncts The disjuncts.
     */
    public function __construct(array $disjuncts = [])
    {
        foreach ($disjuncts as $disjunct) {
            if ($disjunct instanceof self) {
                foreach ($disjunct->disjuncts as $expr) {
                    // $disjunct is guaranteed not to contain Disjunctions
                    $this->disjuncts[] = $expr;
                }
            } else {
                $this->disjuncts[] = $disjunct;
            }
        }
    }

    /**
     * Returns the disjuncts of the disjunction.
     *
     * @return Expression[] The disjuncts.
     */
    public function getDisjuncts()
    {
        return $this->disjuncts;
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

        foreach ($this->disjuncts as $disjunct) {
            if ($disjunct->equivalentTo($expr)) {
                return $this;
            }
        }

        $disjuncts = $this->disjuncts;

        if ($expr instanceof self) {
            $disjuncts = array_merge($disjuncts, $expr->disjuncts);
        } else {
            $disjuncts[] = $expr;
        }

        return new self($disjuncts);
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
    public function orCount(Expression $expr)
    {
        return $this->orX(Expr::count($expr));
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

    public function evaluate($values)
    {
        foreach ($this->disjuncts as $expr) {
            if ($expr->evaluate($values)) {
                return true;
            }
        }

        return false;
    }

    public function equivalentTo(Expression $other)
    {
        if (get_class($this) !== get_class($other)) {
            return false;
        }

        /** @var static $other */
        $leftDisjuncts = $this->disjuncts;
        $rightDisjuncts = $other->disjuncts;

        foreach ($leftDisjuncts as $leftDisjunct) {
            foreach ($rightDisjuncts as $j => $rightDisjunct) {
                if ($leftDisjunct->equivalentTo($rightDisjunct)) {
                    unset($rightDisjuncts[$j]);
                    continue 2;
                }
            }

            // $leftDisjunct was not found in $rightDisjuncts
            return false;
        }

        // All $leftDisjuncts were found. Check if any $rightDisjuncts are left
        return 0 === count($rightDisjuncts);
    }

    public function toString()
    {
        return implode(' || ', array_map(function (Expression $disjunct) {
            return $disjunct instanceof AndX ? '('.$disjunct->toString().')' : $disjunct->toString();
        }, $this->disjuncts));
    }

    public function __toString()
    {
        return $this->toString();
    }
}
