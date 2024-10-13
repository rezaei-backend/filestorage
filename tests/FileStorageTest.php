<?php

use PHPUnit\Framework\TestCase;

require_once 'FileStorage.php';

class FileStorageTest extends TestCase {
    private $storage;
    private $testDirectory;

    protected function setUp(): void {
        $this->testDirectory = 'test_files/';
        $this->storage = new FileStorage($this->testDirectory, 5);
    }

    protected function tearDown(): void {
        // Delete all files in test directory
        array_map('unlink', glob($this->testDirectory . '*.txt'));
        rmdir($this->testDirectory);
    }

    public function testSaveToFile() {
        $data = ['name' => 'mohammad', 'age' => 25];
        $savedFile = $this->storage->saveToFile($data);

        $this->assertFileExists($savedFile, "File should be created");
        $this->assertStringEqualsFile($savedFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function testGetNextFileName() {
        // Create some files to check the next filename generation
        $this->storage->saveToFile(['dummy' => 'data']);
        $this->storage->saveToFile(['dummy' => 'data']);
        
        $nextFileName = $this->storage->saveToFile(['dummy' => 'data']);
        $expectedFileName = $this->testDirectory . '3.txt'; // as 1 and 2 are created
        
        $this->assertEquals($expectedFileName, $nextFileName, "Next filename should be correct");
    }

    public function testDirectoryCreation() {
        $this->assertTrue(is_dir($this->testDirectory), "Directory should be created");
    }
}
