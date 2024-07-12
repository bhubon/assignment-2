<?php

function dd($variable = []) {
    echo '<pre>';
    var_dump($variable);
    echo '</pre>';
}

function sanitize($variable) {
    return htmlspecialchars(stripslashes(trim($variable)));
}

function flash($key, $message = null) {
    if ($message) {
        $_SESSION['flash'][$key] = $message;

    } elseif (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);

        return $message;
    }
}

function check_login() {
    if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
        header('Location:dashboard.php');
        exit();
    }
}

function check_no_auth() {
    if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
        header('Location:login.php');
        exit();
    }
}

function check_auth() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']) ? true : false;
}

function logout_user() {
    session_destroy();
    header('Location:index.php');
}



function unique_token($userId, $username) {

    $data = $username . bin2hex(random_bytes(5));
    $uniqueToken = hash('sha256', $data);
    $uniqueToken = substr($uniqueToken, 0, 10);

    return $uniqueToken;
}



function register_user($name, $email, $password) {

    $filename = "inc/db.txt";

    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);


    // Check if email already exists
    foreach ($lines as $line) {
        list($existing_name, $existing_email, $existing_password, $url_token) = explode("|", $line);
        if ($existing_email == $email) {
            return 'exists'; // Email already exists
        }
    }

    $total_user = count($lines);
    $total_user_with_new = $total_user + 1;

    $url_token = unique_token($total_user_with_new, $name);




    $user_data = "$name|$email|$password|$url_token\n";
    if (file_put_contents("inc/db.txt", $user_data, FILE_APPEND)) {
        return 'success';

    } else {
        return 'failed';

    }
}

function login_user($email, $password) {

    $response = [];

    if (!empty($email) || !empty($password)) {
        $users = file("inc/db.txt", FILE_IGNORE_NEW_LINES);
        if (!empty($users)) {
            foreach ($users as $user) {
                list($stored_name, $stored_email, $stored_password, $url_token) = explode("|", $user);

                if ($email == $stored_email && password_verify($password, $stored_password)) {
                    $response = [
                        'valid_user' => true,
                        'user_email' => $stored_email,
                    ];
                    break;
                }


            }
        }
    }

    return $response;
}

function get_user() {
    if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {

        $email = $_SESSION['user'];
        $response = [];

        $users = file("inc/db.txt", FILE_IGNORE_NEW_LINES);
        if (!empty($users)) {
            foreach ($users as $user) {
                list($stored_name, $stored_email, $stored_password, $url_token) = explode("|", $user);

                if ($email == $stored_email) {

                    $user = explode('|', $user);

                    $response = [
                        'user' => $user,
                    ];
                    break;
                }


            }
        }

        return $response;

    } else {
        header('Location:login.php');
        exit;
    }
}

function get_feedback_url_details() {
    if (isset($_GET['user']) && !empty($_GET['user'])) {
        $user_unique_id = sanitize($_GET['user']);

        $response = [];

        $users = file("inc/db.txt", FILE_IGNORE_NEW_LINES);
        if (!empty($users)) {
            foreach ($users as $user) {
                list($stored_name, $stored_email, $stored_password, $url_token) = explode("|", $user);

                if ($user_unique_id == $url_token) {

                    $response = [
                        'user_name' => $stored_name,
                        'unique_url' => $url_token,
                    ];
                    break;
                }


            }
        }

        return $response;
    }
}

function store_feedback($user_url, $feedback) {

    $response = [];

    if (!empty($user_url) || !empty($feedback)) {

        $feedback_data = "$user_url|$feedback\n";
        if (file_put_contents('inc/feedback.txt', $feedback_data, FILE_APPEND)) {
            $response = [
                'status' => 'success',
                'message' => 'Feedback successfully submitted',
            ];
        } else {
            $response = [
                'status' => 'failed',
                'message' => 'Something went wrong. Try again!',
            ];
        }
    } else {
        $response = [
            'status' => 'failed',
            'message' => 'Something went wrong. Try again!',
        ];
    }

    return $response;
}

function get_user_feedback($user_unique_id) {
    $user_feedback = [];

    $stored_feedback = file("inc/feedback.txt", FILE_IGNORE_NEW_LINES);
    if (!empty($stored_feedback)) {
        foreach ($stored_feedback as $item) {
            list($unique_id, $feedback_detail) = explode("|", $item);

            if ($unique_id == $user_unique_id) {
                array_push($user_feedback, $item);
            }


        }
    }

    return $user_feedback;

}