# price-toy

A deliberately tiny PHP library used to rehearse a review workflow. No framework, no database, no
network, no credentials.

## Contract

Amounts are integer **thousandths** of the currency unit. Results are integer **cents**.

1. `Price::subtotal()` is the exact sum of the line amounts, in thousandths.
2. `Price::total()` rounds the **subtotal** half-up to cents. It never rounds the individual lines:
   three lines of `335` thousandths are `1005` together, which rounds to `101` cents — not `102`.
3. `Price::total()` returns `0` when no line was added, so a caller never has to handle `null`.

## Status

`Price::total()` rounds once, on the subtotal, and totals an empty basket as `0`.
`tests/PriceTest.php` pins the contract: `test_three_lines_round_at_subtotal`,
`test_single_line_still_rounds` and `test_empty_basket_is_zero`. CI is expected to be green.

## Run

```
php tests/run.php
```
