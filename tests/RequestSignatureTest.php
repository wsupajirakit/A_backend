<?php

declare(strict_types=1);

namespace Tests;

use App\Security\RequestSignature;

final class RequestSignatureTest extends TestCase
{
    public function testContractVectorProducesExpectedSignature(): void
    {
        $canonical = RequestSignature::canonical('GET', '/api/v1/top-charts', '', '1700000000', 'abcdefghijklmnop', '');
        $this->assertSame(
            "GET\n/api/v1/top-charts\n\n1700000000\nabcdefghijklmnop\ne3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855",
            $canonical
        );
        $this->assertSame(
            'yrtECS+NgkMz7KJneR/cxnjQZQynLbXtMbAadVcjm2k=',
            RequestSignature::sign('test-secret-0123456789', $canonical)
        );
    }

    public function testQueryIsSortedAndRfc3986Encoded(): void
    {
        $this->assertSame('a=1&b=x%20y&z=~~', RequestSignature::canonicalQuery('z=%7E~&b=x%20y&a=1'));
        $this->assertSame('', RequestSignature::canonicalQuery(''));
    }

    public function testInvalidKeyJsonYieldsNoKeys(): void
    {
        $this->assertSame([], RequestSignature::keysFromJson('not json'));
        $this->assertSame(['a' => 's'], RequestSignature::keysFromJson('{"a":"s","b":""}'));
    }
}
