<?php
session_start();
require_once '../config/db.php';
require_once 'includes/admin-auth.php';

// Pagination settings
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Status filter
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
$whereClause = "";
if ($statusFilter !== 'all') {
    $whereClause = "WHERE r.status = '$statusFilter'";
}

// Count total requests
$countQuery = "SELECT COUNT(*) as total FROM student_requests r $whereClause";
$countResult = $conn->query($countQuery);
$totalRequests = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRequests / $limit);

// Get requests
$query = "
    SELECT r.request_id, r.status, r.requested_at, r.updated_at,
           s.user_id as student_id, s.reg_number as student_reg, s.email as student_email,
           t.user_id as tutor_id, t.reg_number as tutor_reg, t.email as tutor_email,
           sub.subject_id, sub.subject_name
    FROM student_requests r
    JOIN users s ON r.student_id = s.user_id
    JOIN users t ON r.tutor_id = t.user_id
    JOIN subjects sub ON r.subject_id = sub.subject_id
    $whereClause
    ORDER BY 
        CASE 
            WHEN r.status = 'pending' THEN 1
            WHEN r.status = 'accepted' THEN 2
            WHEN r.status = 'rejected' THEN 3
        END,
        r.requested_at DESC
    LIMIT $limit OFFSET $offset
";
$result = $conn->query($query);

require_once 'includes/admin-header.php';
?>

<main class="admin-main">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Student Tutoring Requests</h1>
            <div class="filter-section">
                <form action="" method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="status">Filter by Status:</label>
                        <select id="status" name="status" onchange="this.form.submit()">
                            <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Requests</option>
                            <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="accepted" <?php echo $statusFilter === 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                            <option value="rejected" <?php echo $statusFilter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
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
                                <th>ID</th>
                                <th>Student</th>
                                <th>Tutor</th>
                                <th>Subject</th>
                                <th>Requested</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['request_id']; ?></td>
                                        <td>
                                            <a href="#" class="user-link" data-toggle="tooltip" title="<?php echo $row['student_email']; ?>">
                                                <?php echo $row['student_reg']; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="#" class="user-link" data-toggle="tooltip" title="<?php echo $row['tutor_email']; ?>">
                                                <?php echo $row['tutor_reg']; ?>
                                            </a>
                                        </td>
                                        <td><?php echo $row['subject_name']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($row['requested_at'])); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo $row['updated_at'] ? date('M d, Y', strtotime($row['updated_at'])) : 'Not updated'; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No requests found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $statusFilter; ?>" class="page-link">
                                <i class="fas fa-angle-left"></i> Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&status=<?php echo $statusFilter; ?>" 
                               class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $statusFilter; ?>" class="page-link">
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