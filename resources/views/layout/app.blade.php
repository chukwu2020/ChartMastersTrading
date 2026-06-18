<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/public">

    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>CHARTMASTERS CIRCLE</title>

    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/chartmasterlogo1.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/chartmasterlogo1.png') }}" sizes="180x180">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="assets/css/scrollCue.css">
    <link rel="stylesheet" href="assets/css/remixicon.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>

</head>

<body class="bg-white text-neutral-900 !dark:bg-white !dark:text-neutral-900">


    @include('snippets.top-header')
    @include('snippets.header')

    @yield('content')

    @include('snippets.footer')

    <div class="go-top"><i class="ri-arrow-up-fill"></i></div>

    <!-- WhatsApp Widget -->
    <script>
        (function() {
            var options = {
                whatsapp: "+44 (774) 266-3627", 
                call_to_action: "Contact us!",
                position: "left"
            };
            var proto = document.location.protocol,
                host = "getbutton.io",
                url = proto + "//static." + host;
            var s = document.createElement("script");
            s.type = "text/javascript";
            s.async = true;
            s.src = url + "/widget-send-button/js/init.js";
            s.onload = function() {
                WhWidgetSendButton.init(host, proto, options);
            };
            var x = document.getElementsByTagName("script")[0];
            x.parentNode.insertBefore(s, x);
        })();
    </script>

    <script src="assets/js/swiper-bundle.min.js"></script>
    <script src="assets/js/fslightbox.min.js"></script>
    <script src="assets/js/smooth-scroll.js"></script>
    <script src="assets/js/scrollCue.min.js"></script>
    <script src="assets/js/script.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert/dist/sweetalert.min.js"></script>

    @if (session('message'))
    <script>
        swal("Successful!", "{{ session('message') }}!", "success");
    </script>
    @endif

    @if (session('error'))
    <script>
        swal("Error!", "{{ session('error') }}!", "warning");
    </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

   


<!-- Smartsupp Live Chat script -->
<script type="text/javascript">
var _smartsupp = _smartsupp || {};
_smartsupp.key = '55fd755db70fd843852555ef89d50093dad30af9';
window.smartsupp||(function(d) {
  var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
  s=d.getElementsByTagName('script')[0];c=d.createElement('script');
  c.type='text/javascript';c.charset='utf-8';c.async=true;
  c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
})(document);
</script>



<noscript>Powered by <a href="https://chartmasterscircle.com/" target="_blank">ChartmastersCircle</a></noscript>





<style>
    .breadcrumb-item a::after {
    content: none !important;
}
</style>

</body>

</html>