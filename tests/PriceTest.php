<?php
declare(strict_types=1);

namespace PriceToy\Tests;

use PriceToy\Price;

/**
 * PHPUnit-shaped, dependency-free: every public method whose name starts with "test_" is a test, and a
 * thrown \AssertionError fails it. See tests/run.php.
 */
final class PriceTest
{
    private function assertSame(int $expected, ?int $actual, string $message): void
    {
        if ($expected !== $actual) {
            throw new \AssertionError(sprintf(
                '%s: expected %s, got %s',
                $message,
                var_export($expected, true),
                var_export($actual, true)
            ));
        }
    }

    /** The contract: rounding happens once, on the subtotal. */
    public function test_three_lines_round_at_subtotal(): void
    {
        $price = new Price();
        $price->add(335);
        $price->add(335);
        $price->add(335);

        $this->assertSame(1005, $price->subtotal(), 'subtotal is the exact sum of the lines');
        $this->assertSame(101, $price->total(), 'three lines of 335 must total 101 cents');
    }

    /** Guards against a suite that is red for everything. */
    public function test_single_line_still_rounds(): void
    {
        $price = new Price();
        $price->add(335);

        $this->assertSame(34, $price->total(), 'a single line of 335 rounds to 34 cents');
    }

    /** The second half of the contract: empty is null, never zero. */
    public function test_empty_basket_is_null(): void
    {
        $price = new Price();

        $this->assertSame(0, $price->lineCount(), 'a fresh basket has no lines');
        if ($price->total() !== null) {
            throw new \AssertionError('an empty basket must total null, not 0');
        }
    }
}
