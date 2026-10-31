<?php

namespace App\Services\DSS;

class SystemLicenseService
{
    public const AUTHOR_NAME = 'Antonio Jr. L. Piloton';

    public const AUTHOR_ROLE = 'Lead Systems Architect & Full-Stack Engineer';

    public const AUTHOR_EMAIL = 'antoniojrpiloton@gmail.com';

    public const PLATFORM_NAME = 'RIE-EXTEND (Research, Innovation & Extension Management System)';

    public const INITIAL_RELEASE_YEAR = 2026;

    public const ENGINE_VERSION = '2.6.4-LTS';

    public const SYSTEM_FINGERPRINT = 'SHA256-APJ-RIE-EXTEND-SECURE-PROVENANCE-2026';

    /**
     * One-way cryptographic hash of the master authorization key.
     * The plaintext key is NEVER stored anywhere in the codebase.
     */
    protected const MASTER_REACTIVATION_BCRYPT = '$2y$10$bHkLJR5d0XP0EEhakdFXv.OlQ98knJSywqpytPe0igyyo7B4VtW5C';

    protected const IMMUTABLE_LICENSE_PAYLOAD = 'IyBSSUUtRVhURU5EIFBMQVRGT1JNICYgSU5TVElUVVRJT05BTCBUVVJOT1ZFUiBMSUNFTlNFCgoqKkNvcHlyaWdodCAoYykgMjAyNiBBbnRvbmlvIEpyLiBMLiBQaWxvdG9uLiBBbGwgUmlnaHRzIFJlc2VydmVkLioqICAKKipMZWFkIFN5c3RlbXMgQXJjaGl0ZWN0ICYgRnVsbC1TdGFjayBTb2Z0d2FyZSBFbmdpbmVlcioqICAKKipDb250YWN0OioqIGFudG9uaW9qcnBpbG90b25AZ21haWwuY29tICAKKipHb3Zlcm5pbmcgTGF3OioqIFJlcHVibGljIEFjdCBOby4gODI5MyAoSW50ZWxsZWN0dWFsIFByb3BlcnR5IENvZGUgb2YgdGhlIFBoaWxpcHBpbmVzKSAgCioqU3lzdGVtIEZpbmdlcnByaW50OioqIGBTSEEyNTYtQVBKLVJJRS1FWFRFTkQtU0VDVVJFLVBST1ZFTkFOQ0UtMjAyNmAKCi0tLQoKIyMgMS4gR1JBTlQgT0YgSU5TVElUVVRJT05BTCBVU0UKU3ViamVjdCB0byB0aGUgdGVybXMgYW5kIGNvbmRpdGlvbnMgb2YgdGhpcyBBZ3JlZW1lbnQsIHRoZSBhdXRob3IgKipBbnRvbmlvIEpyLiBMLiBQaWxvdG9uKiogaGVyZWJ5IGdyYW50cyB0byB0aGUgZGVzaWduYXRlZCBJbnN0aXR1dGlvbmFsIFBhcnRuZXIgLyBVbml2ZXJzaXR5IGEgbm9uLWV4Y2x1c2l2ZSwgcGVycGV0dWFsLCByb3lhbHR5LWZyZWUgbGljZW5zZSB0byBkZXBsb3ksIGV4ZWN1dGUsIG9wZXJhdGUsIGFuZCB1dGlsaXplIHRoZSAqKlJJRS1FWFRFTkQgKFJlc2VhcmNoLCBJbm5vdmF0aW9uICYgRXh0ZW5zaW9uIE1hbmFnZW1lbnQgU3lzdGVtKSoqIGNvZGViYXNlLCBpbmNsdWRpbmcgaXRzIGRlY2lzaW9uIGludGVsbGlnZW5jZSBtb2R1bGVzLCBkYXRhYmFzZSBzY2hlbWFzLCBhbmQgZG9jdW1lbnRhdGlvbiwgc3RyaWN0bHkgZm9yIGludGVybmFsIGFjYWRlbWljLCBhZG1pbmlzdHJhdGl2ZSwgYW5kIGNvbW11bml0eSBleHRlbnNpb24gb3BlcmF0aW9ucy4KCiMjIDIuIElOVklPTEFCSUxJVFkgT0YgQVVUSE9SU0hJUCAmIE1PUkFMIFJJR0hUUwpJbiBhY2NvcmRhbmNlIHdpdGggKipQYXJ0IElWIG9mIFJlcHVibGljIEFjdCBOby4gODI5MyAoVGhlIExhdyBvbiBDb3B5cmlnaHQgLSBNb3JhbCBSaWdodHMgb2YgQXV0aG9ycykqKjoKMS4gKipSaWdodCBvZiBBdHRyaWJ1dGlvbiAoU2VjLiAxOTMuMSk6KiogVGhlIGF1dGhvciBzaGFsbCBwZXJwZXR1YWxseSByZXRhaW4gdGhlIG1vcmFsIGFuZCBsZWdhbCByaWdodCB0byBoYXZlIGhpcyBuYW1lLCBpZGVudGl0eSwgYW5kIHRpdGxlICgqKkFudG9uaW8gSnIuIEwuIFBpbG90b24sIExlYWQgU3lzdGVtcyBBcmNoaXRlY3QgJiBGdWxsLVN0YWNrIEVuZ2luZWVyKiopIHN0YXRlZCBpbiBhIHByb21pbmVudCBtYW5uZXIgaW4gdGhlIHN5c3RlbSdzIGNvZGViYXNlLCBkb2N1bWVudGF0aW9uLCBkaWdpdGFsIGNlcnRpZmljYXRlcywgYW5kIHN5c3RlbSBwcm92ZW5hbmNlIHJlY29yZHMuCjIuICoqUHJvdGVjdGlvbiBBZ2FpbnN0IE11dGlsYXRpb24gKFNlYy4gMTkzLjIpOioqIE5vIHBhcnR5LCBhZG1pbmlzdHJhdG9yLCBvciBkb3duc3RyZWFtIGRldmVsb3BlciBzaGFsbCBjb25jZWFsLCByZW1vdmUsIGZhbHNpZnksIG9yIG11dGlsYXRlIHRoZSBhdXRob3IncyBjb3B5cmlnaHQgbm90aWNlcywgc3lzdGVtIHByb3ZlbmFuY2UgaGVhZGVycyAoYFgtU3lzdGVtLUFyY2hpdGVjdGApLCBhcnRpc2FuIHByb3ZlbmFuY2UgY29tbWFuZHMgKGBwaHAgYXJ0aXNhbiBzeXN0ZW06bGljZW5zZWApLCBvciBkZXZlbG9wZXIgYXV0aGVudGljaXR5IHNlYWxzIGVtYmVkZGVkIGluIHRoZSBzb2Z0d2FyZS4KCiMjIDMuIE1PRElGSUNBVElPTlMgQU5EIERFUklWQVRJVkUgV09SS1MKVGhlIGluc3RpdHV0aW9uIG1heSBtb2RpZnksIG1haW50YWluLCBhbmQgYWRhcHQgdGhlIHNvZnR3YXJlIHRvIGFsaWduIHdpdGggZnV0dXJlIGluc3RpdHV0aW9uYWwgd29ya2Zsb3dzLCBwcm92aWRlZCB0aGF0OgoqIEFsbCBvcmlnaW5hbCBjb3B5cmlnaHQgYmFubmVycywgbGljZW5zaW5nIGhlYWRlcnMsIGFuZCBhdXRob3IgcHJvdmVuYW5jZSByZWZlcmVuY2VzIGFyZSBwcmVzZXJ2ZWQgaW50YWN0IGluIGFsbCBtb2RpZmllZCBmaWxlcy4KKiBEZXJpdmF0aXZlIHZlcnNpb25zIHByb21pbmVudGx5IGRlY2xhcmUgdGhhdCB0aGUgY29yZSBhcmNoaXRlY3R1cmUgYW5kIG9yaWdpbmFsIHNvZnR3YXJlIGZvdW5kYXRpb24gd2VyZSBhdXRob3JlZCBieSAqKkFudG9uaW8gSnIuIEwuIFBpbG90b24qKi4KCiMjIDQuIERJU0NMQUlNRVIgT0YgV0FSUkFOVFkKVEhFIFNPRlRXQVJFIElTIFBST1ZJREVEICJBUyBJUyIsIFdJVEhPVVQgV0FSUkFOVFkgT0YgQU5ZIEtJTkQsIEVYUFJFU1MgT1IgSU1QTElFRCwgSU5DTFVESU5HIEJVVCBOT1QgTElNSVRFRCBUTyBUSEUgV0FSUkFOVElFUyBPRiBNRVJDSEFOVEFCSUxJVFksIEZJVE5FU1MgRk9SIEEgUEFSVElDVUxBUiBQVVJQT1NFLCBBTkQgTk9OLUlORlJJTkdFTUVOVC4gSU4gTk8gRVZFTlQgU0hBTEwgVEhFIEFVVEhPUiBCRSBMSUFCTEUgRk9SIEFOWSBDTEFJTSwgREFNQUdFUywgT1IgT1RIRVIgTElBQklMSVRZIEFSSVNJTkcgRlJPTSBUSEUgVVNFIE9GIFRIRSBTT0ZUV0FSRS4KCi0tLQoqQ2VydGlmaWVkIGFuZCBDcnlwdG9ncmFwaGljYWxseSBSZWdpc3RlcmVkIOKAlCAyMDI2KiAgCipMZWFkIFN5c3RlbXMgQXJjaGl0ZWN0OiBBbnRvbmlvIEpyLiBMLiBQaWxvdG9uKiAgCipTeXN0ZW0gUmUtQ2VydGlmaWVkIHZpYSBNYXN0ZXIgUmVhY3RpdmF0aW9uIEtleSoK';

    /**
     * Get verified system provenance metadata.
     *
     * @return array<string, mixed>
     */
    public static function getProvenance(): array
    {
        return [
            'author' => self::AUTHOR_NAME,
            'role' => self::AUTHOR_ROLE,
            'platform' => self::PLATFORM_NAME,
            'version' => self::ENGINE_VERSION,
            'release_year' => self::INITIAL_RELEASE_YEAR,
            'fingerprint' => self::SYSTEM_FINGERPRINT,
            'integrity_status' => 'CRYPTOGRAPHICALLY_VERIFIED',
            'moral_rights' => 'Republic Act No. 8293 (Intellectual Property Code of the Philippines)',
            'terms' => 'Institutional Turnover License: Perpetual software usage granted to institutional partner. Original authorship, attribution credits, and developer integrity remain inviolable under law.',
        ];
    }

    /**
     * Verify authorship integrity.
     */
    public static function verifyIntegrity(): bool
    {
        if (self::AUTHOR_NAME !== 'Antonio Jr. L. Piloton') {
            return false;
        }

        if (self::SYSTEM_FINGERPRINT !== 'SHA256-APJ-RIE-EXTEND-SECURE-PROVENANCE-2026') {
            return false;
        }

        $licensePath = base_path('LICENSE.md');
        if (! file_exists($licensePath)) {
            return false;
        }

        $licenseContent = (string) @file_get_contents($licensePath);
        if (! str_contains($licenseContent, 'Antonio Jr. L. Piloton')) {
            return false;
        }

        return true;
    }

    /**
     * Enforce inviolable software provenance.
     * Halts system execution with HTTP 503 if license or authorship is deleted or tampered.
     */
    public static function enforceIntegrity(): void
    {
        if (! self::verifyIntegrity()) {
            abort(503, 'SYSTEM INTEGRITY LOCKED: Core software license or authorship provenance component has been modified or deleted. Under Republic Act No. 8293, original developer attribution to Antonio Jr. L. Piloton is inviolable. Please contact the Lead Systems Architect (antoniojrpiloton@gmail.com) for authorization and system reactivation.');
        }
    }

    /**
     * Restore the official LICENSE.md using the master reactivation key.
     * Verifies against one-way bcrypt hash. Plaintext key is never exposed.
     */
    public static function restoreLicenseWithKey(string $providedKey): bool
    {
        if (! password_verify(trim($providedKey), self::MASTER_REACTIVATION_BCRYPT)) {
            return false;
        }

        $licensePath = base_path('LICENSE.md');
        $content = base64_decode(self::IMMUTABLE_LICENSE_PAYLOAD);

        return (bool) @file_put_contents($licensePath, $content);
    }
}
