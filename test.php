<?php

/**
 * Plugin Name: Notification Card Giant
 * Description: Exibe um card de notificação após 15 segundos na página.
 * Version: 1.0
 * Author: Giant Propeller
 */

// You shall not pass
if (!defined('ABSPATH')) {
    exit;
}

session_start();

if (!isset($_SESSION['selected'])) {
    $_SESSION['selected'] = rand(120, 129);
}


define('NOTIFICATION_CARD_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once plugin_dir_path(__FILE__) . 'includes/notification-card-enqueues.php';

new Notification_card_enqueues();

// This plugin prints on the screen a notification card after 15 seconds
// The notification card is a div with a message and a close button
// The notification card is hidden by default and is shown after 15 seconds
// It closes when the user clicks on the close button
// or after 10 seconds

add_action('wp_enqueue_scripts', function () {
    // Adiciona o CSS diretamente no <head>
    add_action('wp_head', function () {
?>
        <style>
            #notification-card {
            background : #fff;
                position: fixed;
                bottom: 20px;
                left: 20px;
                width: 300px;

                padding: 20px;
                color: black;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
                border-radius: 8px;
                z-index: 9999;
                font-family: Arial, sans-serif;
                display: flex;
                visibility: hidden;
                opacity: 0;
                transform: translateX(-100%);
                transition: transform 0.5s ease-out, opacity 0.5s ease-out, visibility 0.5s;
            }

            @keyframes slideRight {
                from {
                    transform: translateX(-100%);
                    opacity: 0;
                }

                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes slideLeft {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }

                to {
                    transform: translateX(-100%);
                    opacity: 0;
                }
            }

            #notification-card.show {
                animation: slideRight 0.5s forwards;
            }

            #notification-card.hide {
                animation: slideLeft 0.5s forwards;
            }

            #notification-card button {
                background: none;
                border: none;
                font-size: 1.5rem;
                cursor: pointer;
                position: absolute;
                top: 10px;
                right: 10px;
            }

            .ng-right {
                width: inherit;
                padding-right: 10px;
                padding-left: 10px;
            }

            .ng-title {
                width: max-content;
                font-size: 0.5em;
                color: grey;

                background-color: white;
            }

            .ng-user-location {
                display: flex;
                align-items: flex-end;
                justify-content: center;


            }

            .ng-msg {
                font-size: 0.9rem;
                max-width: -webkit-fillavailable;
                text-overflow: inherit;
                margin-bottom: 6px;

            }

            .ng-user {

                font-size: 0.7rem;
                font-weight: bold;
                margin: 0 5px;

            }

            .ng-location {
                font-size: 0.7rem;
                margin: 0 5px;
                font-style: italic;

            }

            .ng-date {
                display: flex;
                align-self: flex-end;
                justify-content: flex-end;
                font-size: 0.6rem;
                color: grey;
            }

            .ng-img {
                border-radius: 5px;
                width: 50px;
            }

            .ng-selected {
                font-size: 1rem;
                color: grey;

            }
        </style>
    <?php
    });

    // Adiciona o HTML e JavaScript no rodapé
    add_action('wp_footer', function () {
        //make an array with star trek and star wars quotes
        $quotes = [];
        $selected = $_SESSION['selected'];
    ?>
        <div id="notification-card" class="show">
            <button id="close-notification"><?php echo esc_html('&times;'); ?></button>
            <div class="ng-left">
                <img class="ng-img" id="notification-img" src="<?php echo NOTIFICATION_CARD_PLUGIN_URL . 'assets/images/regency_logo.png'; ?>" alt="Regency Logo">
            </div>

            <div class="ng-right">
                <div class="ng-title" id="notification-title"></div>
                <!-- <div class="ng-selected" id="notification-selected"></div> -->
                <div class="ng-msg" id="notification-msg"></div>
                <div class="ng-user-location">
                    <div class="ng-user" id="notification-user"></div>
                    <div class="ng-location" id="notification-location"></div>
                </div>
                <div class="ng-date" id="notification-date"></div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const notificationCard = document.getElementById('notification-card');
                const closeButton = document.getElementById('close-notification');
                const notificationImg = document.getElementById('notification-img');
                const notificationTitle = document.getElementById('notification-title');
                const notificationMsg = document.getElementById('notification-msg');
                const notificationUser = document.getElementById('notification-user');
                const notificationDate = document.getElementById('notification-date');
                const notificationLocation = document.getElementById('notification-location');

                // const notificationSelected = document.getElementById('notification-selected');
                let notificationTimeout;
                let selected = <?php echo $selected; ?>;
                let reset_interval = 180000

                function getRandomMessage() {
                    const messages = [{
                            msg: 'Regency made getting a home warranty a breeze! Their service is top-notch, and the price was great.',
                            user: 'Sarah M.',
                        },
                        {
                            msg: 'Quick and reliable service like Regency Total Warranty makes dealing with repairs a breeze.',
                            user: 'Becky P.',
                        },
                        {
                            msg: 'I’ve never experienced such availability and prompt service. Regency is a game-changer!',
                            user: 'John D.',
                        },
                        {
                            msg: 'Consistent and reliable. I have used them for many years, and they are consistent in providing service.',
                            user: 'Randy B.'
                        },
                        {
                            msg: 'Great service, great service. The resolution was fast and easy to schedule.',
                            user: 'Brian P',
                        },
                        {
                            msg: 'Exceptional service! The plumber who fixed our problem in the bathroom was very professional and didn’t stop working on the problem until he fixed it!',
                            user: 'Jason W.',
                        },
                        {
                            msg: 'Very happy with your service, professional and efficient. Repair technician is always on time, and they get my problem solved!',
                            user: 'Tom W.',
                        }


                    ];
                    return messages[Math.floor(Math.random() * messages.length)];
                }

                function showNotification() {
                    // Update the data first
                    const message = getRandomMessage();
                    notificationMsg.textContent = message.msg;
                    notificationUser.textContent = message.user;
                    notificationDate.textContent = `${message.days} days ago`;
                    notificationLocation.textContent = message.location;
                    // notificationSelected.textContent = `${selected} customers Selected regency total today`;
                    // Show the card only after all elements are in place
                    notificationCard.classList.remove('hide');
                    notificationCard.classList.add('show');
                    notificationCard.style.visibility = 'visible';
                    notificationTimeout = setTimeout(hideNotification, 5000); // Hide after 5 seconds
                }

                function hideNotification() {
                    notificationCard.classList.remove('show');
                    notificationCard.classList.add('hide');
                    setTimeout(() => {
                        notificationCard.style.visibility = 'hidden';
                    }, 500); // Match the duration of the transition
                }

                // Initial delay of 5 seconds before showing the notification
                setTimeout(function() {
                    showNotification();

                    // Repeat the process every 10 seconds
                    setInterval(function() {
                        showNotification();
                    }, 10000); // 5 seconds visible + 5 seconds hidden

                    // Increment the selected variable every 20 seconds
                    setInterval(function() {
                        selected++;
                        // Update the session variable via AJAX
                        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'action=update_selected&selected=' + selected,
                        });
                    }, 20000); // 20 seconds interval

                    // Reset the selected variable every 30 seconds
                    setInterval(function() {
                        selected = 120;
                        // Reset the session variable via AJAX
                        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'action=reset_selected',
                        });
                    }, 180000); // 3 minutes interval
                }, 5000);

                // Manually close the notification
                closeButton.addEventListener('click', function() {
                    clearTimeout(notificationTimeout);
                    hideNotification();
                });
            });
        </script>
<?php
    });
});

add_action('wp_ajax_update_selected', 'update_selected');
add_action('wp_ajax_nopriv_update_selected', 'update_selected');

function update_selected()
{
    session_start();
    if (isset($_POST['selected'])) {
        $_SESSION['selected'] = intval($_POST['selected']);
    }
    wp_die();
}

// Add AJAX handler to reset the session variable
add_action('wp_ajax_reset_selected', 'reset_selected');
add_action('wp_ajax_nopriv_reset_selected', 'reset_selected');

function reset_selected()
{
    session_start();
    $_SESSION['selected'] = 120;
    wp_die();
}