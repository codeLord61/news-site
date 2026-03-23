<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?= \app\core\App::assetPath('css/styles.css') ?>">
    <script>
        window.appBaseUrl = "<?= \app\core\App::$PROJECT_ROOT_URL ?>";
    </script>
</head>

<body class="min-h-screen flex">
    <div class="grid grid-cols-1 md:grid-cols-2 w-full">
        <!-- Image -->
        <div class="md:block hidden">
            <div class="h-full bg-cover bg-[image:var(--image-login)]"></div>
        </div>
        <!-- Form -->
        <div class="flex justify-center items-center md:p-10 p-6">
            <!-- Form container -->
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                    <a href="<?= url('/') ?>" class="text-3xl font-black text-black hover:opacity-80 transition-opacity">Packly News</a>
                </div>

                <!-- Signup form -->
                <!-- Updated form action to map to our api url over HTTP, but frontend usually uses JS fetch. For now just ID is fine. -->
                <form id="signupForm" action="<?= url('/api/v1/register') ?>" method="post" class="space-y-6 auth-form ">
                    <div class="text-center">
                        <!-- Title -->
                        <h1 class="md:text-4xl text-3xl font-bold text-primary-600">
                            Create Account
                        </h1>
                    </div>
                    <!-- Container for form inputs -->
                    <div class="space-y-5">
                        <div>
                            <label for="fullname" class="block text-sm font-medium text-neutral-700 mb-1.5">Full
                                Name</label>
                            <input type="text"
                                class="border border-neutral-300 w-full px-4 py-3 rounded-lg  focus:ring-2 focus:ring-primary-200 outline-none transition"
                                placeholder="Your name.." required id="fullname" name="fullname">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-neutral-700 mb-1.5">Email</label>
                            <input type="email"
                                class="border border-neutral-300 w-full px-4 py-3 rounded-lg focus:ring-2 focus:ring-primary-200 outline-none transition"
                                placeholder="johndoe@gmail.com" required id="email" name="email">
                        </div>
                        <div class="relative">
                            <label for="signupPassword"
                                class="block text-sm font-medium text-neutral-700 mb-1.5">Password</label>
                            <input type="password" id="signupPassword"
                                class="border border-neutral-300 w-full px-4 py-3 rounded-lg focus:ring-2 focus:ring-primary-200 outline-none transition pr-11"
                                placeholder="••••••••" required name="password">
                            <button type="button"
                                class="absolute right-4 top-10 text-neutral-500 hover:text-primary-600 cursor-pointer"
                                onclick="togglePassword('signupPassword', 'signupEye')"><i id="signupEye"
                                    class="fa-solid fa-eye"></i>
                            </button>
                        </div>

                    </div>

                    <!-- Signup button -->
                    <button type="submit"
                        class="w-full bg-primary-600 text-white py-3 rounded-lg hover:bg-primary-700 font-bold transition cursor-pointer"
                        name="submit">
                        Sign Up
                    </button>

                    <!-- Switch to login -->
                    <p class="text-sm text-center text-neutral-600 mt-6">
                        Already have an account ?
                        <button type="button" class="text-primary-600 font-medium hover:text-primary-700 cursor-pointer"
                            onclick="switchForm('login')">Log in</button>
                    </p>
                </form>

                <!-- Login form -->
                <form id="loginForm" method="post" action="<?= url('/api/v1/login') ?>" class="space-y-6 auth-form hidden">
                    <div class="text-center mb-8">
                        <!-- Title -->
                        <h1 class="md:text-4xl text-3xl font-bold text-primary-600">
                            Log in
                        </h1>
                        <!-- Sub-title -->
                        <p class="mt-2 text-neutral-600 text-sm md:text-base">Welcome back!</p>
                    </div>
                    <!-- Container for form inputs -->
                    <div class="space-y-5">
                        <div>
                            <label for="email" class="block text-sm font-medium text-neutral-700 mb-1.5">Email</label>
                            <input type="email"
                                class="border border-neutral-300 w-full px-4 py-3 rounded-lg focus:ring-2 focus:ring-primary-200 outline-none transition"
                                placeholder="johndoe@gmail.com" required id="email" name="email">
                        </div>
                        <div class="relative">
                            <label for="loginPassword"
                                class="block text-sm font-medium text-neutral-700 mb-1.5">Password</label>
                            <input type="password" id="loginPassword"
                                class="border border-neutral-300 w-full px-4 py-3 rounded-lg focus:ring-2 focus:ring-primary-200 outline-none transition pr-11"
                                placeholder="••••••••" required name="password">
                            <button type="button"
                                class="absolute right-4 top-10 text-neutral-500 hover:text-primary-600 cursor-pointer"
                                onclick="togglePassword('loginPassword', 'loginEye')"><i id="loginEye"
                                    class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Login button -->
                    <button type="submit"
                        class="w-full bg-primary-600 text-white py-3 rounded-lg hover:bg-primary-700 font-bold transition cursor-pointer"
                        name="submit">
                        Log In
                    </button>

                    <!-- Switch to Signup -->
                    <p class="text-sm text-center text-neutral-600 mt-6">
                        No account yet?
                        <button type="submit" class="text-primary-600 font-medium hover:text-primary-700 cursor-pointer"
                            onclick="switchForm('signup')">Sign up</button>
                    </p>
                </form>
            </div>
        </div>
    </div>
    <script src="<?= \app\core\App::assetPath('js/auth.js') ?>"></script>
</body>

</html>