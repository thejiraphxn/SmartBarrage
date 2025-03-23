<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Smart Barrage</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php">Dashboard</a>
        </li>
        <?php
            if(isset($_SESSION['loggedin'])){?>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">ออกจากระบบ</a>
                </li>
        <?php }
        ?>
        <?php
            if(!isset($_SESSION['loggedin'])){?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">เข้าสู่ระบบ</a>
                </li>
        <?php }
        ?>
      </ul>
    </div>
  </div>
</nav>