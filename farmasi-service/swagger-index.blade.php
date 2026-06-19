<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('l5-swagger.documentations.'.config('l5-swagger.default').'.api.title', 'API Docs') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="{{ l5_swagger_asset(config('l5-swagger.default'), 'swagger-ui.css') }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #0f1117;
            color: #e2e8f0;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        /* Top navbar */
        .swagger-top-bar {
            background: linear-gradient(135deg, #1e3a5f 0%, #0d2137 100%);
            padding: 16px 32px;
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 1px solid #1e40af44;
            box-shadow: 0 4px 24px rgba(0,0,0,0.4);
        }
        .swagger-top-bar .logo {
            font-size: 22px;
            font-weight: 700;
            color: #60a5fa;
            letter-spacing: -0.5px;
        }
        .swagger-top-bar .logo span { color: #34d399; }
        .swagger-top-bar .subtitle {
            font-size: 13px;
            color: #94a3b8;
            margin-left: auto;
        }

        /* Override Swagger UI */
        #swagger-ui { max-width: 1200px; margin: 0 auto; padding: 24px 16px; }

        .swagger-ui .topbar { display: none !important; }

        .swagger-ui .info {
            background: #1e293b;
            border-radius: 12px;
            padding: 24px !important;
            margin-bottom: 24px;
            border: 1px solid #334155;
        }
        .swagger-ui .info .title {
            color: #60a5fa !important;
            font-size: 28px !important;
        }
        .swagger-ui .info .description p {
            color: #94a3b8 !important;
        }

        .swagger-ui .scheme-container {
            background: #1e293b !important;
            border-radius: 12px;
            padding: 16px !important;
            border: 1px solid #334155;
            margin-bottom: 24px;
        }

        .swagger-ui select {
            background: #0f172a !important;
            color: #e2e8f0 !important;
            border: 1px solid #334155 !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
        }

        .swagger-ui .btn.authorize {
            background: #1d4ed8 !important;
            color: white !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 8px 20px !important;
            font-weight: 600 !important;
        }
        .swagger-ui .btn.authorize:hover {
            background: #2563eb !important;
        }

        /* Tag sections */
        .swagger-ui .opblock-tag {
            background: #1e293b !important;
            border-radius: 10px !important;
            border: 1px solid #334155 !important;
            color: #e2e8f0 !important;
            padding: 12px 16px !important;
            margin-bottom: 8px !important;
            font-size: 16px !important;
            font-weight: 600 !important;
        }
        .swagger-ui .opblock-tag:hover {
            background: #263548 !important;
        }

        /* Operation blocks */
        .swagger-ui .opblock {
            border-radius: 10px !important;
            margin-bottom: 8px !important;
            border: 1px solid #334155 !important;
            overflow: hidden;
        }
        .swagger-ui .opblock .opblock-summary {
            background: #1e293b !important;
            padding: 12px 16px !important;
        }
        .swagger-ui .opblock .opblock-summary-description {
            color: #94a3b8 !important;
        }
        .swagger-ui .opblock .opblock-body {
            background: #162032 !important;
        }

        /* GET */
        .swagger-ui .opblock-get {
            border-color: #1d4ed8 !important;
        }
        .swagger-ui .opblock-get .opblock-summary-method {
            background: #1d4ed8 !important;
            border-radius: 6px !important;
            font-weight: 700 !important;
        }

        /* POST */
        .swagger-ui .opblock-post {
            border-color: #059669 !important;
        }
        .swagger-ui .opblock-post .opblock-summary-method {
            background: #059669 !important;
            border-radius: 6px !important;
            font-weight: 700 !important;
        }

        /* PUT */
        .swagger-ui .opblock-put {
            border-color: #d97706 !important;
        }
        .swagger-ui .opblock-put .opblock-summary-method {
            background: #d97706 !important;
            border-radius: 6px !important;
            font-weight: 700 !important;
        }

        /* DELETE */
        .swagger-ui .opblock-delete {
            border-color: #dc2626 !important;
        }
        .swagger-ui .opblock-delete .opblock-summary-method {
            background: #dc2626 !important;
            border-radius: 6px !important;
            font-weight: 700 !important;
        }

        /* Inputs */
        .swagger-ui input[type=text],
        .swagger-ui textarea {
            background: #0f172a !important;
            color: #e2e8f0 !important;
            border: 1px solid #334155 !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
        }

        /* Execute button */
        .swagger-ui .btn.execute {
            background: #1d4ed8 !important;
            color: white !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 10px 24px !important;
            font-weight: 600 !important;
        }

        /* Response */
        .swagger-ui .responses-inner {
            background: #0f172a !important;
            border-radius: 8px !important;
            padding: 16px !important;
        }
        .swagger-ui .microlight {
            background: #0f172a !important;
            color: #34d399 !important;
            border-radius: 8px !important;
            padding: 12px !important;
        }

        /* Labels & text */
        .swagger-ui label, .swagger-ui .parameter__name,
        .swagger-ui table thead tr th {
            color: #94a3b8 !important;
        }
        .swagger-ui .parameter__type { color: #60a5fa !important; }

        /* Modal */
        .swagger-ui .dialog-ux .modal-ux {
            background: #1e293b !important;
            border: 1px solid #334155 !important;
            border-radius: 12px !important;
        }
        .swagger-ui .dialog-ux .modal-ux-header {
            background: #162032 !important;
            border-bottom: 1px solid #334155 !important;
            border-radius: 12px 12px 0 0 !important;
        }
        .swagger-ui .dialog-ux .modal-ux-header h3 { color: #e2e8f0 !important; }
        .swagger-ui .dialog-ux .modal-ux-content p { color: #94a3b8 !important; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f1117; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
    </style>
</head>
<body>

<div class="swagger-top-bar">
    <div class="logo">🏥 E-Healthcare <span>Rawat Jalan</span></div>
    <div class="subtitle">API Documentation · v1.0.0 · OAS 3.0</div>
</div>

<div id="swagger-ui"></div>

<script src="{{ l5_swagger_asset(config('l5-swagger.default'), 'swagger-ui-bundle.js') }}"></script>
<script src="{{ l5_swagger_asset(config('l5-swagger.default'), 'swagger-ui-standalone-preset.js') }}"></script>
<script>
window.onload = function() {
    const defined = @json(config('l5-swagger.documentations'));
    const defaultConfig = @json(config('l5-swagger.default'));

    var configObject = {
        dom_id: '#swagger-ui',
        url: "{{ route('l5-swagger.'.config('l5-swagger.default').'.docs') }}",
        operationsSorter: @json(config('l5-swagger.documentations.'.config('l5-swagger.default').'.operations_sort')),
        configUrl: null,
        validatorUrl: @json(config('l5-swagger.documentations.'.config('l5-swagger.default').'.validator_url')),
        oauth2RedirectUrl: "{{ route('l5-swagger.'.config('l5-swagger.default').'.oauth2_callback') }}",
        requestSnippetsEnabled: true,
        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIBundle.SwaggerUIStandalonePreset
        ],
        plugins: [
            SwaggerUIBundle.plugins.DownloadUrl
        ],
        layout: "StandaloneLayout",
        deepLinking: true,
        filter: true,
        persistAuthorization: @json(config('l5-swagger.documentations.'.config('l5-swagger.default').'.persist_authorization', false)),
    };

    @if(config('l5-swagger.defaults.ui.display.doc_expansion'))
    configObject.docExpansion = @json(config('l5-swagger.defaults.ui.display.doc_expansion'));
    @endif

    const ui = SwaggerUIBundle(configObject);
    window.ui = ui;
}
</script>
</body>
</html>
