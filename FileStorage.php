<?php

class FileStorage {
    private $directory;
    private $maxFiles;

    public function __construct($directory = 'files/', $maxFiles = 100) {
        $this->directory = $directory;
        $this->maxFiles = $maxFiles;
        // chek directory and create directory
        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0777, true);
        }
    }
    // next filename
    private function getNextFileName() {
        for ($i = $this->maxFiles; $i >= 1; $i--) {
            $fileName = $this->directory . $i . '.txt';
            if (!file_exists($fileName)) {
                return $fileName;
            }
        }
        // max file 00 , if have 00 files , return back 
        return $this->directory . $this->maxFiles . '.txt';
    }

    // save data to file 
    public function saveToFile($data) {
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        $fileName = $this->getNextFileName();
        file_put_contents($fileName, $jsonData);
        return $fileName;
    }
}

// API Handler 
class ApiHandler {
    private $storage;

    public function __construct(FileStorage $storage) {
        $this->storage = $storage;
    }

    public function handleRequest() {
        $requestData = file_get_contents('php://input');
        $data = json_decode($requestData, true);
        $savedFile = $this->storage->saveToFile($data);

        // api response
        $response = [
            'status' => 'success',
            'file' => $savedFile,
            'message' => 'Data has been saved successfully!'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
$storage = new FileStorage();
$apiHandler = new ApiHandler($storage);
$apiHandler->handleRequest();