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
            color: #000000; /* Default icon color */
        }

        /* Hover Effect */
        .bg-blue-100 a svg:hover {
            transform: scale(1.1);
        }

        /* Active Icon Style */
        .clicked-icon svg {
            color: #1E40AF; /* Customize the color for the active state */
        }

        /* Underline for Active Icon */
        .clicked-icon::after {
            content: '';
            position: absolute;
            bottom: -5px; /* Position underline below the icon */
            left: 0;
            right: 0;
            height: 2px;
            background-color: #1E40AF; /* Color of the underline */
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

    <!-- Registration Icon -->
    <a href="registration.php">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5v14m8-7h-2m0 0h-2m2 0v2m0-2v-2M3 11h6m-6 4h6m11 4H4c-.55228 0-1-.4477-1-1V6c0-.55228.44772-1 1-1h16c.5523 0 1 .44772 1 1v12c0 .5523-.4477 1-1 1Z" />
        </svg>
    </a>

    <!-- Patient Icon -->
    <a href="patients.php">
        <svg viewBox="0 0 32 32" fill="currentColor">
            <path d="M9.731,14.075c-1.387,0.252 -2.676,0.921 -3.687,1.932c-1.309,1.309 -2.044,3.084 -2.044,4.935l0,4.039c0,1.657 1.343,3 3,3c4.184,-0 13.816,-0 18,-0c1.657,-0 3,-1.343 3,-3l0,-4.039c0,-1.851 -0.735,-3.626 -2.044,-4.935c-1.011,-1.011 -2.3,-1.68 -3.687,-1.932c0.468,-0.939 0.731,-1.997 0.731,-3.117c0,-3.863 -3.137,-7 -7,-7c-3.863,0 -7,3.137 -7,7c0,1.12 0.263,2.178 0.731,3.117Z" />
            <path d="M20,20.008l-1,-0c-0.552,-0 -1,0.448 -1,1c-0,0.552 0.448,1 1,1l1,-0l0,1c-0,0.552 0.448,1 1,1c0.552,-0 1,-0.448 1,-1l0,-1l1,-0c0.552,-0 1,-0.448 1,-1c-0,-0.552 -0.448,-1 -1,-1l-1,-0l0,-1c-0,-0.552 -0.448,-1 -1,-1c-0.552,-0 -1,0.448 -1,1l0,1Z" />
        </svg>
    </a>

    <!-- Room Icon -->
    <a href="room.php">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 17v2M12 5.5V10m-6 7v2m15-2v-4c0-1.6569-1.3431-3-3-3H6c-1.65685 0-3 1.3431-3 3v4h18Zm-2-7V8c0-1.65685-1.3431-3-3-3H8C6.34315 5 5 6.34315 5 8v2h14Z" />
        </svg>
    </a>

    <!-- Staff Icon -->
    <a href="staff.php">
        <svg viewBox="0 0 64 64" fill="currentColor">
            <path d="M40.067 20.573c0 4.557-3.699 8.25-8.26 8.25c-4.556 0-8.249-3.694-8.249-8.25s3.693-8.25 8.249-8.25c4.561 0 8.26 3.694 8.26 8.25z" />
            <path d="M31.82.524c-3.818 0-9.151 1.522-13.014 5.385l4.588 8.359a10.703 10.703 0 0 1 8.426-4.09c3.459 0 6.537 1.634 8.498 4.175l4.5-8.636C41.475 2.064 35.48.525 31.82.525zm3.4 6.138h-2.136v2.134h-2.566V6.662h-2.136V4.097h2.136V1.954h2.566v2.143h2.136v2.565z" />
            <path d="M20.966 43.651h2.113l-3.018 10.344h23.581l-3.004-10.344h2.115l3.023 10.344h6.939l-4.736-15.672c-.74-2.587-3.984-7.142-9.582-7.28l-12.87-.011c-5.725.028-9.037 4.672-9.786 7.29l-4.828 15.672h7.037l3.016-10.343z" />
            <path d="M.947 57.293h61.73v5.873H.947v-5.873z" />
        </svg>
    </a>
</div>

<!-- JavaScript for Active Icon -->
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
