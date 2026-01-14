<?php

session_start();

include "db-conn.php";

if (!isset($_SESSION['username'])) {
	header("location:login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>My Daily Journal | Admin</title>
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
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
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
		body {
			display: flex;
			flex-direction: column;
			min-height: 100vh;
		}
		#content {
			flex: 1;
		}
	</style>
</head>
<body class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300">
	<!-- nav begin -->
	<nav class="bg-gray-50 dark:bg-gray-900 shadow-sm fixed top-0 w-full z-50 py-3 transition-colors duration-300">
		<div class="container mx-auto px-4">
			<div class="flex items-center justify-between">
				<a class="text-xl font-bold text-gray-900 dark:text-gray-100" href="." target="_blank">My Daily Journal</a>

				<div class="flex items-center gap-4">
					<button class="md:hidden px-3 py-2" id="menuToggle">
						<i class="bi bi-list text-2xl"></i>
					</button>

					<button
						class="px-3 py-2 rounded-full border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
						id="themeToggle"
					>
						<i id="themeIcon" class="bi bi-moon-stars"></i>
					</button>

					<ul class="hidden md:flex gap-6 items-center">
						<li>
							<a class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors" href="index.php">
								<i class="bi bi-globe mr-1"></i>Website
							</a>
						</li>
						<li>
							<a class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors" href="admin.php?page=dashboard">Dashboard</a>
						</li>
						<li>
							<a class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors" href="admin.php?page=article">Article</a>
						</li>
						<li>
							<a class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors" href="admin.php?page=gallery">Gallery</a>
						</li>
						<li>
							<a class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors" href="admin.php?page=user">User</a>
						</li>
						<li class="relative group">
							<button class="flex items-center gap-1 text-red-500 font-bold hover:text-red-600 transition-colors" id="dropdownBtn">
								<?= $_SESSION['username'] ?>
								<i class="bi bi-chevron-down text-sm"></i>
							</button>
							<ul id="dropdownMenu" class="hidden absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-2">
								<li>
									<a class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" href="logout.php">
										<i class="bi bi-box-arrow-right mr-2"></i>Logout
									</a>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</div>

			<!-- Mobile Menu -->
			<ul id="mobileMenu" class="hidden md:hidden mt-4 space-y-2 text-center">
				<li>
					<a class="block py-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" href="index.php" target="_blank">
						<i class="bi bi-globe mr-1"></i>Website
					</a>
				</li>
				<li>
					<a class="block py-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" href="admin.php?page=dashboard">Dashboard</a>
				</li>
				<li>
					<a class="block py-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" href="admin.php?page=article">Article</a>
				</li>
				<li>
					<a class="block py-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" href="admin.php?page=gallery">Gallery</a>
				</li>
				<li>
					<a class="block py-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" href="admin.php?page=user">User</a>
				</li>
				<li>
					<span class="block py-2 text-red-500 font-bold"><?= $_SESSION['username'] ?></span>
				</li>
				<li>
					<a class="block py-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" href="logout.php">Logout</a>
				</li>
			</ul>
		</div>
	</nav>
	<!-- nav end -->

	<!-- content begin -->
	<section id="content" class="pt-24 pb-12 px-4">
		<div class="container mx-auto">
			<?php
			if (isset($_GET['page'])) {
				$allowed_pages = ['dashboard', 'article', 'gallery', 'user'];
				$page = $_GET['page'];

				if (in_array($page, $allowed_pages) && file_exists($page . ".php")) {
					include($page . ".php");
				} else {
					?>
					<div class="bg-red-50 dark:bg-red-900/30 rounded-xl p-6 border border-red-200 dark:border-red-800">
						<p class="text-red-600 dark:text-red-400">
							<i class="bi bi-exclamation-triangle mr-2"></i>Halaman tidak ditemukan!
						</p>
					</div>
					<?php
				}
			} else {
				include("dashboard.php");
			}
			?>
		</div>
	</section>
	<!-- content end -->

	<!-- footer begin -->
	<footer class="text-center py-6 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
		<div class="mb-2">
			<a href="https://www.instagram.com/nafhx7" class="inline-block mx-2 text-gray-600 dark:text-gray-400 hover:text-blue-500 transition-colors">
				<i class="bi bi-instagram text-2xl"></i>
			</a>
			<a href="https://github.com/rsyzee" class="inline-block mx-2 text-gray-600 dark:text-gray-400 hover:text-blue-500 transition-colors">
				<i class="bi bi-github text-2xl"></i>
			</a>
			<a href="https://t.me/zxscyf" class="inline-block mx-2 text-gray-600 dark:text-gray-400 hover:text-green-500 transition-colors">
				<i class="bi bi-telegram text-2xl"></i>
			</a>
		</div>
		<div class="text-gray-500 dark:text-gray-400 text-sm">
			Ahmad Rassya Annafi'ulhaq &copy; 2025. All rights reserved.
		</div>
	</footer>
	<!-- footer end -->

	<script>
		const themeToggle = document.getElementById("themeToggle");
		const themeIcon = document.getElementById("themeIcon");
		const menuToggle = document.getElementById("menuToggle");
		const mobileMenu = document.getElementById("mobileMenu");
		const dropdownBtn = document.getElementById("dropdownBtn");
		const dropdownMenu = document.getElementById("dropdownMenu");

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

		menuToggle.addEventListener("click", function () {
			mobileMenu.classList.toggle("hidden");
		});

		dropdownBtn.addEventListener("click", function () {
			dropdownMenu.classList.toggle("hidden");
		});

		document.addEventListener("click", function (e) {
			if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
				dropdownMenu.classList.add("hidden");
			}
		});
	</script>
</body>
</html>
