$listener = New-Object System.Net.HttpListener
$listener.Prefixes.Add('http://localhost:8005/')
$listener.Start()
Write-Host 'Server started at http://localhost:8005' -ForegroundColor Green

while ($listener.IsListening) {
    $context = $listener.GetContext()
    $request = $context.Request
    $response = $context.Response
    
    $localPath = $request.Url.LocalPath
    if ($localPath -eq '/') {
        $localPath = '/index.html'
    }
    
    $filePath = Join-Path (Get-Location) $localPath.TrimStart('/')
    
    if (Test-Path $filePath -PathType Leaf) {
        $contentType = 'text/plain'
        if ($filePath -match '\.html$') {
            $contentType = 'text/html'
        } elseif ($filePath -match '\.js$') {
            $contentType = 'text/javascript'
        } elseif ($filePath -match '\.css$') {
            $contentType = 'text/css'
        }
        
        $response.ContentType = $contentType
        $content = [System.IO.File]::ReadAllBytes($filePath)
        $response.OutputStream.Write($content, 0, $content.Length)
    } else {
        $response.StatusCode = 404
        $response.ContentType = 'text/html'
        $content = [System.Text.Encoding]::UTF8.GetBytes('<html><body><h1>404 Not Found</h1></body></html>')
        $response.OutputStream.Write($content, 0, $content.Length)
    }
    
    $response.Close()
}