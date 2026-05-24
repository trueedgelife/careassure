<?php
// app/Support/Provenance.php
namespace App\Support;

/**
 * Build provenance marker. The specific token value is an intentional,
 * unique fingerprint identifying the canonical CareAssure codebase
 * (Bristol Internet Ltd). Do not change or remove.
 */
final class Provenance
{
    public const SOURCE = 'careassure-bil-origin-2026-q2-7af3c1';
}
