<?php
session_start();
require_once '../config/db.php';
require_once 'includes/admin-auth.php';

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
}

// Count total tutors
$countQuery = "SELECT COUNT(*) as total FROM users $whereClause";
$countStmt = $conn->prepare($countQuery);
if (!empty($search)) {
    $countStmt->bind_param("ss", $search, $search);
}
$countStmt->execute();
$totalTutors = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = ceil($totalTutors / $limit);

// Get tutors
$query = "
    SELECT u.user_id, u.reg_number, u.email, u.phone, u.year_of_study, 
           u.is_authorized, u.created_at, 
           (SELECT COUNT(*) FROM tutor_subjects WHERE tutor_id = u.user_id) as subject_count
    FROM users u
    $whereClause
    ORDER BY u.created_at DESC
    LIMIT $limit OFFSET $offset
";
$stmt = $conn->prepare($query);
if (!empty($search)) {
    $stmt->bind_param("ss", $search, $search);
}
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

require_once 'includes/admin-header.php';
?>

<main class="admin-main">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Manage Tutors</h1>
            <div class="filter-section">
                <form action="" method="GET" class="filter-form">
                    <div class="form-group search-group">
                        <input type="text" name="search" placeholder="Search by reg number or email" value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <div class="form-group">
                        <label for="authorized">Authorization:</label>
                        <select id="authorized" name="authorized" onchange="this.form.submit()">
                            <option value="all" <?php echo $authFilter === 'all' ? 'selected' : ''; ?>>All Tutors</option>
                            <option value="yes" <?php echo $authFilter === 'yes' ? 'selected' : ''; ?>>Authorized</option>
                            <option value="no" <?php echo $authFilter === 'no' ? 'selected' : ''; ?>>Unauthorized</option>
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
                                <th>Year</th>
                                <th>Subjects</th>
                                <th>Registered On</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['reg_number']; ?></td>
                                        <td><?php echo $row['email']; ?></td>
                                        <td><?php echo $row['phone']; ?></td>
                                        <td><?php echo $row['year_of_study']; ?></td>
                                        <td><?php echo $row['subject_count']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <?php if ($row['is_authorized']): ?>
                                                <span class="status-badge status-approved">Authorized</span>
                                            <?php else: ?>
                                                <span class="status-badge status-pending">Unauthorized</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center actions-column">
                                            <div class="action-buttons">
                                                <a href="tutor-details.php?id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                
                                                <?php if ($row['is_authorized']): ?>
                                                    <form action="toggle-authorization.php" method="POST" class="d-inline">
                                                        <input type="hidden" name="tutor_id" value="<?php echo $row['user_id']; ?>">
                                                        <input type="hidden" name="action" value="revoke">
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to revoke authorization?')">
                                                            <i class="fas fa-ban"></i> Revoke
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form action="toggle-authorization.php" method="POST" class="d-inline">
                                                        <input type="hidden" name="tutor_id" value="<?php echo $row['user_id']; ?>">
                                                        <input type="hidden" name="action" value="authorize">
                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to authorize this tutor?')">
                                                            <i class="fas fa-check"></i> Authorize
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
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
                            <a href="?page=<?php echo $page - 1; ?>&authorized=<?php echo $authFilter; ?>&search=<?php echo urlencode($search); ?>" class="page-link">
                                <i class="fas fa-angle-left"></i> Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&authorized=<?php echo $authFilter; ?>&search=<?php echo urlencode($search); ?>" 
                               class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&authorized=<?php echo $authFilter; ?>&search=<?php echo urlencode($search); ?>" class="page-link">
                                Next <i class="fas fa-angle-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/admin-footer.php'; ?>