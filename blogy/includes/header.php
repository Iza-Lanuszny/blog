<div class="header__nav">
        <div class="header__name">
            <img src="assets/logo.png" alt="logo" />
            <h1>Blogily</h1>
        </div>
        <div class="header__menu">
    <ul>
        <li><a href="others/menu/game/list.php">Game</a></li>
        <li><a href="others/menu/post/post.php">Posts</a></li>
        <?php include "others/menu/admin.php";  ?>

        <?php if (isset($_SESSION['username'])): ?>
            <li class="user-info">Hi, <b><?php echo $_SESSION['username']; ?></b>!</li>
            <li><a href="others/menu/logout.php">Log out</a></li>
        <?php else: ?>
            <li><a href="others/menu/login.php">Log in</a></li>
        <?php endif; ?>

    </ul>
</div>