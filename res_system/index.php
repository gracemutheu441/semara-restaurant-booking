<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$name = $_SESSION['user_name'];

$customer_reservations = null;
$menu_items = null;

if ($role === 'customer') {
    // 1. Fetch reservations for logged-in guest
    $cust_sql = "SELECT r.reservation_id, t.table_number, r.reservation_date, r.reservation_time, r.status 
                 FROM reservations r
                 JOIN restaurant_tables t ON r.table_id = t.table_id
                 WHERE r.user_id = '$user_id'
                 ORDER BY r.reservation_date DESC, r.reservation_time DESC";
    $customer_reservations = $conn->query($cust_sql);

    // 2. Fetch only available menu items for guest preview
    $menu_sql = "SELECT item_name, price FROM menus WHERE is_available = 1 ORDER BY item_name ASC";
    $menu_items = $conn->query($menu_sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semara Hotel - Premium Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            color: #334155;
        }
        .main-nav {
            background: linear-gradient(90deg, #0f172a 0%, #1e293b 100%) !important;
            padding: 1.25rem 2rem;
            border-bottom: 2px solid #3b82f6;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .profile-gradient-card {
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43 -7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3z'/%3E%3C/g%3E%3C/svg%3E"), linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #ffffff;
            border: none;
            border-radius: 24px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3);
        }
        .profile-gradient-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
        }
        .ui-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 24px;
            padding: 2.25rem;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .ui-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -4px rgba(15, 23, 42, 0.08);
        }
        .booking-item-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #64748b; /* Sleek indicator line */
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            transition: all 0.2s ease;
        }
        .booking-item-card:hover {
            border-color: #cbd5e1;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }
        /* Dynamic Border Accent Matching Booking States */
        .booking-item-card:has(.status-confirmed) { border-left-color: #10b981; }
        .booking-item-card:has(.status-pending) { border-left-color: #f59e0b; }
        .booking-item-card:has(.status-cancelled) { border-left-color: #ef4444; }

        .btn-premium-action {
            background: #ffffff;
            color: #0f172a;
            border: none;
            font-weight: 700;
            border-radius: 14px;
            padding: 0.85rem 2.25rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .btn-premium-action:hover {
            background: #3b82f6;
            color: #ffffff;
            transform: scale(1.03);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
        }
        .status-pill {
            font-size: 0.7rem;
            font-weight: 800;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: inline-block;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }
        .status-confirmed { background: #dcfce7; color: #065f46; border: 1px solid #a7f3d0; }
        .status-pending { background: #fef9c3; color: #78350f; border: 1px solid #fde68a; }
        .status-cancelled { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        
        .section-title {
            font-weight: 800;
            font-size: 1.4rem;
            letter-spacing: -0.5px;
            color: #0f172a;
        }
        .icon-box {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }
        .menu-item-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.15rem;
            transition: all 0.2s ease;
            margin-bottom: 0.85rem;
        }
        .menu-item-box:hover {
            background: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.05);
            transform: translateX(3px); /* Interactive nudge effect */
        }
        .price-tag {
            font-weight: 800;
            color: #0f172a;
            font-size: 1rem;
            background: #f1f5f9;
            padding: 0.4rem 0.8rem;
            border-radius: 10px;
        }
        .compact-footer {
            border-top: 1px solid #cbd5e1;
            padding-top: 2rem;
            margin-top: 4rem;
        }
    </style>
</head>
<body>

    <nav class="navbar main-nav navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold tracking-tight text-white fs-5" href="index.php">🏨 SEMARA HOTEL</a>
            <a href="logout.php" class="btn btn-sm btn-outline-danger px-4 rounded-pill fw-bold small">Log Out</a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row g-4">
            
            <div class="col-12">
                <div class="card profile-gradient-card shadow-sm">
                    <div class="row align-items-center g-3">
                        <div class="col-md-8">
                            <span class="badge bg-blue bg-opacity-20 text-white px-3 py-1.5 rounded-pill fw-bold mb-2 small" style="letter-spacing: 0.5px; font-size: 0.7rem; background: rgba(59, 130, 246, 0.3);">
                                SYSTEM PORTAL // <?php echo strtoupper($role); ?>
                            </span>
                            <h1 class="fw-bold tracking-tight mb-1" style="font-size: 2.2rem; letter-spacing: -0.5px;">Welcome, <?php echo $name; ?></h1>
                            <p class="text-white-50 small mb-0 leading-relaxed">Access and monitor your live dashboard modules at Semara Hotel instantly.</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <?php if ($role === 'staff') { ?>
                                <a href="menu_manage.php" class="btn btn-premium-action me-2 bg-white text-dark shadow-sm">Manage Menu</a>
                                <a href="report.php" class="btn btn-outline-light fw-bold rounded-3 opacity-90 px-3 py-2">Reports</a>
                            <?php } else { ?>
                                <a href="reserve.php" class="btn btn-premium-action text-dark shadow-sm">Book a New Table</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($role === 'customer') { ?>
            
                <div class="col-lg-7">
                    <div class="card ui-card border-0 h-100">
                        <h4 class="section-title mb-1">My Bookings</h4>
                        <p class="text-muted small mb-4">Real-time dynamic status updates of your restaurant tables.</p>
                        
                        <div class="booking-list">
                            <?php if ($customer_reservations && $customer_reservations->num_rows > 0) { 
                                while($res = $customer_reservations->fetch_assoc()) { 
                                    $status = $res['status'];
                                    $status_class = "status-pending";
                                    if ($status === 'Confirmed') $status_class = "status-confirmed";
                                    if ($status === 'Cancelled') $status_class = "status-cancelled";
                                    ?>
                                    
                                    <div class="booking-item-card d-flex flex-column justify-content-between align-items-start p-3">
                                        <div class="d-flex align-items-center mb-2 w-100 justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box me-3">🍽️</div>
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Table <?php echo $res['table_number']; ?></h6>
                                                    <small class="text-secondary font-monospace fw-semibold" style="font-size: 0.75rem;">ID: #<?php echo $res['reservation_id']; ?></small>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="status-pill <?php echo $status_class; ?>">
                                                    <?php echo ($status === 'Pending') ? 'Pending' : $status; ?>
                                                </span>
                                            </div>
                                        </div>
                                        <hr class="w-100 my-2 opacity-5">
                                        <div class="w-100 d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-slate small text-primary"><?php echo date("M d, Y", strtotime($res['reservation_date'])); ?></span>
                                            <span class="text-muted small fw-medium">🕒 Time Slot: <span class="text-dark fw-bold"><?php echo $res['reservation_time']; ?></span></span>
                                        </div>
                                    </div>
                                    
                                <?php } 
                            } else { ?>
                                <div class="text-center py-5 bg-light rounded-4">
                                    <p class="text-muted mb-0 small fw-medium">No restaurant reservations logged yet.</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card ui-card border-0 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="section-title mb-1">Digital Menu</h4>
                                <p class="text-muted small mb-0">Available options today</p>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1.5 small fw-bold" style="font-size: 0.65rem; border: 1px solid rgba(16,185,129,0.2);">✨ LIVE</span>
                        </div>

                        <div class="menu-list overflow-auto pe-1" style="max-height: 420px;">
                            <?php if ($menu_items && $menu_items->num_rows > 0) { 
                                while($item = $menu_items->fetch_assoc()) { ?>
                                    <div class="menu-item-box d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-box me-3 bg-white border" style="font-size: 1.1rem;">🍲</div>
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;"><?php echo $item['item_name']; ?></h6>
                                                <small class="text-muted" style="font-size: 0.75rem;">Fresh Order</small>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="price-tag">
                                                KES <?php echo number_format($item['price'], 0); ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php } 
                            } else { ?>
                                <div class="text-center py-4 bg-light rounded-4">
                                    <p class="text-muted mb-0 small">The digital menu is currently empty.</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="col-12 compact-footer text-center">
                    <p class="text-secondary small mb-2">
                        <strong>Need Support with a Booking?</strong> Connect with our management desk for modifications or cancellations.
                    </p>
                    <div class="d-flex justify-content-center gap-4 text-muted small" style="font-size: 0.85rem;">
                        <span>📞 <strong class="text-dark">Call Desk:</strong> +254 712 345678</span>
                        <span class="text-muted opacity-50">|</span>
                        <span>✉️ <strong class="text-dark">Email Support:</strong> care@semara.com</span>
                    </div>
                </div>
                
            <?php } ?>

        </div>
    </div>

</body>
</html>