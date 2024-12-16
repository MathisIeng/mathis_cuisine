<?php

namespace App\services;

use PHPUnit\Framework\TestCase;

class UniqueFilenameGeneratorTest extends TestCase
{
    public function testGenerateUniqueFilename() {

        $uniqueFilenameGenerator = new UniqueFilenameGenerator();
        $uniqueFilename = $uniqueFilenameGenerator->generateUniqueFilename('hello', 'jpg');

        $this->assertStringContainsString('jpg', $uniqueFilename);

    }
}