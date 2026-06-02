<!DOCTYPE html>
<html lang="en">

<head>



    <base href="/public">
    <meta charset="UTF-8">
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title -->
    <!-- Title -->
    <title>CHARTMASTERS CIRCLE</title>
    <!-- 5star color -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Favicons for different devices -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/chartmasterlogo1.png') }}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/chartmasterlogo1.png') }}" sizes="192x192">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/chartmasterlogo1.png') }}" sizes="512x512">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/images/chartmasterlogo1.png') }}" sizes="180x180">


    <!-- Add this to your main Blade layout file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin_assets/css/remixicon.css">
    <!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> -->

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('admin_assets/css/lib/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/lib/dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/lib/editor-katex.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/lib/editor.atom-one-dark.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/lib/editor.quill.snow.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/lib/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/lib/full-calendar.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/lib/jquery-jvectormap-2.0.5.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/lib/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/lib/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/lib/prism.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/lib/file-upload.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/lib/audioplayer.css') }}">
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Custom Style -->
    <link rel="stylesheet" href="{{ asset('admin_assets/css/style.css') }}">

    <style>
        html,
        body {
            color: #0C3A30 !important;
        }

        div,
        * {
            color: #0C3A30 !important;
        }
    </style>


</head>

<body class="min-h-screen flex flex-col ">

    @include('snippets.user_sidebar')

    <main class="dashboard-main pt-20 flex-grow">
        @include('snippets.user_header')

        @yield('content')

        <!-- language names stay in English -->
        <style>
            .goog-te-menu-value span {
                display: none !important;
            }

            .goog-te-menu-value:before {
                content: "🌐 Language" !important;
            }

            .goog-te-menu2 {
                max-height: 500px !important;
                overflow-y: auto !important;
            }

            .goog-te-menu2-item div,
            .goog-te-menu2-item span {
                font-family: Arial, sans-serif !important;
                color: #333 !important;
                font-size: 13px !important;
            }
        </style>

        <!-- Custom language Styles -->
        <style>
            .languageTranslateWrapper {
                position: fixed;
                bottom: 0.75rem;
                left: 0.65rem;
                z-index: 9999;
            }

            .languageTranslate {
                background: none;
                border: none;
                border-radius: 6px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
                max-width: 150px;
                display: flex;
                align-items: center;
            }

            .goog-logo-link,
            .goog-te-gadget img,
            .VIpgJd-ZVi9od-l4eHX-hSRGPd {
                display: none !important;
            }

            .goog-te-gadget {
                color: transparent !important;
            }

            .goog-te-gadget .goog-te-combo {
                width: 100% !important;
                color: #0C3A30 !important;
                padding: 6px 8px !important;
                border-radius: 4px !important;
                font-size: 13px !important;
                font-weight: 500;
                border: 2px solid #8bc905;
                appearance: none;
                cursor: pointer;
                background-image: url('data:image/svg+xml;charset=UTF-8,<svg fill="%238bc905" height="16" viewBox="0 0 24 24" width="16" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
                background-repeat: no-repeat;
                background-position: right 8px center;
                background-size: 14px;
            }

            .goog-te-banner-frame.skiptranslate {
                display: none !important;
            }

            body>.skiptranslate {
                display: none !important;
            }

            body {
                top: 0px !important;
            }


            .goog-te-combo:focus {
                outline: none;
                border-color: #0C3A30 !important;
            }

            .verified-icon svg {
                width: 20px;
                height: 20px;
                color: #8bc905;
            }

            .custom-hover-deposit {
                background-color: transparent;
                color: #0C3A30;
                font-weight: 700;
                border-radius: 0.5rem;
                transition: all 0.1s ease;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                background-color: none;
            }

            .custom-hover-deposit:hover {
                background-color: #9EDD05 !important;
                color: #0C3A30;
            }
        </style>




        <footer class="d-footer mt-8" style="background-image: url('/assets/images/hero/hero-image-1.svg'); background-position:center; background-size:cover; bottom:0; left:0; right:0;">

            <div class="flex items-center justify-between gap-3">

                <p class="mb-0">
                    © {{ date('Y') }} <span class="font-semibold text-sm text-[#8bc905]"> ChartMasters Circle</span>. All rights reserved.
                    <span class="mx-2 text-gray-400">|</span>
                    <a href="{{route('termsprivacy')}}" class="hover:text-[#9EDD05] transition-colors duration-200">Privacy Policy</a>
                    <span class="mx-2 text-gray-400">|</span>
                    <a href="{{ route('termsprivacy')}}" class="hover:text-[#9EDD05] transition-colors duration-200">Terms</a>
                </p>
            </div>
        </footer>


    </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('admin_assets/js/lib/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('admin_assets/js/lib/apexcharts.min.js') }}"></script>
    <script src="{{ asset('admin_assets/js/lib/simple-datatables.min.js') }}"></script>
    <script src="{{ asset('admin_assets/js/lib/iconify-icon.min.js') }}"></script>
    <script src="{{ asset('admin_assets/js/lib/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('admin_assets/js/lib/jquery-jvectormap-2.0.5.min.js') }}"></script>
    <script src="{{ asset('admin_assets/js/lib/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('admin_assets/js/lib/magnifc-popup.min.js') }}"></script>
    <script src="{{ asset('admin_assets/js/lib/slick.min.js') }}"></script>
    <script src="{{ asset('admin_assets/js/lib/prism.js') }}"></script>
    <script src="{{ asset('admin_assets/js/lib/file-upload.js') }}"></script>
    <script src="{{ asset('admin_assets/js/lib/audioplayer.js') }}"></script>
    <script src="{{ asset('admin_assets/js/flowbite.min.js') }}"></script>
    <script src="{{ asset('admin_assets/js/app.js') }}"></script>
    <script src="{{ asset('admin_assets/js/homeOneChart.js') }}"></script>

    <!-- SweetAlert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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
    @if (Session::has('success'))
    <script>
        swal("Successful!", "{{ Session::get('success') }}!", "success");
    </script>
    @endif
    @if (Session::has('error'))
    <script>
        swal("Error!", "{{ Session::get('error') }}!", "warning");
    </script>
    @endif

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


<!-- Smartsupp Live Chat script -->



<!-- Smartsupp Live Chat script -->
<script type="text/javascript">
var _smartsupp = _smartsupp || {};
_smartsupp.key = '0dda8c93da930491d9a0a2c655be10a8916850e0';
window.smartsupp||(function(d) {
  var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
  s=d.getElementsByTagName('script')[0];c=d.createElement('script');
  c.type='text/javascript';c.charset='utf-8';c.async=true;
  c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
})(document);
</script>

<noscript>Powered by <a href="https://chartmasterscircle.com/" target="_blank">ChartmastersCircle</a></noscript>












    <!-- Replace your current Google Translate script with this: -->
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en'
            }, 'google_translate_element');
        }
    </script>
    <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit&hl=en"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const observer = new MutationObserver(function() {
                const langSelect = document.querySelector('.goog-te-combo');
                if (langSelect) {
                    // Restore saved language
                    const savedLang = localStorage.getItem('selected_lang');
                    if (savedLang && langSelect.value !== savedLang) {
                        langSelect.value = savedLang;
                        langSelect.dispatchEvent(new Event('change'));
                    }

                    // Save language on change
                    langSelect.addEventListener('change', function() {
                        localStorage.setItem('selected_lang', langSelect.value);
                    });

                    observer.disconnect(); // Stop once done
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    </script>




</body>

</html>