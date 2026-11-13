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

    /**
     * Regression for #3: the half-up decision belongs to the subtotal, so a basket whose lines round
     * individually in the other direction must follow its subtotal, not its lines.
     *
     * 335 thousandths is .5 of a cent: three of them are 1005, which halves up to 101 cents. Rounding
     * each line first gives 34+34+34 = 102. The mirror case is 334 thousandths: three of them are
     * 1002, which halves down to 100 cents, while rounding each line first gives 33+33+33 = 99.
     */
    public function test_rounding_boundary_is_decided_by_the_subtotal(): void
    {
        $price = new Price();
        $price->add(335);
        $price->add(335);
        $price->add(335);

        $this->assertSame(101, $price->total(), 'three lines of 335 must total 101 cents, not 102');

        $mirror = new Price();
        $mirror->add(334);
        $mirror->add(334);
        $mirror->add(334);

        $this->assertSame(100, $mirror->total(), 'three lines of 334 must total 100 cents, not 99');
    }
}
