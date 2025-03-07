<?php
session_start();
require_once '../includes/config.php'; // This initializes $pdo
require_once '../includes/functions.php';

// Debug: Check if $pdo is set
if (!isset($pdo)) {
    die("Database connection is not established.");
}

// Pagination settings
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Auth filter
$authFilter = isset($_GET['authorized']) ? $_GET['authorized'] : 'all';
$whereClause = "WHERE role = 'tutor'";
if ($authFilter !== 'all') {
    $isAuthorized = ($authFilter === 'yes') ? 1 : 0;
    $whereClause .= " AND is_authorized = $isAuthorized";
}

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if (!empty($search)) {
    $search = '%' . $search . '%';
    $whereClause .= " AND (reg_number LIKE ? OR email LIKE ?)";
    $whereClause .= " AND (email LIKE ? OR phone LIKE ?)";
}

// Count total tutors
$countQuery = "SELECT COUNT(*) as total FROM users $whereClause";
$countStmt = $pdo->prepare($countQuery); // Use $pdo instead of $conn
if (!empty($search)) {
    $countStmt->execute([$search, $search]);
} else {
    $countStmt->execute();
}
$totalTutors = $countStmt->fetchColumn();
$totalPages = ceil($totalTutors / $limit);

// Get tutors
$query = "
    SELECT u.user_id, u.reg_number, u.email, u.phone, 
           u.is_authorized, u.created_at, 
           (SELECT COUNT(*) FROM tutor_subjects WHERE tutor_id = u.user_id) as subject_count
    FROM users u
    $whereClause
    ORDER BY u.created_at DESC
    LIMIT $limit OFFSET $offset
";
$stmt = $pdo->prepare($query); // Use $pdo instead of $conn
if (!empty($search)) {
    $stmt->execute([$search, $search]);
} else {
    $stmt->execute();
}
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'includes/admin-header.php';
?>

<!-- Rest of your HTML code -->

<main class="admin-main">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Manage Tutors</h1>
            <div class="filter-section">
                <form action="" method="GET" class="filter-form">
                    <div class="form-group search-group">
                        <input type="text" name="search" placeholder="Search by email or phone" 
                               value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <div class="form-group">
                        <select id="authorized" name="authorized" onchange="this.form.submit()">
                            <option value="all" <?= $authFilter === 'all' ? 'selected' : '' ?>>All Tutors</option>
                            <option value="yes" <?= $authFilter === 'yes' ? 'selected' : '' ?>>Authorized</option>
                            <option value="no" <?= $authFilter === 'no' ? 'selected' : '' ?>>Unauthorized</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Reg Number</th>
                                <th>Email</th>
                                <th>Phone</th> 
                                <th>Subjects</th>
                                <th>Registered</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($result)): ?>
                                <?php foreach ($result as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['reg_number']) ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td><?= htmlspecialchars($row['phone']) ?></td> 
                                        <td><?= $row['subject_count'] ?></td>
                                        <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                                        <td>
                                            <span class="status-badge <?= $row['is_authorized'] ? 'status-approved' : 'status-pending' ?>">
                                                <?= $row['is_authorized'] ? 'Authorized' : 'Unauthorized' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="tutor-details.php?id=<?= $row['user_id'] ?>" 
                                                   class="btn btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                    <span class="action-text">View</span>
                                                </a>
                                                <?php if ($row['is_authorized']): ?>
                                                    <form method="POST" action="toggle-authorization.php">
                                                        <input type="hidden" name="tutor_id" value="<?= $row['user_id'] ?>">
                                                        <input type="hidden" name="action" value="revoke">
                                                        <button type="submit" class="btn btn-danger"
                                                                onclick="return confirm('Revoke authorization?')">
                                                            <i class="fas fa-ban"></i>
                                                            <span class="action-text">Revoke</span>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" action="toggle-authorization.php">
                                                        <input type="hidden" name="tutor_id" value="<?= $row['user_id'] ?>">
                                                        <input type="hidden" name="action" value="authorize">
                                                        <button type="submit" class="btn btn-success"
                                                                onclick="return confirm('Authorize this tutor?')">
                                                            <i class="fas fa-check"></i>
                                                            <span class="action-text">Authorize</span>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No tutors found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page-1 ?>&authorized=<?= $authFilter ?>&search=<?= urlencode($search) ?>" 
                               class="page-link">
                                <i class="fas fa-chevron-left"></i> Prev
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>&authorized=<?= $authFilter ?>&search=<?= urlencode($search) ?>" 
                               class="page-link <?= $i === $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page+1 ?>&authorized=<?= $authFilter ?>&search=<?= urlencode($search) ?>" 
                               class="page-link">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/admin-footer.php'; ?>