# price-toy

A deliberately tiny PHP library used to rehearse a review workflow. No framework, no database, no
network, no credentials.

## Contract

Amounts are integer **thousandths** of the currency unit. Results are integer **cents**.

1. `Price::subtotal()` is the exact sum of the line amounts, in thousandths.
2. `Price::total()` rounds the **subtotal** half-up to cents. It never rounds the individual lines:
   three lines of `335` thousandths are `1005` together, which rounds to `101` cents — not `102`.
3. `Price::total()` returns `null` when no line was added, so a caller can tell "nothing" from "free".

## Status

The repository is seeded with **one defect**: `total()` rounds each line and then adds, so a
three-line basket of `335` comes out one cent too high. `tests/PriceTest.php` contains the failing test
that exposes it (`test_three_lines_round_at_subtotal`) and a passing test that proves the suite is not
blanket-red (`test_single_line_still_rounds`). CI is expected to be red until the defect is fixed.

## Run

```
php tests/run.php
```
