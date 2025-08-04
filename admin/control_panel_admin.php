<?php
use Parse\ParseException;
use Parse\ParseQuery;
use Parse\ParseUser;

// Fonction pour obtenir le comptage avec plusieurs méthodes de fallback
function getReliableCount($className) {
    try {
        // Méthode 1: Comptage direct (peut échouer sur Back4App)
        $query = new ParseQuery($className);
        
        // ✅ SOLUTION CORRIGÉE : Passer useMasterKey comme paramètre à count()
        if ($className === '_User') {
            $count = $query->count(true); // true = useMasterKey
        } else {
            $count = $query->count();
        }
       
        // Si le comptage semble correct, le retourner
        if ($count > 0) {
            return $count;
        }
       
        // Méthode 2: Compter en récupérant les objets (fallback)
        return getCountByFetching($className);
       
    } catch (ParseException $e) {
        error_log("Erreur comptage direct pour $className: " . $e->getMessage());
       
        // Méthode de fallback
        return getCountByFetching($className);
    }
}

// Méthode alternative : compter en récupérant les objets
function getCountByFetching($className) {
    try {
        $totalCount = 0;
        $limit = 1000;
        $skip = 0;
       
        do {
            $query = new ParseQuery($className);
            $query->limit($limit);
            $query->skip($skip);
           
            // ✅ SOLUTION CORRIGÉE : Passer useMasterKey comme paramètre à find()
            if ($className === '_User') {
                $query->select(['objectId', 'username', 'createdAt']);
                $results = $query->find(true); // true = useMasterKey
            } else {
                $results = $query->find();
            }
            
            $batchCount = count($results);
            $totalCount += $batchCount;
            $skip += $limit;
           
        } while ($batchCount == $limit);
       
        return $totalCount;
       
    } catch (ParseException $e) {
        error_log("Erreur comptage par récupération pour $className: " . $e->getMessage());
        return 0;
    }
}

// ✅ FONCTION CORRIGÉE : Fonction spécifique pour les utilisateurs avec useMasterKey
function getUserCount() {
    try {
        // Approche 1: Utiliser ParseUser directement avec MasterKey
        $query = ParseUser::query();
        $query->select(['objectId']); // Minimiser les données récupérées
       
        // ✅ CORRIGÉ : Passer useMasterKey comme paramètre à count()
        $count = $query->count(true); // true = useMasterKey
       
        if ($count >= 0) { // Changé de > 0 à >= 0 car 0 est une valeur valide
            return $count;
        }
       
        // Approche 2: Si le comptage échoue, récupérer par batch
        return getUserCountByBatch();
       
    } catch (ParseException $e) {
        error_log("Erreur getUserCount: " . $e->getMessage());
        return getUserCountByBatch();
    }
}

function getUserCountByBatch() {
    try {
        $totalUsers = 0;
        $limit = 100; // Limite plus petite pour les utilisateurs
        $skip = 0;
       
        do {
            $query = ParseUser::query();
            $query->limit($limit);
            $query->skip($skip);
            $query->select(['objectId']); // Seulement l'ID pour minimiser la charge
           
            // ✅ CORRIGÉ : Passer useMasterKey comme paramètre à find()
            $users = $query->find(true); // true = useMasterKey
            $batchCount = count($users);
            $totalUsers += $batchCount;
            $skip += $limit;
           
        } while ($batchCount == $limit);
       
        return $totalUsers;
       
    } catch (ParseException $e) {
        error_log("Erreur getUserCountByBatch: " . $e->getMessage());
        return 0;
    }
}

// ✅ FONCTION CORRIGÉE : Fonction pour les utilisateurs enregistrés aujourd'hui
function getTodayRegistrations() {
    try {
        // Calculer la date d'il y a 24h
        $yesterday = new DateTime();
        $yesterday->sub(new DateInterval('P1D'));
       
        $query = ParseUser::query();
        $query->greaterThan('createdAt', $yesterday);
        $query->select(['objectId']);
       
        // ✅ CORRIGÉ : Passer useMasterKey comme paramètre à count()
        $count = $query->count(true); // true = useMasterKey
       
        if ($count >= 0) { // Changé de > 0 à >= 0
            return $count;
        }
       
        // Fallback: récupérer et compter
        $users = $query->find(true); // true = useMasterKey
        return count($users);
       
    } catch (ParseException $e) {
        error_log("Erreur getTodayRegistrations: " . $e->getMessage());
       
        // Fallback avec une approche différente
        try {
            $query = new ParseQuery('_User');
            $query->greaterThanOrEqualToRelativeTime('createdAt', '24 hrs ago');
            $query->select(['objectId']);
            $users = $query->find(true); // true = useMasterKey
            return count($users);
        } catch (ParseException $e2) {
            error_log("Erreur fallback registrations: " . $e2->getMessage());
            return 0;
        }
    }
}

// ✅ FONCTION CORRIGÉE : Obtenir les derniers utilisateurs pour l'affichage du tableau
function getLatestUsers($limit = 10) {
    try {
        $query = ParseUser::query();
        $query->descending('createdAt');
        $query->limit($limit);
        $query->select(['username', 'name', 'avatar', 'gender', 'birthday', 'location', 'createdAt']);
       
        // ✅ CORRIGÉ : Passer useMasterKey comme paramètre à find()
        return $query->find(true); // true = useMasterKey
       
    } catch (ParseException $e) {
        error_log("Erreur getLatestUsers: " . $e->getMessage());
        return [];
    }
}
?>

<!-- ✅ HTML CORRIGÉ : Dashboard avec les corrections -->
<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <div class="col">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Control panel</li>
            </ol>
        </div>
    </div>
   
    <!-- Container fluid -->
    <div class="container-fluid">
        <!-- Start Page Content -->
        <div class="row">
            <!-- Registered Today -->
            <div class="col-md-3">
                <div class="card p-30">
                    <div class="media">
                        <?php
                        $registedToday = getTodayRegistrations();
                        echo '<div class="media-body media-text-left">
                            <h2>'.$registedToday.'</h2>
                            <p class="m-b-0">Registered today</p>
                        </div>';
                        ?>
                        <div class="media-left meida media-middle">
                            <span><i class="fa fa-user-plus f-s-60 <?php echo $default_icon_color;?>"></i></span>
                        </div>
                    </div>
                </div>
            </div>
           
            <!-- Total Users -->
            <div class="col-md-3">
                <div class="card p-30">
                    <div class="media">
                        <?php
                        $totalUsers = getUserCount();
                        echo '<div class="media-body media-text-left">
                            <h2>'.$totalUsers.'</h2>
                            <p class="m-b-0">Total Users</p>
                        </div>';
                       
                        // Debug info amélioré
                        if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
                            echo '<small style="color: blue;">Debug: Total users = '.$totalUsers.'</small>';
                        }
                        ?>
                        <div class="media-left meida media-middle">
                            <span><i class="fa fa-users f-s-60 <?php echo $default_icon_color;?>"></i></span>
                        </div>
                    </div>
                </div>
            </div>
           
            <!-- Messages -->
            <div class="col-md-3">
                <div class="card p-30">
                    <div class="media">
                        <?php
                        $messageCount = getReliableCount("Message");
                        echo '<div class="media-body media-text-left">
                            <h2>'.$messageCount.'</h2>
                            <p class="m-b-0">Messages</p>
                        </div>';
                        ?>
                        <div class="media-left meida media-middle">
                            <span><i class="fa fa-comments-o f-s-60 <?php echo $default_icon_color;?>"></i></span>
                        </div>
                    </div>
                </div>
            </div>
           
            <!-- Videos -->
            <div class="col-md-3">
                <div class="card p-30">
                    <div class="media">
                        <?php
                        $videoCount = getReliableCount("Video");
                        echo '<div class="media-body media-text-left">
                            <h2>'.$videoCount.'</h2>
                            <p class="m-b-0">Videos</p>
                        </div>';
                        ?>
                        <div class="media-left meida media-middle">
                            <span><i class="fa fa-play f-s-60 <?php echo $default_icon_color;?>"></i></span>
                        </div>
                    </div>
                </div>
            </div>
           
            <!-- Streamings -->
            <div class="col-md-3">
                <div class="card p-30">
                    <div class="media">
                        <?php
                        $streamingCount = getReliableCount("Streaming");
                        echo '<div class="media-body media-text-left">
                            <h2>'.$streamingCount.'</h2>
                            <p class="m-b-0">Streamings</p>
                        </div>';
                        ?>
                        <div class="media-left meida media-middle">
                            <span><i class="fa fa-video-camera f-s-60 <?php echo $default_icon_color;?>"></i></span>
                        </div>
                    </div>
                </div>
            </div>
           
            <!-- Challenges -->
            <div class="col-md-3">
                <div class="card p-30">
                    <div class="media">
                        <?php
                        $challengeCount = getReliableCount("Challenge");
                        echo '<div class="media-body media-text-left">
                            <h2>'.$challengeCount.'</h2>
                            <p class="m-b-0">All challenges</p>
                        </div>';
                        ?>
                        <div class="media-left meida media-middle">
                            <span><i class="fa fa-hashtag f-s-60 <?php echo $default_icon_color;?>"></i></span>
                        </div>
                    </div>
                </div>
            </div>
           
            <!-- Categories -->
            <div class="col-md-3">
                <div class="card p-30">
                    <div class="media">
                        <?php
                        $categoryCount = getReliableCount("Category");
                        echo '<div class="media-body media-text-left">
                            <h2>'.$categoryCount.'</h2>
                            <p class="m-b-0">Categories</p>
                        </div>';
                        ?>
                        <div class="media-left meida media-middle">
                            <span><i class="fa fa-hashtag f-s-60 <?php echo $default_icon_color;?>"></i></span>
                        </div>
                    </div>
                </div>
            </div>
           
            <!-- Stories -->
            <div class="col-md-3">
                <div class="card p-30">
                    <div class="media">
                        <?php
                        $storiesCount = getReliableCount("Stories");
                        echo '<div class="media-body media-text-left">
                            <h2>'.$storiesCount.'</h2>
                            <p class="m-b-0">Stories</p>
                        </div>';
                        ?>
                        <div class="media-left meida media-middle">
                            <span><i class="fa fa-history f-s-60 <?php echo $default_icon_color;?>"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
        <div class="row bg-white m-l-0 m-r-0 box-shadow"></div>
       
        <div class="row">
            <div class="col-lg">
                <div class="card">
                    <div class="card-title">
                        <h4>Latest Users</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-dark">
                                <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Avatar</th>
                                    <th>Gender</th>
                                    <th>Birthday</th>
                                    <th>Location</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                // ✅ CORRIGÉ : Utiliser la nouvelle fonction avec useMasterKey
                                $latestUsers = getLatestUsers(10);
                                
                                if (!empty($latestUsers)) {
                                    foreach ($latestUsers as $user) {
                                        echo '<tr>';
                                        
                                        // Name - sécuriser la chaîne
                                        $name = $user->get('name');
                                        echo '<td>' . htmlspecialchars(is_string($name) ? $name : 'N/A') . '</td>';
                                        
                                        // Username - sécuriser la chaîne
                                        $username = $user->get('username');
                                        echo '<td>' . htmlspecialchars(is_string($username) ? $username : 'N/A') . '</td>';
                                       
                                        // Avatar
                                        $avatar = $user->get('avatar');
                                        if ($avatar && method_exists($avatar, 'getURL') && $avatar->getURL()) {
                                            echo '<td><img src="' . $avatar->getURL() . '" width="30" height="30" class="rounded-circle"></td>';
                                        } else {
                                            echo '<td><i class="fa fa-user-circle"></i></td>';
                                        }
                                       
                                        // Gender - sécuriser la chaîne
                                        $gender = $user->get('gender');
                                        echo '<td>' . htmlspecialchars(is_string($gender) ? $gender : 'N/A') . '</td>';
                                        
                                        // Birthday - gérer l'objet DateTime
                                        $birthday = $user->get('birthday');
                                        if ($birthday instanceof DateTime) {
                                            echo '<td>' . htmlspecialchars($birthday->format('Y-m-d')) . '</td>';
                                        } else {
                                            echo '<td>' . htmlspecialchars(is_string($birthday) ? $birthday : 'N/A') . '</td>';
                                        }
                                        
                                        // Location - sécuriser la chaîne
                                        $location = $user->get('location');
                                        echo '<td>' . htmlspecialchars(is_string($location) ? $location : 'N/A') . '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="6">Aucun utilisateur trouvé</td></tr>';
                                }
                                
                                // Debug info
                                if (isset($_GET['debug']) && $_GET['debug'] == 'true') {
                                    echo '<tr><td colspan="6"><small>Debug: ' . count($latestUsers) . ' utilisateurs récupérés</small></td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Content -->
    </div>
    <!-- End Container fluid -->
</div>