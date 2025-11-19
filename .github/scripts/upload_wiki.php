<?php

/**
 * Use: 
 * $ export TOBA_API_TOKEN="supersecrettoken"
 * $ php .github/scripts/upload_wiki.php --id=test --host=local.sabau360.net
 */


/**
 * Parameters 
 * 
 */
$parameters = [];
$expected_parameters = ['id', 'host'];

foreach ($argv as $arg) {

    if (strpos($arg, '--') !== 0) {
        continue;
    }

    $parts = explode('=', substr($arg, 2), 2);
    $key = $parts[0];
    $value = $parts[1] ?? null;

    $parameters[$key] = $value;
}

foreach ($expected_parameters as $key) {

    if (empty($parameters[$key])) {

        echo "No parameter provided: '{$key}'\n";
        exit(1);
    }

}

// print_r($parameters); exit;

extract($parameters);
$token = getenv('TOBA_API_TOKEN');


if (!$token) {

    echo "No env TOBA_API_TOKEN provided\n";
    exit(1);

}


$startDirectory = realpath(__DIR__ . '/../../');

/**
 * Send data to a server using PUT method.
 *
 * @param string $url
 * @param array|string $payload
 * @param array $headers
 * @return array
 */
function sendFile(string $url, $payload, array $headers = []): array
{
    // If payload is an array, automatically convert to JSON
    if (is_array($payload)) {
        $payload = json_encode($payload);
        $headers[] = 'Content-Type: application/json';
    }

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => $headers
    ]);

    $response = curl_exec($curl);
    $error = curl_error($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    $data = json_decode($response, true);

    if (is_array($data)) {

        $response = $data;
    }

    if ($status != 200 && empty($error) && !empty($response['error'])) {

        $error = $response['error'];
    }

    if ($error) {

        throw new \RuntimeException($error, $status ? $status : 1);
    }

    return [
        'status' => $status,
        'response' => $response,
        'error' => $error
    ];
}


// Create recursive directory iterator
$directoryIterator = new RecursiveDirectoryIterator(
    $startDirectory,
    FilesystemIterator::SKIP_DOTS
);

// Wrap it in recursive iterator
$recursiveIterator = new RecursiveIteratorIterator($directoryIterator);

// Array to store found Markdown files
$markdownFiles = [];

foreach ($recursiveIterator as $file) {
    // Skip directories, we only want files
    if ($file->isDir()) {
        continue;
    }

    // Check file extension (case-insensitive)
    if (strtolower($file->getExtension()) === 'md') {

        $md = $file->getPathname();
        $md = substr($md, strlen($startDirectory) + 1);

        $markdownFiles[$md] = $file->getPathname();
    }
}


// print_r($markdownFiles);

$meta = [
    'index' => [],
];

foreach ($markdownFiles as $file => $path) {
    $meta['index'][] = $file;
}
// print_r($meta);

$headers = [
    'Authorization: Bearer ' . $token
];

$url = "https://{$host}/api/wiki/{$id}/meta.json";
echo "Uploading: {$id}/meta.json ...";
$result = sendFile($url, json_encode($meta), $headers);
// print_r($result);
echo empty($result['response']['status']) ? ' Error' : " {$result['response']['status']}";
echo "\n";


foreach ($markdownFiles as $file => $path) {

    $url = "https://{$host}/api/wiki/{$id}/{$file}";
    echo "Uploading: {$id}/{$file} ...";
    $result = sendFile($url, file_get_contents($path), $headers);

    echo empty($result['response']['status']) ? ' Error' : " {$result['response']['status']}";
    echo "\n";

}

