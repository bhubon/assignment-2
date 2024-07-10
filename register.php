<?php
require_once './common/header.php';


check_login();

$errors = [];

$name = $email = $password = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (empty($_POST['name'])) {
        $errors['name'] = 'Please provide your name';
    } else {
        $name = sanitize($_POST['name']);
    }

    if (empty($_POST['email'])) {
        $errors['email'] = 'Please provide your email';
    } else {
        $email = sanitize($_POST['email']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please provide a valid email address';
        }
    }

    if (empty($_POST['password'])) {
        $errors['password'] = 'Please provide password';

    } elseif (strlen($_POST['password']) < 8) {
        $errors['password'] = 'Please provide password longer then 8 character';

    } elseif ($_POST['password'] !== $_POST['confirm_password']) {
        $errors['confirm_password'] = 'Confirm password does not match';

    } else {
        $password = sanitize($_POST['password']);
        $password = password_hash($password, PASSWORD_DEFAULT);
    }

    if (empty($errors)) {

        $response = register_user($name, $email, $password);

        if ($response == 'exists') {

            $errors['auth_error'] = 'Email already exists';

        } elseif ($response == 'success') {
            flash('success', 'Account registered successfully, Please log in');
            header('Location:login.php');
            exit;

        } else {
            $errors['auth_error'] = 'Something went wrong, please try again!';
        }
    }


}

?>


<main class="">
    <div class="relative flex min-h-screen flex-col justify-center overflow-hidden bg-gray-50 py-6 sm:py-12">
        <img src="./images/beams.jpg" alt=""
            class="absolute top-1/2 left-1/2 max-w-none -translate-x-1/2 -translate-y-1/2" width="1308" />
        <div
            class="absolute inset-0 bg-[url(./images/grid.svg)] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]">
        </div>

        <div class="relative sm:mx-auto sm:max-w-lg sm:rounded-lg sm:px-10">
            <?php
            if (isset($errors['auth_error'])) {
                ?>
                <div class="mt-2 bg-red-500 text-sm text-white rounded-lg p-4" role="alert">
                    <?php echo $errors['auth_error']; ?>
                </div>
                <?php
            }
            ?>
        </div>


        <div
            class="relative bg-white px-6 pt-10 pb-8 shadow-xl ring-1 ring-gray-900/5 sm:mx-auto sm:max-w-lg sm:rounded-lg sm:px-10">
            <div class="mx-auto max-w-xl">
                <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
                    <div class="mx-auto w-full max-w-xl text-center px-24">
                        <h1
                            class="block text-center font-bold text-2xl bg-gradient-to-r from-blue-600 via-green-500 to-indigo-400 inline-block text-transparent bg-clip-text">
                            TruthWhisper</h1>
                    </div>

                    <div class="mt-10 mx-auto w-full max-w-xl">
                        <form class="space-y-6" action="register.php" method="POST">
                            <div>
                                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                                <div class="mt-2">
                                    <input id="name" name="name" type="text" required
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        value="<?php echo $name; ?>">
                                </div>

                                <?php
                                if (isset($errors['name'])) {
                                    ?>
                                    <p class=" text-sm text-red-600 dark:text-red-500"><?php echo $errors['name']; ?>
                                    </p>
                                    <?php
                                }
                                ?>

                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email
                                    address</label>
                                <div class="mt-2">
                                    <input id="email" name="email" type="email" autocomplete="email" required
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        value="<?php echo $email; ?>">
                                </div>
                                <?php
                                if (isset($errors['email'])) {
                                    ?>
                                    <p class=" text-sm text-red-600 dark:text-red-500"><?php echo $errors['email']; ?>
                                    </p>
                                    <?php
                                }
                                ?>
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <label for="password"
                                        class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                                </div>
                                <div class="mt-2">
                                    <input id="password" name="password" type="password" autocomplete="current-password"
                                        required
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <?php
                                if (isset($errors['password'])) {
                                    ?>
                                    <p class=" text-sm text-red-600 dark:text-red-500">
                                        <?php echo $errors['password']; ?>
                                    </p>
                                    <?php
                                }
                                ?>
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <label for="confirm_password"
                                        class="block text-sm font-medium leading-6 text-gray-900">Confirm
                                        Password</label>
                                </div>
                                <div class="mt-2">
                                    <input id="confirm_password" name="confirm_password" type="password" required
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <?php
                                if (isset($errors['confirm_password'])) {
                                    ?>
                                    <p class="text-sm text-red-600 dark:text-red-500">
                                        <?php echo $errors['confirm_password']; ?>
                                    </p>
                                    <?php
                                }
                                ?>
                            </div>

                            <div>
                                <button type="submit"
                                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Register</button>
                            </div>
                        </form>

                        <p class="mt-10 text-center text-sm text-gray-500">
                            Already have an account?
                            <a href="./login.php"
                                class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Login!</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once './common/footer.php';
?>