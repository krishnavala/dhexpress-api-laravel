<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <meta name="description" content="Welcome to Yaba." />
        <meta property="og:locale" content="en_US" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="Yaba" />
        <meta property="og:description" content="Welcome to Yaba" />
        <meta property="og:url" content="https://yaba.com/" />
        <meta property="og:url" content="https://yaba.com/" />
        <meta property="og:site_name" content="yaba" />
        <meta property="og:image" content="{!! asset('yaba_community_logo.png') !!}" />
        <meta property="og:image:alt" content="yaba" />
        <title>{{ config('app.name', 'Yaba') }}</title>
        <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        
        <script>
            $(document).ready(function() {
                checkAppStatus();
            });
            function checkAppStatus() {
                @php
                if ($data['browser'] == 'android') {
                    $defferLink = env('ANDROID_STORE_LINK');
                    $appLink = env('ANDROID_APP_DEST_LINK').$data['param'];
                } elseif ($data['browser'] == 'ios') {
                    $defferLink = env('IOS_STORE_LINK');
                    $appLink = env('IOS_APP_DEST_LINK').$data['param'];
                } else {
                    if($data['os'] == "Mac") {
                        $appLink = env('IOS_STORE_LINK');
                        $defferLink = env('IOS_STORE_LINK');
                    } else {
                        $appLink = env('ANDROID_STORE_LINK');
                        $defferLink = env('ANDROID_STORE_LINK');    
                    }
                }
                @endphp

                @if(!empty($appLink) && !empty($defferLink))
                    window.location.href = " <?php echo $appLink; ?> ";
                    setTimeout(function () {
                        window.location = "<?php echo $defferLink; ?>";
                    }, 5000);
                @endif
            }
        </script>
    </head>
<body>
</body>
</html>