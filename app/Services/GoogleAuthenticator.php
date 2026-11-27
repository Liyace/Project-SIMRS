<?php

namespace App\Services;

/**
 * Service Google Authenticator TOTP (RFC 6238) murni tanpa library eksternal.
 * Kompatibel 100% dengan aplikasi Google Authenticator, Microsoft Authenticator,
 * dan aplikasi TOTP standar lainnya.
 */
class GoogleAuthenticator
{
    private static string $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Menghasilkan Secret Key Base32 acak (panjang 16 karakter standar TOTP).
     */
    public static function generateSecret(int $length = 16): string
    {
        $chars = self::$base32Chars;
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Menghasilkan kode 6 digit OTP saat ini berdasarkan timestamp.
     */
    public static function getCode(string $secret, ?int $timestamp = null): string
    {
        if ($timestamp === null) {
            $timestamp = time();
        }

        $timeSlice = (int) floor($timestamp / 30);
        $secretBinary = self::base32Decode($secret);

        // Pack waktu 64-bit Big-Endian
        $timeBinary = pack('N*', 0) . pack('N*', $timeSlice);

        // HMAC-SHA1
        $hash = hash_hmac('sha1', $timeBinary, $secretBinary, true);

        // Dynamic truncation sesuai RFC 6238 / RFC 4226
        $offset = ord(substr($hash, -1)) & 0x0F;
        $hashPart = substr($hash, $offset, 4);
        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;

        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verifikasi kode yang dimasukkan pengguna.
     * Menggunakan toleransi waktu $discrepancy (default ±2 interval = ±60 detik / total jendela 2,5 menit)
     * agar lebih toleran terhadap sedikit perbedaan detik jam di HP pengguna.
     */
    public static function verifyCode(string $secret, string $code, int $discrepancy = 2): bool
    {
        $code = trim($code);
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }

        $currentTime = time();
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $checkTime = $currentTime + ($i * 30);
            if (hash_equals(self::getCode($secret, $checkTime), $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * URL OtpAuth untuk aplikasi Authenticator.
     */
    public static function getOtpAuthUrl(string $name, string $secret, string $issuer = 'RS Delta'): string
    {
        return 'otpauth://totp/' . rawurlencode($issuer) . ':' . rawurlencode($name) .
            '?secret=' . $secret .
            '&issuer=' . rawurlencode($issuer) .
            '&algorithm=SHA1&digits=6&period=30';
    }

    /**
     * URL gambar QR Code untuk dipindai di Google Authenticator.
     */
    public static function getQrCodeUrl(string $name, string $secret, string $issuer = 'RS Delta'): string
    {
        $otpauthUrl = self::getOtpAuthUrl($name, $secret, $issuer);
        return 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=10&data=' . rawurlencode($otpauthUrl);
    }

    /**
     * Decode string Base32 menjadi biner.
     */
    private static function base32Decode(string $b32): string
    {
        $b32 = strtoupper(str_replace(' ', '', $b32));
        $chars = self::$base32Chars;
        $buffer = 0;
        $bufferBits = 0;
        $binary = '';

        for ($i = 0; $i < strlen($b32); $i++) {
            $pos = strpos($chars, $b32[$i]);
            if ($pos === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $pos;
            $bufferBits += 5;

            if ($bufferBits >= 8) {
                $bufferBits -= 8;
                $binary .= chr(($buffer >> $bufferBits) & 0xFF);
            }
        }

        return $binary;
    }
}
