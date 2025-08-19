<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swagger UI</title>
    <link rel="stylesheet" type="text/css" href="{{$baseUrl}}/swagger-ui/swagger-ui.css" />
    <script src="{{$baseUrl}}/swagger-ui/swagger-ui-bundle.js"></script>
    <script src="{{$baseUrl}}/swagger-ui/swagger-ui-standalone-preset.js"></script>
</head>
<body>
<div id="swagger-ui"></div>
<script>
    const baseOrigin = window.location.origin; // e.g. http://localhost


    const ui = SwaggerUIBundle({
        url: "{{ $swaggerJsonUrl }}",  // Adjust to the correct path of your Swagger JSON
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIBundle.presets.schemas
        ],
        layout: "BaseLayout",
        requestInterceptor: (req) => {
            req.withCredentials = true;

            const url = new URL(req.url);

            if (url.hostname === 'localhost') {
                // Insert /my-remote/public after hostname but before the rest of path
                // Avoid duplicate /my-remote/public if already there:
                if (!url.pathname.startsWith('/my-remote/public')) {
                    url.pathname = '/my-remote/public' + url.pathname;
                }
            }

            req.url = url.toString();
            return req;
        }
    });


</script>
</body>
</html>
