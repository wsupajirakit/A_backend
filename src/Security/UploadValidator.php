<?php

declare(strict_types=1);

namespace App\Security;

use finfo;

/**
 * Validates uploaded images by content: MIME via finfo, extension whitelist,
 * size and dimension limits. The stored extension is derived from the MIME type,
 * never from the client file name.
 */
final class UploadValidator
{
    public const OK = 1;
    public const NOT_IMAGE = 2;
    public const BAD_TYPE = 3;
    public const TOO_LARGE = 4;
    public const UPLOAD_ERROR = 5;
    public const DIR_NOT_WRITABLE = 6;

    private const MIME_EXT = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/bmp' => 'bmp',
        'image/x-ms-bmp' => 'bmp',
    ];

    /** @param bool $requireUploadedFile false only in tests */
    public function __construct(private bool $requireUploadedFile = true)
    {
    }

    /**
     * @param array{tmp_name?: string, error?: int, size?: int} $file entry from $_FILES
     * @param list<string> $allowedExtensions e.g. ['jpg', 'png']
     * @param array{0: int, 1: int}|null $minSize
     * @param array{0: int, 1: int}|null $maxSize
     */
    public function validate(array $file, array $allowedExtensions, int $maxBytes, ?array $minSize = null, ?array $maxSize = null): int
    {
        $tmp = (string) ($file['tmp_name'] ?? '');
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || $tmp === '' || !is_file($tmp)) {
            return self::UPLOAD_ERROR;
        }
        if ($this->requireUploadedFile && !is_uploaded_file($tmp)) {
            return self::UPLOAD_ERROR;
        }
        if ((int) filesize($tmp) > $maxBytes) {
            return self::TOO_LARGE;
        }
        $ext = $this->extensionFor($tmp);
        if ($ext === null) {
            return self::NOT_IMAGE;
        }
        $allowed = array_map(static fn (string $e): string => $e === 'jpeg' ? 'jpg' : strtolower($e), $allowedExtensions);
        if (!in_array($ext, $allowed, true)) {
            return self::BAD_TYPE;
        }
        $dimensions = @getimagesize($tmp);
        if ($dimensions === false) {
            return self::NOT_IMAGE;
        }
        return $this->dimensionsOk($dimensions[0], $dimensions[1], $minSize, $maxSize) ? self::OK : self::NOT_IMAGE;
    }

    /** Safe extension from file content, or null when not an allowed image type. */
    public function extensionFor(string $path): ?string
    {
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);
        return is_string($mime) ? (self::MIME_EXT[$mime] ?? null) : null;
    }

    public static function randomName(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * @param array{0: int, 1: int}|null $min
     * @param array{0: int, 1: int}|null $max
     */
    private function dimensionsOk(int $w, int $h, ?array $min, ?array $max): bool
    {
        if ($min !== null && ($w < $min[0] || $h < $min[1])) {
            return false;
        }
        return $max === null || ($w <= $max[0] && $h <= $max[1]);
    }
}
