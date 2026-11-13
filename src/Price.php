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
     * Total in cents. An empty basket totals 0, so a caller never has to handle null.
     *
     * The contract (README.md rule 2) rounds once, on the subtotal: three lines of 335 are 1005
     * thousandths together, which halves up to 101 cents. Individual lines are never rounded.
     */
    public function total(): int
    {
        // Half-up from thousandths to cents. Lines are non-negative (see add()), so intdiv is safe;
        // an empty basket has subtotal 0 and therefore totals 0.
        return intdiv($this->subtotal() + 5, 10);
    }
}
