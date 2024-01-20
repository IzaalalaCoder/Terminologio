<nav id="menu">
    <ul>
        <!-- La page de tous les concepts -->
        <li id="concepts">
            <a href="../concept/concepts.php">
                <img src="../../assets/icons/concept.png" alt="Concepts" title="Concepts">
            </a>
        </li>

        <!-- La page de recherche -->
        <li id="search">
            <a href="../general/search.php">
                <img src="../../assets/icons/search.png" alt="Recherche" title="Recherche">
            </a>
        </li>

        <?php if (isset($_SESSION['IDMEM'])) { ?>

            <!-- La page du profil du membre connecté -->
            <?php if (!isset($_SESSION['ISADMIN'])) { ?>
                <li id="profil">
                    <a href="../member/profil.php">
                        <img src="../../assets/icons/profil.png" alt="Profil" title="Profil">
                    </a>
                </li>
            <?php } ?>

            <?php if (isset($_SESSION['ISADMIN'])) { ?>
                <!-- La page de gestion des concepts et de leur traductions -->
                <li id="admin">
                    <a href="../admin/gestionning.php">
                        <img src="../../assets/icons/parametres.png" alt="">
                    </a>
                </li>
            <?php } ?>

            <!-- La page de déconnexion -->
            <li id="logout">
                <a href="../auth/logout.php">
                    <img src="../../assets/icons/logout.png" alt="Déconnexion" title="Déconnexion">
                </a>
            </li>
        <?php } else { ?>
            <!-- La page de connexion -->
            <li id="login">
                <a href="../auth/login.php">
                    <img src="../../assets/icons/login.png" alt="Connexion" title="Connexion">
                </a>
            </li>
        <?php } ?>
    </ul>
</nav>

<script>
    function clickedItem(id) {
        const element = document.getElementById(id);
        if (element == null) {
            return;
        } else {
            element.classList.add('clicked');
        }
    }
</script>
