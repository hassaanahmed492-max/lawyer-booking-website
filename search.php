<?php
/**
 * Search Lawyers Page
 */
$pageTitle = 'Find Lawyers';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDBConnection();

// Get filter values
$serviceFilter = intval($_GET['service'] ?? 0);
$cityFilter = sanitize($_GET['city'] ?? '');
$nameFilter = sanitize($_GET['name'] ?? '');
$currentPage = max(1, intval($_GET['page'] ?? 1));
$perPage = 9;

// Get services for filter
$servicesStmt = $pdo->query("SELECT * FROM services WHERE status = 1 ORDER BY name");
$services = $servicesStmt->fetchAll();

// Build query
$where = ["lp.is_approved = 1", "u.status = 'active'"];
$params = [];

if ($serviceFilter > 0) {
    $where[] = "ls.service_id = ?";
    $params[] = $serviceFilter;
}

if (!empty($cityFilter)) {
    $where[] = "lp.city = ?";
    $params[] = $cityFilter;
}

if (!empty($nameFilter)) {
    $where[] = "u.name LIKE ?";
    $params[] = '%' . $nameFilter . '%';
}

$whereClause = implode(' AND ', $where);

// Count total results
$countSql = "SELECT COUNT(DISTINCT lp.id) as total 
    FROM lawyer_profiles lp
    JOIN users u ON lp.user_id = u.id
    LEFT JOIN lawyer_services ls ON lp.id = ls.lawyer_id
    WHERE $whereClause";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalItems = $countStmt->fetch()['total'];

$pagination = paginate($totalItems, $currentPage, $perPage);

// Get lawyers
$sql = "SELECT DISTINCT lp.*, u.name, u.email,
    GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ') as service_names
    FROM lawyer_profiles lp
    JOIN users u ON lp.user_id = u.id
    LEFT JOIN lawyer_services ls ON lp.id = ls.lawyer_id
    LEFT JOIN services s ON ls.service_id = s.id
    WHERE $whereClause
    GROUP BY lp.id
    ORDER BY lp.is_featured DESC, lp.rating DESC
    LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$lawyers = $stmt->fetchAll();

// Build base URL for pagination
$paginationUrl = SITE_URL . '/search.php?service=' . $serviceFilter . '&city=' . urlencode($cityFilter) . '&name=' . urlencode($nameFilter);

require_once __DIR__ . '/includes/header.php';
?>

<!-- Search Page Header -->
<div class="search-page-header">
    <div class="container">
        <h1><i class="fas fa-search"></i> Find Lawyers</h1>
        <p>Browse our network of verified legal professionals</p>
    </div>
</div>

<div class="container">
    <div class="search-layout">
        <!-- Filters Sidebar -->
        <aside class="search-filters">
            <form action="" method="GET">
                <div class="filter-section">
                    <h3><i class="fas fa-search"></i> Search by Name</h3>
                    <input type="text" name="name" class="form-control" placeholder="Lawyer name..." value="<?php echo sanitize($nameFilter); ?>">
                </div>

                <div class="filter-section">
                    <h3><i class="fas fa-gavel"></i> Service Type</h3>
                    <select name="service" class="form-control">
                        <option value="0">All Services</option>
                        <?php foreach ($services as $service): ?>
                        <option value="<?php echo $service['id']; ?>" <?php echo $serviceFilter == $service['id'] ? 'selected' : ''; ?>>
                            <?php echo sanitize($service['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-section">
                    <h3><i class="fas fa-map-marker-alt"></i> City</h3>
                    <select name="city" class="form-control">
                        <option value="">All Cities</option>
                        <?php
                        $cities = ['Islamabad','Lahore','Karachi','Rawalpindi','Peshawar','Quetta','Faisalabad','Multan'];
                        foreach ($cities as $city):
                        ?>
                        <option value="<?php echo $city; ?>" <?php echo $cityFilter === $city ? 'selected' : ''; ?>><?php echo $city; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-search"></i> Apply Filters
                </button>

                <?php if ($serviceFilter || $cityFilter || $nameFilter): ?>
                <a href="<?php echo SITE_URL; ?>/search.php" class="btn btn-outline btn-block" style="margin-top: 8px;">
                    <i class="fas fa-times"></i> Clear Filters
                </a>
                <?php endif; ?>
            </form>
        </aside>

        <!-- Results -->
        <div class="search-results">
            <div class="search-results-header">
                <h2>
                    <?php if ($serviceFilter || $cityFilter || $nameFilter): ?>
                        Search Results
                    <?php else: ?>
                        All Lawyers
                    <?php endif; ?>
                </h2>
                <span class="results-count"><?php echo $totalItems; ?> lawyer<?php echo $totalItems !== 1 ? 's' : ''; ?> found</span>
            </div>

            <?php if (empty($lawyers)): ?>
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h3>No Lawyers Found</h3>
                <p>Try adjusting your filters or search criteria.</p>
                <a href="<?php echo SITE_URL; ?>/search.php" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Reset Search
                </a>
            </div>
            <?php else: ?>
            <div class="lawyers-grid">
                <?php foreach ($lawyers as $lawyer): ?>
                <div class="lawyer-card">
                    <div class="lawyer-card-header">
                        <img src="<?php echo getLawyerPhoto($lawyer['photo']); ?>" alt="<?php echo sanitize($lawyer['name']); ?>" class="lawyer-avatar">
                        <div class="lawyer-card-info">
                            <h3>
                                <a href="<?php echo SITE_URL; ?>/lawyer_profile.php?id=<?php echo $lawyer['id']; ?>">
                                    <?php echo sanitize($lawyer['name']); ?>
                                </a>
                            </h3>
                            <?php echo starRating($lawyer['rating']); ?>
                            <div class="lawyer-specialization">
                                <?php 
                                $serviceNames = $lawyer['service_names'] ? explode(', ', $lawyer['service_names']) : [];
                                foreach (array_slice($serviceNames, 0, 2) as $sName): 
                                ?>
                                <span class="spec-tag"><?php echo sanitize($sName); ?></span>
                                <?php endforeach; ?>
                                <?php if (count($serviceNames) > 2): ?>
                                <span class="spec-tag">+<?php echo count($serviceNames) - 2; ?> more</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="lawyer-card-body">
                        <div class="lawyer-card-meta">
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo sanitize($lawyer['city']); ?></span>
                            <span><i class="fas fa-briefcase"></i> <?php echo $lawyer['experience_years']; ?> yrs</span>
                            <span><i class="fas fa-star"></i> <?php echo $lawyer['total_reviews']; ?> reviews</span>
                        </div>
                    </div>
                    <div class="lawyer-card-footer">
                        <div class="lawyer-fee">
                            Rs. <?php echo number_format($lawyer['consultation_fee']); ?> <small>/ session</small>
                        </div>
                        <a href="<?php echo SITE_URL; ?>/lawyer_profile.php?id=<?php echo $lawyer['id']; ?>" class="btn btn-primary btn-sm">View Profile</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php echo renderPagination($pagination, $paginationUrl); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
