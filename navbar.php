<!-- <div class="navbar">
    <div class="heading">
        <a href="home.php" class= "heading">Social Networking</a>
    </div>

    <div class="search_bar">
        <form action= "search_page.php" method="GET">
            <input type="text" name="search" placeholder="Search">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>

    <div class="navlinks">
        
        <a href="user.php"><i class="fa-solid fa-user"></i></a>
        <a href="settings.php"><i class="fa-solid fa-gear"></i></a>
        <a href="logout.php">Logout</a>
    </div>

</div> -->


<div class="navbar-top">
      <div class="logo-search-bar">
        <div class="logo-image">
          <a href="home-page.php">
          <img src="images/logo.jpg" alt="" class="logo" />
          </a>
          
        </div>

        <div class="search-bar">
        <form action= "get-users.php" method="GET">
          <input
            type="text"
            class="search-input"
            name="search-input"
            placeholder="# Find more people ✌️"
          />
          </form>
        </div>
      </div>

      <div class="logout-button">
        <button class="logout"> <a class ="logout-link"href="logout.php">Logout</a></button>
      </div>
    </div>