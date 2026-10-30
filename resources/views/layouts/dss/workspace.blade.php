<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SNSU Extension System — Workspace')</title>

    <!-- Local Bootstrap 5 CSS & Material Design Icons -->
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/libs/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">

    <!-- Dynamic SaaS Theme Engine -->
    @include('layouts.dss.theme-styles')

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #0f172a;
        }
        .workspace-shell {
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background-color: #f8fafc;
        }
        .workspace-topbar {
            height: 58px;
            min-height: 58px;
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 20;
        }
        .workspace-main {
            height: calc(100vh - 58px);
            display: flex;
            overflow: hidden;
        }
        .workspace-sidebar-pane {
            width: 480px;
            min-width: 420px;
            max-width: 540px;
            height: 100%;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            z-index: 10;
            border-right: 1px solid #e2e8f0;
            flex-shrink: 0;
        }
        .workspace-viewer-pane {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            background-color: #0f172a08;
            min-width: 0;
        }
        .workspace-viewer-tabs {
            height: 48px;
            min-height: 48px;
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            gap: 8px;
        }
        .workspace-viewer-stage {
            flex: 1 1 auto;
            position: relative;
            height: calc(100% - 48px);
            width: 100%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #1e293b10;
        }
        @media (max-width: 991px) {
            .workspace-main {
                flex-direction: column-reverse;
                overflow-y: auto;
            }
            .workspace-sidebar-pane {
                width: 100%;
                min-width: 100%;
                max-width: 100%;
                height: auto;
                min-height: 50vh;
                border-right: none;
            }
            .workspace-viewer-pane {
                height: 60vh;
                border-bottom: 1px solid #e2e8f0;
            }
        }
    </style>

    @yield('styles')
</head>
<body>
    <div class="workspace-shell">
        @yield('content')
    </div>

    <!-- Local Core Scripts -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    </script>
    <script src="{{ asset('js/dss/live_notifications.js') }}"></script>

    @yield('scripts')
</body>
</html>
