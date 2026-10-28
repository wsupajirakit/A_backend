<?php

declare(strict_types=1);

namespace Tests;

use App\Security\UploadValidator;

final class UploadValidatorTest extends TestCase
{
    // 1x1 transparent PNG
    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

    public function testValidImagePasses(): void
    {
        $file = $this->file(base64_decode(self::PNG), 'photo.png');
        $validator = new UploadValidator(false);
        $this->assertSame(UploadValidator::OK, $validator->validate($file, ['jpg', 'png'], 1024));
        $this->assertSame('png', $validator->extensionFor($file['tmp_name']));
    }

    public function testScriptWithImageExtensionIsRejected(): void
    {
        $file = $this->file('<?php echo "x";', 'shell.png');
        $this->assertSame(UploadValidator::NOT_IMAGE, (new UploadValidator(false))->validate($file, ['png'], 1024));
    }

    public function testTypeNotInWhitelistIsRejected(): void
    {
        $file = $this->file(base64_decode(self::PNG), 'photo.jpg');
        $this->assertSame(UploadValidator::BAD_TYPE, (new UploadValidator(false))->validate($file, ['jpg'], 1024));
    }

    public function testOversizedFileIsRejected(): void
    {
        $file = $this->file(base64_decode(self::PNG), 'photo.png');
        $this->assertSame(UploadValidator::TOO_LARGE, (new UploadValidator(false))->validate($file, ['png'], 10));
    }

    public function testDimensionLimitsAndUploadErrors(): void
    {
        $validator = new UploadValidator(false);
        $file = $this->file(base64_decode(self::PNG), 'photo.png');
        $this->assertSame(UploadValidator::NOT_IMAGE, $validator->validate($file, ['png'], 1024, [10, 10]));
        $this->assertSame(UploadValidator::UPLOAD_ERROR, $validator->validate(['error' => UPLOAD_ERR_NO_FILE], ['png'], 1024));
        $this->assertSame(UploadValidator::UPLOAD_ERROR, (new UploadValidator())->validate($file, ['png'], 1024), 'non-uploaded file');
        $this->assertSame(32, strlen(UploadValidator::randomName()));
    }

    /** @return array{name: string, tmp_name: string, error: int, size: int} */
    private function file(string $content, string $name): array
    {
        $path = self::tempDir('upload') . '/tmp';
        file_put_contents($path, $content);
        return ['name' => $name, 'tmp_name' => $path, 'error' => UPLOAD_ERR_OK, 'size' => strlen($content)];
    }
}
