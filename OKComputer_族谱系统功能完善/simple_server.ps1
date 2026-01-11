$listener = New-Object System.Net.HttpListener
$listener.Prefixes.Add('http://localhost:8007/')
$listener.Start()
Write-Host '服务器已启动，访问地址：http://localhost:8007'

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
        }
        if ($filePath -match '\.js$') {
            $contentType = 'text/javascript'
        }
        if ($filePath -match '\.css$') {
            $contentType = 'text/css'
        }
        if ($filePath -match '\.png$') {
            $contentType = 'image/png'
        }
        if ($filePath -match '\.jpg$') {
            $contentType = 'image/jpeg'
        }
        if ($filePath -match '\.svg$') {
            $contentType = 'image/svg+xml'
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

# 按任意键停止服务器
Read-Host '按任意键停止服务器'
$listener.Stop()