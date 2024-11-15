<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar with Active Icons</title>
    <style>
        /* Sidebar Styles */
        .bg-blue-100 {
            background-color: #E0F2FE;
            height: 100vh;
            width: 8rem;
            position: fixed;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem 0;
        }

        /* Icon Styling */
        .bg-blue-100 a {
            position: relative;
            margin-bottom: 2rem;
        }

        .bg-blue-100 a svg {
            height: 2.5rem;
            width: 2.5rem;
            transition: transform 0.3s ease, color 0.3s ease;
            color: #000000;
            /* Default icon color */
        }

        /* Hover Effect */
        .bg-blue-100 a svg:hover {
            transform: scale(1.1);
        }

        /* Active Icon Style */
        .clicked-icon svg {
            color: #1E40AF;
            /* Customize the color for the active state */
        }

        /* Underline for Active Icon */
        .clicked-icon::after {
            content: '';
            position: absolute;
            bottom: -5px;
            /* Position underline below the icon */
            left: 0;
            right: 0;
            height: 2px;
            background-color: #1E40AF;
            /* Color of the underline */
            border-radius: 1px;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="bg-blue-100">
        <!-- Home Icon -->
        <a href="dashboard.php">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5" />
            </svg>
        </a>

        <!-- Patient Icon -->
        <a href="patients.php">
            <svg viewBox="0 0 32 32" fill="currentColor">
                <path d="M9.731,14.075c-1.387,0.252 -2.676,0.921 -3.687,1.932c-1.309,1.309 -2.044,3.084 -2.044,4.935l0,4.039c0,1.657 1.343,3 3,3c4.184,-0 13.816,-0 18,-0c1.657,-0 3,-1.343 3,-3l0,-4.039c0,-1.851 -0.735,-3.626 -2.044,-4.935c-1.011,-1.011 -2.3,-1.68 -3.687,-1.932c0.468,-0.939 0.731,-1.997 0.731,-3.117c0,-3.863 -3.137,-7 -7,-7c-3.863,0 -7,3.137 -7,7c0,1.12 0.263,2.178 0.731,3.117Z" />
                <path d="M20,20.008l-1,-0c-0.552,-0 -1,0.448 -1,1c-0,0.552 0.448,1 1,1l1,-0l0,1c-0,0.552 0.448,1 1,1c0.552,-0 1,-0.448 1,-1l0,-1l1,-0c0.552,-0 1,-0.448 1,-1c-0,-0.552 -0.448,-1 -1,-1l-1,-0l0,-1c-0,-0.552 -0.448,-1 -1,-1c-0.552,-0 -1,0.448 -1,1l0,1Z" />
            </svg>
        </a>


        <!-- registration Icon -->
        <a href="rooms.php" class="mb-8">
            <svg class="h-10 w-10 hover:scale-110 transition transform duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 17v2M12 5.5V10m-6 7v2m15-2v-4c0-1.6569-1.3431-3-3-3H6c-1.65685 0-3 1.3431-3 3v4h18Zm-2-7V8c0-1.65685-1.3431-3-3-3H8C6.34315 5 5 6.34315 5 8v2h14Z" />
            </svg>

        </a>

        <!-- SOAP Icon -->
        <a href="SOAP.php" class="mb-8">
            <svg class="h-10 w-10 hover:scale-110 transition transform duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 4h3a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3m0 3h6m-6 5h6m-6 4h6M10 3v4h4V3h-4Z" />
            </svg>

        </a>
    </div>
    <script>
        // Add event listeners to all sidebar links
        document.querySelectorAll('.bg-blue-100 a').forEach((link) => {
            link.addEventListener('click', function() {
                // Remove 'clicked-icon' class from all links
                document.querySelectorAll('.bg-blue-100 a').forEach((btn) => btn.classList.remove('clicked-icon'));

                // Add 'clicked-icon' class to the clicked link
                this.classList.add('clicked-icon');
            });

            // Check if the current URL matches the href of the link
            if (window.location.href.indexOf(link.href) !== -1) {
                // If the current page URL matches the link href, apply the 'clicked-icon' class
                link.classList.add('clicked-icon');
            }
        });
    </script>

</body>

</html>