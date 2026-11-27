<?php

namespace Tests\Unit;

use App\Services\GoogleAuthenticator;
use PHPUnit\Framework\TestCase;

class GoogleAuthenticatorTest extends TestCase
{
    public function test_can_generate_secret_and_verify_totp()
    {
        $secret = GoogleAuthenticator::generateSecret();
        $this->assertEquals(16, strlen($secret));

        $code = GoogleAuthenticator::getCode($secret);
        $this->assertEquals(6, strlen($code));
        $this->assertTrue(ctype_digit($code));

        $this->assertTrue(GoogleAuthenticator::verifyCode($secret, $code));
        $this->assertFalse(GoogleAuthenticator::verifyCode($secret, '000000'));
    }

    public function test_qr_code_url_generation()
    {
        $secret = 'JBSWY3DPEHPK3PXP';
        $qrUrl = GoogleAuthenticator::getQrCodeUrl('dr. Budi', $secret);

        $this->assertStringContainsString('api.qrserver.com', $qrUrl);
        $this->assertStringContainsString('Delta', $qrUrl);
        $this->assertStringContainsString($secret, $qrUrl);
    }
}
