<?php
session_start();
include 'db-conn.php';

$error = '';

if (isset($_SESSION['username'])) {
	header("location:admin.php");
	exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = $_POST['user'];
	$password = hash('sha256', $_POST['pass']);

	$stmt = $pdo->prepare("SELECT username FROM user WHERE username = :username AND password = :password");
	$stmt->execute(['username' => $username, 'password' => $password]);
	$user = $stmt->fetch();

	if (!empty($user)) {
		$_SESSION['username'] = $user['username'];
		header("location:admin.php");
		exit();
	} else {
		$error = "Username atau password salah!";
	}

}
?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>Login | My Daily Journal</title>
		<script>
			(function () {
				try {
					var theme = localStorage.getItem('theme') || 'light';
					if (theme === 'dark') {
						document.documentElement.classList.add('dark');
					} else {
						document.documentElement.classList.remove('dark');
					}
				} catch (e) {}
			})();
		</script>
		<script src="https://cdn.tailwindcss.com"></script>
		<link
			href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
			rel="stylesheet"
		/>
		<script>
			tailwind.config = {
				darkMode: "class",
			};
		</script>
		<style>
			.login-card {
				transition: transform 0.3s ease, box-shadow 0.3s ease;
			}
			.login-card:hover {
				transform: translateY(-4px);
			}
		</style>
	</head>
	<body class="bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-950 dark:to-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300 min-h-screen">

		<!-- Background Pattern -->
		<div class="fixed inset-0 opacity-30 dark:opacity-20 pointer-events-none">
			<div class="absolute top-20 left-10 w-72 h-72 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl animate-pulse"></div>
			<div class="absolute top-40 right-10 w-72 h-72 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
			<div class="absolute bottom-20 left-1/2 w-72 h-72 bg-pink-400 rounded-full mix-blend-multiply filter blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
		</div>

		<!-- Theme Toggle -->
		<div class="fixed top-4 right-4 z-50">
			<button
				class="px-3 py-2 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors shadow-md"
				id="themeToggle"
			>
				<i id="themeIcon" class="bi bi-moon-stars"></i>
			</button>
		</div>

		<!-- Back to Home -->
		<div class="fixed top-4 left-4 z-50">
			<a href="index.php" class="flex items-center gap-2 px-4 py-2 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors shadow-md text-sm font-medium">
				<i class="bi bi-arrow-left"></i>
				<span class="hidden sm:inline">Kembali</span>
			</a>
		</div>

		<div class="min-h-screen flex items-center justify-center px-4 py-12 relative z-10">
			<div class="w-full max-w-md">
				<!-- Login Card -->
				<div class="login-card bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">

					<!-- Header -->
					<div class="bg-gradient-to-r from-blue-500 to-purple-600 p-8 text-center">
						<div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl mb-4">
							<i class="bi bi-journal-bookmark-fill text-4xl text-white"></i>
						</div>
						<h1 class="text-2xl font-bold text-white mb-1">Welcome Back!</h1>
						<p class="text-blue-100 text-sm">Login ke My Daily Journal</p>
					</div>

					<!-- Form -->
					<div class="p-8">
						<form action="" method="post" id="loginForm" class="space-y-5">
							<!-- Username Field -->
							<div>
								<label for="user" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
									<i class="bi bi-person mr-2"></i>Username
								</label>
								<div class="relative">
									<input
										type="text"
										name="user"
										id="user"
										class="w-full px-4 py-3 pl-12 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
										placeholder="Masukkan username"
									/>
									<div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
										<i class="bi bi-person-fill text-gray-400"></i>
									</div>
								</div>
							</div>

							<!-- Password Field -->
							<div>
								<label for="pass" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
									<i class="bi bi-lock mr-2"></i>Password
								</label>
								<div class="relative">
									<input
										type="password"
										name="pass"
										id="pass"
										class="w-full px-4 py-3 pl-12 pr-12 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
										placeholder="Masukkan password"
									/>
									<div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
										<i class="bi bi-lock-fill text-gray-400"></i>
									</div>
									<button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
										<i class="bi bi-eye-fill" id="eyeIcon"></i>
									</button>
								</div>
							</div>

							<!-- Error Message -->
							<?php if (!empty($error)): ?>
							<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
								<i class="bi bi-exclamation-circle"></i>
								<span><?= htmlspecialchars($error) ?></span>
							</div>
							<?php endif; ?>

							<div id="errorMsg" class="hidden bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
								<i class="bi bi-exclamation-circle"></i>
								<span id="errorText"></span>
							</div>

							<!-- Login Button -->
							<button
								type="submit"
								class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2"
							>
								<i class="bi bi-box-arrow-in-right"></i>
								Login
							</button>
						</form>

						<!-- Divider -->
						<div class="flex items-center my-6">
							<div class="flex-1 border-t border-gray-200 dark:border-gray-700"></div>
							<span class="px-4 text-sm text-gray-500 dark:text-gray-400">atau</span>
							<div class="flex-1 border-t border-gray-200 dark:border-gray-700"></div>
						</div>

						<!-- Back to Home Button -->
						<a href="index.php" class="w-full bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium py-3 px-6 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200 dark:border-gray-600 flex items-center justify-center gap-2">
							<i class="bi bi-house"></i>
							Kembali ke Beranda
						</a>
					</div>
				</div>

				<!-- Footer -->
				<p class="text-center text-gray-500 dark:text-gray-400 text-sm mt-6">
					&copy; 2025 My Daily Journal. All rights reserved.
				</p>
			</div>
		</div>

		<script>
			const themeToggle = document.getElementById("themeToggle");
			const themeIcon = document.getElementById("themeIcon");

			const currentTheme = localStorage.getItem("theme") || "light";
			if (currentTheme === "dark") {
				document.documentElement.classList.add("dark");
			}
			updateThemeIcon();

			themeToggle.addEventListener("click", function () {
				document.documentElement.classList.toggle("dark");
				const newTheme = document.documentElement.classList.contains("dark") ? "dark" : "light";
				localStorage.setItem("theme", newTheme);
				updateThemeIcon();
			});

			function updateThemeIcon() {
				const isDark = document.documentElement.classList.contains("dark");
				themeIcon.className = isDark ? "bi bi-sun-fill" : "bi bi-moon-stars";
			}

			const togglePassword = document.getElementById("togglePassword");
			const passwordInput = document.getElementById("pass");
			const eyeIcon = document.getElementById("eyeIcon");

			togglePassword.addEventListener("click", function() {
				const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
				passwordInput.setAttribute("type", type);
				eyeIcon.className = type === "password" ? "bi bi-eye-fill" : "bi bi-eye-slash-fill";
			});

			document.getElementById("loginForm").addEventListener("submit", function(event) {
				const user = document.getElementById("user").value.trim();
				const pass = document.getElementById("pass").value.trim();
				const errorMsg = document.getElementById("errorMsg");
				const errorText = document.getElementById("errorText");

				errorMsg.classList.add("hidden");

				if (user === "") {
					errorText.textContent = "Username tidak boleh kosong!";
					errorMsg.classList.remove("hidden");
					event.preventDefault();
					return;
				}

				if (pass === "") {
					errorText.textContent = "Password tidak boleh kosong!";
					errorMsg.classList.remove("hidden");
					event.preventDefault();
					return;
				}
			});
		</script>
	</body>
</html>
