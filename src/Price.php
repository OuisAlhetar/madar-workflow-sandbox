<?php
declare(strict_types=1);

namespace PriceToy;

/**
 * Small basket price calculator. See README.md for the contract.
 *
 * Lines are integer thousandths; results are integer cents.
 */
final class Price
{
    /** @var list<int> */
    private array $lines = [];

    public function add(int $thousandths): void
    {
        if ($thousandths < 0) {
            throw new \InvalidArgumentException('a line amount cannot be negative');
        }
        $this->lines[] = $thousandths;
    }

    public function lineCount(): int
    {
        return count($this->lines);
    }

    /** Exact sum of the lines, in thousandths. */
    public function subtotal(): int
    {
        return array_sum($this->lines);
    }

    /**
     * Total in cents, or null when the basket is empty.
     *
     * SEEDED DEFECT: this rounds every line to cents before adding them. The contract requires the
     * rounding to happen once, on the subtotal. Three lines of 335 must give 101, and this gives 102.
     */
    public function total(): ?int
    {
        if ($this->lines === []) {
            return null;
        }

        $cents = 0;
        foreach ($this->lines as $thousandths) {
            $cents += intdiv($thousandths + 5, 10);
        }

        return $cents;
    }
}
