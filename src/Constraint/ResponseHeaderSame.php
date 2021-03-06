<?php

declare(strict_types=1);

namespace Speicher210\FunctionalTestBundle\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use SebastianBergmann\Comparator\ComparisonFailure;
use Symfony\Component\HttpFoundation\Response;

final class ResponseHeaderSame extends Constraint
{
    /** @var string */
    private $headerName;

    /** @var string */
    private $expectedValue;

    public function __construct(string $headerName, string $expectedValue)
    {
        $this->headerName    = $headerName;
        $this->expectedValue = $expectedValue;
    }

    /**
     * @param Response|mixed $other
     */
    public function evaluate($other, string $description = '', bool $returnResult = false) : ?bool
    {
        $success = false;

        if ($this->matches($other)) {
            $success = true;
        }

        if ($returnResult) {
            return $success;
        }

        if ($success) {
            return null;
        }

        $actualValue       = $other->headers->get($this->headerName);
        $comparisonFailure = new ComparisonFailure(
            $this->expectedValue,
            $actualValue,
            $this->exporter()->export($this->expectedValue),
            $this->exporter()->export($actualValue)
        );

        $this->fail($other, $description, $comparisonFailure);
    }

    /**
     * {@inheritdoc}
     */
    public function toString() : string
    {
        return \sprintf('has header "%s" with value "%s"', $this->headerName, $this->expectedValue);
    }

    /**
     * {@inheritdoc}
     */
    protected function matches($other) : bool
    {
        if ($other instanceof Response) {
            return $this->expectedValue === $other->headers->get($this->headerName);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function failureDescription($other) : string
    {
        if ($other instanceof Response) {
            return 'the response ' . $this->toString();
        }

        return parent::failureDescription($other);
    }
}
