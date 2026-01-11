$listener = New-Object System.Net.HttpListener
$listener.Prefixes.Add('http://localhost:8010/')
$listener.Start()
Write-Host "Server running at http://localhost:8010/"
Write-Host "Press Ctrl+C to stop the server"

while ($listener.IsListening) {
    try {
        $context = $listener.GetContext()
        $request = $context.Request
        $response = $context.Response
        
        $localPath = $request.Url.LocalPath
        if ($localPath -eq '/') {
            $localPath = '/index.html'
        }
        
        $filePath = Join-Path (Get-Location) $localPath.Substring(1)
        
        if (Test-Path $filePath -PathType Leaf) {
            $contentType = 'text/html'
            if ($filePath -match '\.css$') {
                $contentType = 'text/css'
            } elseif ($filePath -match '\.js$') {
                $contentType = 'text/javascript'
            } elseif ($filePath -match '\.(png|jpg|jpeg|gif)$') {
                $contentType = 'image/' + $matches[1]
            }
            
            $buffer = [System.IO.File]::ReadAllBytes($filePath)
            $response.ContentType = $contentType
            $response.ContentLength64 = $buffer.Length
            $response.OutputStream.Write($buffer, 0, $buffer.Length)
        } else {
            $response.StatusCode = 404
            $buffer = [System.Text.Encoding]::UTF8.GetBytes('<html><body><h1>404 Not Found</h1></body></html>')
            $response.ContentLength64 = $buffer.Length
            $response.OutputStream.Write($buffer, 0, $buffer.Length)
        }
        
        $response.Close()
    } catch {
        Write-Host "Error: $_"
    }
}

$listener.Stop()
Write-Host "Server stopped"