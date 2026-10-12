<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SIMTAG - Tirta Pinang</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <div id="app"></div>
    <script>
        window.__SIMTAG_DASHBOARD__ = @json($dashboard ?? null);
        window.__SIMTAG_BILL_PAGE__ = @json($billPage ?? null);
        window.__SIMTAG_COMPLAINT_PAGE__ = @json($complaintPage ?? null);
        window.__SIMTAG_PROFILE_PAGE__ = @json($profilePage ?? null);
        window.__SIMTAG_HISTORY_PAGE__ = @json($historyPage ?? null);
    </script>

</body>

</html>