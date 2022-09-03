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
 * "and" operators.
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * {@inheritdoc}
 */
class AndX implements Expression
{
    /**
     * @var Expression[]
     */
    private $conjuncts = [];

    /**
     * Creates a conjunction of the given expressions.
     *
     * @param Expression[] $conjuncts The conjuncts.
     */
    public function __construct(array $conjuncts = [])
    {
        foreach ($conjuncts as $conjunct) {
            if ($conjunct instanceof self) {
                foreach ($conjunct->conjuncts as $expr) {
                    // $conjunct is guaranteed not to contain Conjunctions
                    $this->conjuncts[] = $expr;
                }
            } else {
                $this->conjuncts[] = $conjunct;
            }
        }
    }

    /**
     * Returns the conjuncts of the conjunction.
     *
     * @return Expression[] The conjuncts.
     */
    public function getConjuncts()
    {
        return $this->conjuncts;
    }

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

        foreach ($this->conjuncts as $conjunct) {
            if ($conjunct->equivalentTo($expr)) {
                return $this;
            }
        }

        $conjuncts = $this->conjuncts;

        if ($expr instanceof self) {
            $conjuncts = array_merge($conjuncts, $expr->conjuncts);
        } else {
            $conjuncts[] = $expr;
        }

        return new self($conjuncts);
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

    public function evaluate($values)
    {
        foreach ($this->conjuncts as $expr) {
            if (!$expr->evaluate($values)) {
                return false;
            }
        }

        return true;
    }

    public function equivalentTo(Expression $other)
    {
        if (get_class($this) !== get_class($other)) {
            return false;
        }

        /** @var static $other */
        $leftConjuncts = $this->conjuncts;
        $rightConjuncts = $other->conjuncts;

        foreach ($leftConjuncts as $leftConjunct) {
            foreach ($rightConjuncts as $j => $rightConjunct) {
                if ($leftConjunct->equivalentTo($rightConjunct)) {
                    unset($rightConjuncts[$j]);
                    continue 2;
                }
            }

            // $leftConjunct was not found in $rightConjuncts
            return false;
        }

        // All $leftConjuncts were found. Check if any $rightConjuncts are left
        return 0 === count($rightConjuncts);
    }

    public function toString()
    {
        return implode(' && ', array_map(function (Expression $conjunct) {
            return $conjunct instanceof OrX ? '('.$conjunct->toString().')' : $conjunct->toString();
        }, $this->conjuncts));
    }

    public function __toString()
    {
        return $this->toString();
    }
}
